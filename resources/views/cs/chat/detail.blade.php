<x-layouts.app>
    
    <style>
        .chat-container {
            height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            flex-shrink: 0;
        }
        .chat-body {
            flex: 1;
            overflow-y: auto;
            background: #f4f6f9;
        }
        .chat-footer {
            flex-shrink: 0;
        }
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 85%;
            }
        }
    </style>

    <section class="content">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-12">
                    <div class="card chat-container border-0">
                        <!-- Header -->
                        <div class="card-header chat-header d-flex align-items-center border-bottom-0">
                            <a href="{{ route('cs.chat.index') }}" class="btn btn-light btn-sm me-3" title="Kembali ke daftar chat">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <img src="{{ $chatSesi['avatar'] }}" class="rounded-circle me-3" style="width:45px;height:45px;object-fit:cover;">
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $chatSesi['member'] }}</h5>
                                <small class="d-flex align-items-center mt-1">
                                    <span class="badge bg-warning text-dark me-2">
                                        {{ $chatSesi['status'] }}
                                    </span>
                                    <span class="text-white-50">Online</span>
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Info Member</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-flag me-2"></i>Laporkan</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger" id="btn-close-session">
                                            <i class="fas fa-times me-2"></i>Tutup Sesi
                                        </button>
                                    </li>
                                    <script>
                                    // ...existing code...

                                    document.addEventListener('DOMContentLoaded', function() {
                                        // ...existing code...

                                        // Tutup sesi
                                        const btnCloseSession = document.getElementById('btn-close-session');
                                        if (btnCloseSession) {
                                            btnCloseSession.addEventListener('click', function() {
                                                if (confirm('Tutup sesi chat ini?')) {
                                                    fetch("{{ route('cs.chat.close', $chatSesi['id']) }}", {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            'Accept': 'application/json'
                                                        },
                                                        body: JSON.stringify({})
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            alert('Sesi berhasil ditutup!');
                                                            location.reload();
                                                        } else {
                                                            alert('Gagal menutup sesi!');
                                                        }
                                                    })
                                                    .catch(() => alert('Gagal menutup sesi!'));
                                                }
                                            });
                                        }
                                    });
                                    </script>
                                </ul>
                            </div>
                        </div>

                        <!-- Chat Body -->
                        <div class="card-body chat-body p-3" id="chat-body">
                            <div class="d-flex flex-column gap-3" id="chat-messages">
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
                                                        {{-- <a href="{{ asset('storage/chat/'.$msg) }}" target="_blank">
                                                            <img src="{{ asset('storage/chat/'.$msg) }}" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                                                        </a> --}}
                                                        <a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXjqXNvuBWTf9B77ZZy4PLWlkAzGQMoXgrow&s" target="_blank">
                                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXjqXNvuBWTf9B77ZZy4PLWlkAzGQMoXgrow&s" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                                                        </a>
                                                    @else
                                                        {{-- {{ $pesan['message'] }} --}}
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
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer chat-footer bg-light border-top">
                            <form id="chat-form" autocomplete="off" onsubmit="return false;">
                                <div class="row g-2 align-items-center">
                                    <div class="col">
                                        <div class="input-group">
                                            <input type="text" class="form-control border-end-0" placeholder="Ketik pesan..." id="chat-input">
                                            <button class="btn btn-outline-secondary" type="button" tabindex="-1" disabled>
                                                <i class="fas fa-paperclip"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary px-3" type="submit" id="send-btn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <small class="text-muted">Quick Reply:</small>
                                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reply">Terima kasih</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reply">Saya bantu cek</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reply">Tunggu sebentar</button>
                                            <button type="button" class="btn btn-outline-warning btn-sm quick-reply">Mohon berikan rating pelayanan kami dengan membalas angka 1-10</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Auto scroll to bottom
        function scrollToBottom() {
            const chatBody = document.querySelector('.chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();

            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatMessages = document.getElementById('chat-messages');
            const quickReplyBtns = document.querySelectorAll('.quick-reply');
            const sendBtn = document.getElementById('send-btn');

            // Kirim pesan via AJAX
            function sendMessage() {
                const msg = chatInput.value.trim();
                if(msg.length === 0) return;
                sendBtn.disabled = true;
                fetch("{{ route('cs.chat.send', $chatSesi['id']) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: msg })
                })
                .then(res => res.json())
                .then(data => {
                    chatInput.value = '';
                    refreshMessages();
                })
                .catch(() => { alert('Gagal mengirim pesan'); })
                .finally(() => { sendBtn.disabled = false; });
            }

            // Submit form
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });

            // Enter key
            chatInput.addEventListener('keydown', function(e) {
                if(e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });


            // Quick reply
            quickReplyBtns.forEach(function(btn){
                btn.addEventListener('click', function(){
                    chatInput.value = btn.textContent;
                    chatInput.focus();
                });
            });

            // Tidak ada quick rating di CS, hanya ajakan ke member

            // Polling pesan terbaru
            function refreshMessages() {
                fetch("{{ route('cs.chat.detail', $chatSesi['id']) }}?ajax=1")
                    .then(res => res.text())
                    .then(html => {
                        chatMessages.innerHTML = html;
                        scrollToBottom();
                    });
            }

            setInterval(refreshMessages, 3000);
        });
    </script>
</x-layouts.app>