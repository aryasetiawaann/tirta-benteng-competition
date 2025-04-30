<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Pembayaran;
use App\Models\Peserta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Midtrans\Notification;
use Midtrans\Snap;
use Illuminate\Support\Str;

class PesertaController extends Controller
{
    public function create(Request $request){

        $data = [ "acara_id"=> $request->acara,
            "peserta_user_id" => auth()->user()->id,
            "atlet_id"=> $request->atlet,
        ];
        
        Peserta::create($data);
        
        return redirect()->back()->with('success','Atlet berhasil ditambahkan');
    }

    public function tagihan(){

        $pesertas = Peserta::with('getAcara', 'getAtlet')
        ->where([
            ['peserta_user_id', auth()->user()->id],
            ['status_pembayaran', 'Menunggu']
        ])
        ->get();

        return view('pages.dashboard-tagihan', compact('pesertas'));
    }

    public function generateSnapToken(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = true;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $user = auth()->user();
        $pesertaIds = $request->peserta_ids;
        $pesertas = Peserta::whereIn('id', $pesertaIds)->get();
        $itemDetails = [];
        $totalHarga = 0;
        $order_id = 'SC-' . date('Ymd') . '-' . strtoupper(Str::random(5)) . $user->id;

        if ($pesertas->isEmpty()) {
            return response()->json(['error' => 'Tidak ada peserta yang dipilih'], 400);
        }

        // Ini untuk menampilkan peserta - acara yang dibayar
        foreach ($pesertas as $peserta) {
            $itemDetails[] = [
                'id'       => $peserta->id,
                'price'    => $peserta->getAcara->harga ?? 0,
                'quantity' => 1,
                'name'     => "{$peserta->getAtlet->name} - {$peserta->getAcara->nomor_lomba} - {$peserta->getAcara->nama}",
            ];
            $totalHarga += $peserta->getAcara->harga ?? 0;
        }

        // Untuk Perpajakan
        $taxPercentage = 6; // ubah nilai ini untuk mengganti persentase
        $totaltax = $totalHarga * ($taxPercentage / 100);

        $itemDetails[] = [
            'id'        => 'tax',
            'price'     => round($totaltax),
            'quantity'  => 1,  
            'name'      => "Pajak Layanan ({$taxPercentage}%)",
        ];

        $totalHarga += $totaltax;

        // Simpan pembayaran ke database
        $pembayaran = Pembayaran::create([
            'user_id'                 => $user->id,
            'midtrans_order_id'       => $order_id,
            'snap_token'              => null,
            'metode_pembayaran'       => 'none', 
            'total_harga'             => $totalHarga,
        ]);

        if (!$pembayaran) {
            return response()->json(['error' => 'Gagal menyimpan pembayaran'], status: 500);
        }

        // Update peserta dengan pembayaran_id
        Peserta::whereIn('id', $pesertaIds)->update(['pembayaran_id' => $pembayaran->id]);
        
        $transactionDetails = [
            'order_id'     => $order_id,
            'gross_amount' => $totalHarga,
        ];
    
        // Data pelanggan (opsional)
        $customerDetails = [
            'first_name' => $user->name,
            'phone'      => $user->phone ?? null,
            'email'      => $user->email,
        ];
    
        // Data Snap Token
        $transaction = [
            'transaction_details' => $transactionDetails,
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
        ];
        
        try {
            $snapToken = Snap::getSnapToken($transaction);

            // Update Snap Token di database
            $pembayaran->update(['snap_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mendapatkan Snap Token'], 500);
        }
    }


    public function paymentCallback(Request $request)
    {
        $server_key = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $server_key);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $paymentMethod = $request->payment_type;
        $fraudStatus = $request->fraud_status;
        $transactionId = $request->transaction_id;
        $orderId = $request->order_id;

        $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Perbarui status pembayaran
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement': // Pembayaran berhasil
                $pembayaran->status = 'Berhasil';
                Peserta::where('pembayaran_id', $pembayaran->id)->update(['status_pembayaran' => 'Selesai']);
                break;

            case 'pending': // Pembayaran masih menunggu
                $pembayaran->status = 'Menunggu';
                break;

            case 'deny':
            case 'cancel': // Jika gagal atau dibatalkan, biarkan peserta tetap "Menunggu"
                $pembayaran->status = 'Gagal';
                break;
            case 'expire':
                $pembayaran->status = 'Kedaluarsa';
                break;
        }

        $pembayaran->midtrans_transaction_id = $transactionId;
        $pembayaran->metode_pembayaran = $paymentMethod;
        $pembayaran->midtrans_response = $fraudStatus;
        $pembayaran->save();
    }



    public function tagihanRiwayat(){

        $pembayaran = Pembayaran::with('pendaftaran.getAtlet', 'pendaftaran.getAcara')
        ->where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')->get();

        $pembayaran->each(function ($bayar) {
            $bayar->groupedPeserta = $bayar->pendaftaran->groupBy(function($peserta) {
                return $peserta->getAtlet->name;
            });
        });

        return view('pages.dashboard-lunas', compact('pembayaran'));
    }


    public function destroy($id){
        Peserta::find($id)->delete();
        return redirect()->back()->with('success','Tagihan berhasil dihapus');
    }
}
