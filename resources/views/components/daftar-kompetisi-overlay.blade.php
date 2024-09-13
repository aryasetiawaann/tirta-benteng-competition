<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Daftar Atlet</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{route('dashboard.acara.daftar')}}">
                @csrf
                <label for="atlet">Pilih Atlet</label>
                <select id="atlet" name="atlet" style="cursor: pointer">
                    @if ($atlets->count() > 0)
                        @foreach ($atlets as $key => $atlet)
                            @if ($key == 0)
                            <option value="{{ $atlet->id }}" selected>{{ $atlet->name }}</option>
                            @else
                            <option value="{{ $atlet->id }}">{{ $atlet->name }}</option>
                            @endif
                        @endforeach
                    @else
                        <option value="" selected>Belum ada atlet memenuhi kriteria</option>
                    @endif
                </select>
                <input type="hidden" name="acara" id="acara" value="{{ $acara->id}}">
                <input type="hidden" name="harga" id="harga" value="{{ $acara->harga}}">
                @if ($atlets->count() > 0)
                <div class="flex center">
                    <button class="submit-button" type="submit">Kirim</button>
                </div>
                @endif
            </form>
        </section>
    </div>
</div>