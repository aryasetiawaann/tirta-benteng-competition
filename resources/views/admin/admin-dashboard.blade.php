@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">

    <div class="admin-container">
        <div class="card100">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Welcome! {{ auth()->user()->name }}</h2>
                </header>
                <section>
                    <p>{{ \Carbon\Carbon::now()->format('l') }}, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                </section>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Download Buku Acara</h2>
                </header>
                <section>
                    <div>
                        <h4 style="margin-bottom: 10px">EXCEL</h4>
                        <button class="button-blue"><i class='bx bx-download'></i></button>
                    </div>
                </section>
            </div>
        </div>
        <div class="upload card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Upload Hasil Kompetisi</h2>
                </header>
                <section>
                    <div>
                        <h4 style="margin-bottom: 10px">PDF</h4>
                        <button class="button-blue"><i class='bx bx-upload'></i></button>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
</div>
@endsection