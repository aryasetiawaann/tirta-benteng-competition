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
                {{-- pake apa ini?, konek ke database gmn --}}
                <select id="atlet" name="atlet">
                    @foreach ( $atlets as $atlet)
                    <option value="{{ $atlet->id }}">{{ $atlet->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="acara" id="acara" value="{{ $acara->id}}">
                <div class="flex center">
                    <button class="w50" type="submit">Kirim</button>
                </div>
            </form>
        </section>
    </div>
</div>