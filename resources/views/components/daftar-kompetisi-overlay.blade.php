<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Daftar Atlet</h2>
            <p>Silahkan mengisi dokumen di <a href={{route('dashboard.atlet.index')}}>halaman atlet</a> jika nama atlet tidak tersedia</p>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form id="formSubmit" class="atlet" method="POST" action="{{route('dashboard.acara.daftar')}}">
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
                <input type="hidden" name="kompetisi" id="kompetisi" value="{{ $acara->kompetisi->id}}">
                <input type="hidden" name="harga" id="harga" value="{{ $acara->harga}}">
                @if ($atlets->count() > 0)
                <div class="flex center">
                    <button id="submitButton" class="submit-button" type="submit">Kirim</button>
                </div>
                @endif
            </form>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formSubmit'); // pastikan ini mengacu pada form yang tepat
        const button = document.getElementById('submitButton');

        form.addEventListener('submit', function () {
            button.disabled = true;
            button.style.backgroundColor = 'grey';
            button.style.cursor = 'not-allowed';
            button.textContent = 'Mengirim...'; // opsional: ubah teks saat loading
        });
    });
</script>