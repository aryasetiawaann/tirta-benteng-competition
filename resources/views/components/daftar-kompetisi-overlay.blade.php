<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container">
        <header class="flex divider">
            <h2>Daftar Atlet</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor, blanditiis. Nostrum voluptatum error, quisquam consequatur ipsa, doloribus alias cupiditate qui voluptatem distinctio animi. Beatae repellat ut, quibusdam hic rem consequatur!</p>
            <form class="atlet" method="POST" action="{{route('dashboard.acara.daftar')}}">
                @csrf
                <label for="atlet">Pilih Atlet</label>
                <select id="atlet" name="atlet">
                    @if ($atlets->count() > 0)
                        @foreach ($atlets as $atlet)
                            <option value="{{ $atlet->id }}">{{ $atlet->name }}</option>
                        @endforeach
                    @else
                        <option value="" disabled>Belum ada data</option>
                    @endif
                </select>
                <input type="hidden" name="acara" id="acara" value="{{ $acara->id}}">
                <input type="hidden" name="harga" id="harga" value="{{ $acara->harga}}">
                <div class="flex center">
                    <button class="w50" type="submit">Kirim</button>
                </div>
            </form>
        </section>
    </div>
</div>