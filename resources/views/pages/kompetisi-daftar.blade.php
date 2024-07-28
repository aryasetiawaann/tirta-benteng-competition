@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kompetisi')

@section('style')
<style>
    p {
        margin-bottom: 5px
    }

    .grid {
        grid-template-columns: 1fr 1fr 1fr;
    }

    @media screen and (max-width: 1024px) {
        .grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media screen and (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
            <div class="card-left">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <h1>Agung Tirtayasa Competition ABCD</h1>
                </div>
            </div>
            <div class="card-right">
            </div>
            </div>
        </div>
        <div class="bottom-container grid">
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>120 - 50m Gaya Dada Putra</h2>
                    <a href="{{ route('kompetisi.daftar2') }}"><button>Daftar</button></a>
                </header>
                <div>
                    <h3 class="mtopbot">
                        Status : <span class="status buka smaller">Pendaftaran Buka</span>
                    </h3>
                    <h3 class="mtopbot">
                        Harga : <span class="status harga smaller">Rp. 125.000,00</span>
                    </h3>
                    <p>Kuota : 0 / 100</p>
                    <p>Min Umur : 17</p>
                    <p>Max Umur : 25</p>
                </div>
            </section>
        </div>
    </div>
@endsection