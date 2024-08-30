<?php

namespace App\Http\Controllers;

use App\Models\LogoKompetisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class LogoKompetisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $request->validate([
            'logo.*' => 'required|image|mimes:png,jpg,jpeg,webp'
        ]);

        $imageData = [];
        if($files = $request->file('logo')){

            foreach($files as $key => $file){

                $filename = $key. '-' . time() . '.' . $file->extension();


                $file->move(public_path('assets/img'), $filename);

                $imageData[] = [
                    'kompetisi_id' => $request->kompetisi,
                    'name' => 'assets/img/' . $filename,
                ];
            }

        }

        LogoKompetisi::insert($imageData);

        return redirect()->route('dashboard.admin.kompetisi')->with('success', 'Logo berhasil dimasukkan');

    }

    public function destroy($id)
    {
        $logo = LogoKompetisi::find($id);

        if ($logo->name && File::exists(public_path($logo->name))) {
            File::delete(public_path($logo->name));
            $logo->delete();
        }

        return redirect()->route('admin.dashboard')->with('success','Logo profil berhasil dihapus');
    }
}
