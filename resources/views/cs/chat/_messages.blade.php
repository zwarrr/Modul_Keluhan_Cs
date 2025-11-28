@foreach($pesans as $pesan)
    <div class="d-flex mb-1 {{ $pesan['role'] === 'cs' ? 'justify-content-end' : 'justify-content-start' }}">
        @if($pesan['role'] === 'member')
            <div class="me-2 align-self-end">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($pesan['sender']) }}&background=6c757d&color=fff" 
                     class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
            </div>
        @endif
        <div class="message-bubble">
            <div class="p-3 rounded {{ $pesan['role'] === 'cs' ? 'bg-primary text-white' : 'bg-white border' }}">
                @if($pesan['role'] === 'member')
                    <small class="text-muted fw-bold">{{ $pesan['sender'] }}</small>
                @endif
                <div class="mt-1">
                    @php
                        $imgExt = ['jpg','jpeg','png','gif','webp','bmp'];
                        $msg = $pesan['message'];
                        $isImg = false;
                        if (is_string($msg)) {
                            $ext = strtolower(pathinfo($msg, PATHINFO_EXTENSION));
                            $isImg = in_array($ext, $imgExt);
                        }
                    @endphp
                    @if($isImg)
                        {{-- <a href="{{ asset('storage/'.$msg) }}" target="_blank">
                            <img src="{{ asset('storage/'.$msg) }}" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                        </a> --}}
                        <a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXjqXNvuBWTf9B77ZZy4PLWlkAzGQMoXgrow&s" target="_blank">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXjqXNvuBWTf9B77ZZy4PLWlkAzGQMoXgrow&s" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                        </a>
                    @else
                        {{ $pesan['message'] }}
                    @endif
                </div>
                <div class="text-end small {{ $pesan['role'] === 'cs' ? 'text-white-50' : 'text-muted' }} mt-2">
                    {{ $pesan['time'] }}
                </div>
            </div>
        </div>
        @if($pesan['role'] === 'cs')
            <div class="ms-2 align-self-end">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($pesan['sender']) }}&background=007bff&color=fff" 
                     class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
            </div>
        @endif
    </div>
@endforeach