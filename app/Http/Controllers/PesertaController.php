<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Peserta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function create(Request $request){

        $data = [ "acara_id"=> $request->acara,
            "atlet_id"=> $request->atlet,
        ];
        
        $peserta = Peserta::create($data);
        
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = true;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
        
        $params = array(
            'transaction_details' => array(
                'order_id' => $peserta->id,
                'gross_amount' => $request->harga,
            ),
            'customer_details' => array(
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ),
        );
        
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $peserta->snap_token = $snapToken;
        $peserta->save();
        
        
        return redirect()->back()->with('success','Atlet berhasil ditambahkan');
    }

    public function tagihan(){

        $currentDate = Carbon::now();

        $acaraOpen = Acara::whereHas('kompetisi', function ($query) use ($currentDate) {
            $query->where('tutup_pendaftaran', '>=', $currentDate);
        })->pluck('id');

        $atlets = Atlet::where('user_id', auth()->user()->id)
            ->whereHas('acaraBayarMenunggu', function ($query) use ($acaraOpen) {
                $query->whereIn('acara_id', $acaraOpen);
            })
            ->with(['acaraBayarMenunggu' => function ($query) use ($acaraOpen) {
                $query->whereIn('acara_id', $acaraOpen);
            }])
            ->get()
            ->sortByDesc('created_at');

        $totalHarga = 0;
        
        foreach ($atlets as $atlet) {
            foreach ($atlet->acara as $acara) {
                $totalHarga += $acara->harga;
            }
        }

        if($totalHarga > 0){
            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = true;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;
            
            $params = array(
                'transaction_details' => array(
                    'order_id' => rand(),
                    'gross_amount' => $totalHarga,
                ),
                'customer_details' => array(
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ),
            );
            
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        }else{

            $snapToken = null;
        }

        return view('pages.dashboard-tagihan', compact('atlets', 'totalHarga', 'snapToken'));
    }

    public function pembayaranSukses($id){

        Peserta::find($id)->update(['status_pembayaran' => 'Selesai', 'waktu_pembayaran' => now()]);

        return redirect('/dashboard/riwayat-pembayaran');
    }

    public function tagihanBayarSemua() {

        $atletIds = Atlet::where('user_id', auth()->user()->id)->pluck('id');

        Peserta::whereIn('atlet_id', $atletIds)
        ->where('status_pembayaran', 'Menunggu')
        ->update(['status_pembayaran' => 'Selesai', 'waktu_pembayaran' => now()]);

        return redirect('/dashboard/riwayat-pembayaran');        
    }   

    // public function tagihanCallback(Request $request){
    //     $serverKey = config('midtrans.server_key');
    //     $hashed = hash('sha512', $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
    //     if($hashed == $request->signature_key){
    //         if($request->transaction_status == 'expire' || $request->transaction_status == 'cancel'){
    //             return redirect('/dashboard/tagihan');
    //         }
    //     }
    // }

    public function tagihanRiwayat(){
        $atlets = Atlet::whereHas('acaraBayarSelesai')->with('acara')->where('user_id', auth()->user()->id)->get()->sortByDesc('created_at');

        return view('pages.dashboard-lunas', compact('atlets'));
    }


    public function destroy($id){
        Peserta::find($id)->delete();
        return redirect()->back()->with('success','Tagihan berhasil dihapus');
    }
}
