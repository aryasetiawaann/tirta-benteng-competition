<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Upload Hasil Kompetisi</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{ route('dashboard.admin.file.add') }}" enctype="multipart/form-data">
                @csrf

                <label for="kompetisi">Pilih Kompetisi</label>
                <select name="kompetisi" id="kompetisi">
                    @if ($kompetisis->count() > 0 )
                        @foreach ($kompetisis as $key => $kompetisi)
                            @if ($key == 0)
                            <option value="{{ $kompetisi->id }}" selected>{{ $kompetisi->nama }}</option>
                            @else
                            <option value="{{ $kompetisi->id }}">{{ $kompetisi->nama }}</option>
                            @endif
                    @endforeach    
                    @else
                        <option value="" selected>Tidak ada kompetisi</option>                  
                    @endif
                </select>

                <label for="file">Masukan File</label>
                <input type="file" name="file" id="file" accept=".pdf">
                
                @if ($kompetisis->count() > 0)
                <div class="flex center">
                    <button class="w50" type="submit">Simpan</button>
                </div>
                @endif
            </form>
        </section>
    </div>
</div>