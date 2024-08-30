@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">
    <div class="all-container all-card w100">
        <header class="flex divider">
            <h2>Edit File Hasil {{$kompetisi->nama}}</h2>
        </header>
        <section>
            <form class="tambah-container" method="POST" action="{{ route('dashboard.admin.file.update', $kompetisi->id) }}">
                @csrf
                @method('put')

                <label for="file">Masukan File</label>
                <input type="file" name="file" accept=".pdf" id="file" value="{{ $kompetisi->file_hasil }}">
                <input type="hidden" name="id" value="{{ $kompetisi->id }}">

                <div class="flex center">   
                    <button type="submit" class="w50">Simpan</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection