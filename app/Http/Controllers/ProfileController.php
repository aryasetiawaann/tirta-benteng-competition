<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\User;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        return view('pages.profile-user');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            "club" => $request->club,
            "phone" => $request->phone,
            "foto" => $request->foto,
        ];

        $validation = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'required|string|regex:/^(0)[8][1-9][0-9]{6,11}$/| max:15',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa string.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa string.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Nomor telepon harus berupa string.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 15 karakter.',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $user = User::find(Auth::user()->id);

        // Mengganti foto jika ada
        if ($request->hasFile('foto')) {
            if ($user->foto && File::exists(public_path($user->foto))) {
                File::delete(public_path($user->foto));
            }

            $imageName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('assets/img'), $imageName);
            $user->foto = 'assets/img/' . $imageName;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->club = $data['club'];
        $user->phone = $data['phone'];
        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbaharui');
    }


    public function deletePhoto()
    {
        $user = User::find(Auth::user()->id);

        if ($user->foto && File::exists(public_path($user->foto))) {
            File::delete(public_path($user->foto));
            $user->foto = null;
            $user->save();
        }

        return redirect()->back()->with('success','Foto profil berhasil dihapus');
    }
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function adminEdit()
    {
        return view('admin.admin-profile');
    }

    /**
     * Update the user's profile information.
     */
    public function adminUpdate(Request $request)
    {
        $data = [ "name"=> $request->name,
        "email"=> $request->email,
        "club"=> $request->club,
        "foto"=> $request->foto,
        ];

        $validation = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $user = User::find(Auth::user()->id);

        if ($request->hasFile('foto')) {

            if ($user->foto && File::exists(public_path($user->foto))) {
                File::delete(public_path($user->foto));
            }

            $imageName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('assets/img'), $imageName);
            $user->foto = 'assets/img/' . $imageName;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->club = $data['club'];
        $user->save();

        return redirect()->back()->with('success','Profil berhasil diperbaharui');

    }

    public function adminDeletePhoto()
    {
        $user = User::find(Auth::user()->id);

        if ($user->foto && File::exists(public_path($user->foto))) {
            File::delete(public_path($user->foto));
            $user->foto = null;
            $user->save();
        }

        return redirect()->back()->with('success','Foto profil berhasil dihapus');
    }
    /**
     * Delete the user's account.
     */
    public function adminDestroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
