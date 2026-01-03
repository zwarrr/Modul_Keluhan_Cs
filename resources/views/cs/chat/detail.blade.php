<x-layouts.app>
    
    <style>
        .chat-container {
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
            margin-top: 20px;
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
        .typing-indicator {
            display: none;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
        }
        .typing-indicator.active {
            display: flex;
        }
        .d-flex.mb-1 .align-self-end {
            display: none !important;
        }
        .typing-dot {
            width: 8px;
            height: 8px;
            margin: 0 2px;
            background: #6c757d;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.5;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
    </style>

    <!-- Include Handle Session Modal (CS only). Admin is view-only. -->
    @if(($currentUserRole ?? '') === 'cs')
        @include('cs.sections.modal_information.handle_session_modal')
    @endif

    <section class="content">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-12">
                    <div class="card chat-container border-0">
                        <!-- Header -->
                        <div class="card-header chat-header d-flex align-items-center border-bottom-0">
                            <a href="{{ ($currentUserRole ?? '') === 'admin' ? route('admin.sesi-chat.index') : route('cs.chat.index', ['filter' => 'my-chats']) }}" class="btn btn-light btn-sm me-3" title="Kembali ke Chat Saya">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <img src="{{ $chatSesi['avatar'] }}" class="rounded-circle me-3" style="width:45px;height:45px;object-fit:cover;">
                            <div class="flex-grow-1">
                                <h5 class="mb-0">
                                    {{ $chatSesi['member'] }}
                                    @if(($currentUserRole ?? '') === 'admin')
                                        <span class="badge bg-info text-white ms-2"><i class="fas fa-eye"></i> View Only</span>
                                    @endif
                                </h5>
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
                                    @if(($currentUserRole ?? '') === 'cs')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger" id="btn-close-session">
                                                <i class="fas fa-times me-2"></i>Tutup Sesi
                                            </button>
                                        </li>
                                    @endif
                                    <script>
                                    // ...existing code...

                                    document.addEventListener('DOMContentLoaded', function() {
                                        // ...existing code...

                                        // Tutup sesi
                                        const btnCloseSession = document.getElementById('btn-close-session');
                                        if (btnCloseSession) {
                                            btnCloseSession.addEventListener('click', function() {
                                                if (confirm('Tutup sesi chat ini?')) {
                                                    fetch("{{ route($routePrefix . '.close', $chatSesi['id']) }}", {
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
                                    @if(in_array(($pesan['type'] ?? 'message'), ['session_badge','date_badge']))
                                        <div class="text-center my-2">
                                            <span class="badge bg-light text-dark border">{{ $pesan['label'] ?? 'Sesi' }}</span>
                                        </div>
                                        @continue
                                    @endif
                                    <div class="d-flex mb-1 {{ $pesan['role'] === 'cs' ? 'justify-content-end' : 'justify-content-start' }}" @if($pesan['role'] === 'cs' && isset($pesan['id'])) data-message-id="{{ $pesan['id'] }}" @endif>
                                        @if($pesan['role'] === 'member')
                                            <div class="me-2 align-self-end" style="display:none;">
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
                                                        $isImg = false;
                                                        if ($pesan['file_path']) {
                                                            $ext = strtolower(pathinfo($pesan['file_path'], PATHINFO_EXTENSION));
                                                            $isImg = in_array($ext, $imgExt);
                                                        }
                                                    @endphp
                                                    @if($pesan['file_path'])
                                                        @if($isImg)
                                                            <a href="{{ $pesan['file_path'] }}" target="_blank">
                                                                <img src="{{ $pesan['file_path'] }}" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                                                            </a>
                                                        @else
                                                            @php $fileName = basename($pesan['file_path']); @endphp
                                                            <a href="{{ $pesan['file_path'] }}" target="_blank" class="text-decoration-none">
                                                                <i class="fas fa-file"></i> {{ $fileName }}
                                                            </a>
                                                        @endif
                                                        @if($pesan['message'])
                                                            <br>
                                                        @endif
                                                    @endif
                                                    @if($pesan['message'])
                                                        {{ $pesan['message'] }}
                                                    @endif
                                                </div>
                                                <div class="text-end small {{ $pesan['role'] === 'cs' ? 'text-white-50' : 'text-muted' }} mt-2">
                                                    {{ $pesan['time'] }}
                                                    @if($pesan['role'] === 'cs')
                                                        @if(isset($pesan['is_read']) && $pesan['is_read'])
                                                            <i class="fas fa-check-double ms-1 read-check" style="color: #34b7f1;"></i>
                                                        @else
                                                            <i class="fas fa-check-double ms-1 read-check" style="color: rgba(255,255,255,0.5);"></i>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($pesan['role'] === 'cs')
                                            <div class="ms-2 align-self-end" style="display:none;">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($pesan['sender']) }}&background=007bff&color=fff" 
                                                     class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                <!-- Typing Indicator -->
                                <div id="typing-indicator" class="typing-indicator">
                                    <div class="me-2">
                                        <img src="https://ui-avatars.com/api/?name=Member&background=6c757d&color=fff" 
                                             class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                    </div>
                                    <div class="bg-white border p-2 rounded d-flex">
                                        <div class="typing-dot"></div>
                                        <div class="typing-dot"></div>
                                        <div class="typing-dot"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer chat-footer bg-light border-top">
                            @if(auth()->user()->role === 'admin')
                                <!-- Admin View Only Mode -->
                                <div class="alert alert-info mb-0" role="alert">
                                    <i class="fas fa-info-circle"></i> Anda dalam mode <strong>View Only</strong>. Hanya CS yang dapat mengirim pesan dan menutup sesi.
                                </div>
                            @else
                                <!-- CS Active Mode -->
                                <form id="chat-form" autocomplete="off" onsubmit="return false;">
                                    <div class="row g-3 align-items-center">
                                        <div class="col">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Ketik pesan..." id="chat-input">
                                                <!-- <button class="btn btn-outline-secondary" type="button" tabindex="-1" disabled style="padding: 0.5rem 1.2rem;">
                                                    <i class="fas fa-paperclip"></i>
                                                </button> -->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-primary" type="submit" id="send-btn" style="padding: 0.5rem 1.5rem;">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex gap-3 flex-wrap align-items-center">
                                                <small class="text-muted" style="margin-right: 15px;">Quick Reply:</small>
                                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" style="margin-right: 10px; margin-bottom: 8px;">Terima kasih</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" style="margin-right: 10px; margin-bottom: 8px;">Saya bantu cek</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" style="margin-right: 10px; margin-bottom: 8px;">Tunggu sebentar</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @vite(['resources/js/app.js'])
    
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
            const typingIndicator = document.getElementById('typing-indicator');
            
            let typingTimeout;

            // Check if required elements exist (may not exist for admin view-only)
            if (!chatForm || !chatInput || !chatMessages) {
                console.warn('Some chat elements not found - possibly in view-only mode');
            }

            // Check if session needs handling (cs_id is null)
            const sessionCsId = {{ $chatSesi['cs_id'] ?? 'null' }};
            const sessionId = {{ $chatSesi['id'] }};

            // Current user info (for de-duplication)
            const currentUserId = {{ $currentUserId ?? 'null' }};
            const currentUserRole = @json($currentUserRole ?? '');
            const isViewOnly = Boolean(@json($isViewOnly ?? false));
            
            // Mark messages as read when CS opens the chat (NOT for admin - view only)
            if (currentUserRole === 'cs') {
                fetch("{{ route($routePrefix . '.markRead', $chatSesi['id']) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log('Messages marked as read:', data);
                    // Notify parent window (index page) to refresh list
                    if (window.opener && !window.opener.closed) {
                        try {
                            window.opener.postMessage({ type: 'refreshChatList' }, window.location.origin);
                        } catch (e) {
                            console.error('Cannot notify opener:', e);
                        }
                    }
                })
                .catch(err => console.error('Error marking messages as read:', err));
            }
            
            // Only show modal for CS role, not for admin (view-only)
            // NOTE: Don't gate by sessionStorage. Session IDs can be reused after migrate:fresh,
            // which would incorrectly suppress the modal for new pending sessions.
            if (!isViewOnly && sessionCsId === null && currentUserRole === 'cs') {
                // Show handle session modal
                const handleModal = document.getElementById('handleSessionModal');
                if (!handleModal) return;
                handleModal.style.display = 'flex';
                
                // Handle cancel
                document.getElementById('cancelHandleBtn').addEventListener('click', function() {
                    window.location.href = (currentUserRole === 'admin')
                      ? "{{ route('admin.sesi-chat.index') }}"
                      : "{{ route('cs.chat.index') }}";
                });
                
                // Handle confirm
                document.getElementById('confirmHandleBtn').addEventListener('click', function() {
                    // Disable button to prevent double click
                    const confirmBtn = document.getElementById('confirmHandleBtn');
                    confirmBtn.disabled = true;
                    confirmBtn.style.opacity = '0.5';
                    confirmBtn.textContent = 'Memproses...';
                    
                    // Assign CS to session and send automatic message
                    fetch("{{ route($routePrefix . '.handle', ':id') }}".replace(':id', sessionId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Handle response:', data);
                        if (data.success) {
                            // Hide modal
                            handleModal.style.display = 'none';
                            
                            // Reload to show the automatic message
                            location.reload();
                        } else {
                            alert(data.message || 'Gagal menangani sesi!');
                            confirmBtn.disabled = false;
                            confirmBtn.style.opacity = '1';
                            confirmBtn.textContent = 'Tangani';
                        }
                    })
                    .catch((error) => {
                        console.error('Handle error:', error);
                        alert('Terjadi kesalahan!');
                        confirmBtn.disabled = false;
                        confirmBtn.style.opacity = '1';
                        confirmBtn.textContent = 'Tangani';
                    });
                });
            }

            // Send typing signal when CS is typing
            if (chatInput) {
                chatInput.addEventListener('input', function() {
                    clearTimeout(typingTimeout);
                    
                    // Send typing signal
                    fetch("{{ route($routePrefix . '.typing', $chatSesi['id']) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    // Clear typing after 3 seconds of no typing
                    typingTimeout = setTimeout(function() {
                        // Typing stopped
                    }, 3000);
                });
            }

            // Kirim pesan via AJAX
            function sendMessage() {
                const msg = chatInput.value.trim();
                if(msg.length === 0) return;
                sendBtn.disabled = true;
                fetch("{{ route($routePrefix . '.send', $chatSesi['id']) }}", {
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
                    // Append own message locally with read status (server broadcasts toOthers)
                    const now = new Date();
                    const hh = String(now.getHours()).padStart(2, '0');
                    const mm = String(now.getMinutes()).padStart(2, '0');
                    const timeText = `${hh}:${mm}`;

                    const messageId = data.message_id || '';
                    const messageHtml = `
                        <div class="d-flex mb-1 justify-content-end" data-message-id="${messageId}">
                            <div class="message-bubble">
                                <div class="p-3 rounded bg-primary text-white">
                                    <div class="mt-1">${msg}</div>
                                    <div class="text-end small text-white-50 mt-2">
                                        ${timeText}
                                        <i class="fas fa-check-double ms-1 read-check" style="color: rgba(255,255,255,0.5);"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                    scrollToBottom();
                })
                .catch(() => { alert('Gagal mengirim pesan'); })
                .finally(() => { sendBtn.disabled = false; });
            }

            // Submit form
            if (chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    sendMessage();
                });
            }

            // Enter key
            if (chatInput) {
                chatInput.addEventListener('keydown', function(e) {
                    if(e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendMessage();
                    }
                });
            }

            // Quick reply
            if (quickReplyBtns && chatInput) {
                quickReplyBtns.forEach(function(btn){
                    btn.addEventListener('click', function(){
                        chatInput.value = btn.textContent;
                        chatInput.focus();
                    });
                });
            }

            // Tidak ada quick rating di CS, hanya ajakan ke member

            // Polling pesan terbaru dan status typing
            function refreshMessages() {
                fetch("{{ route($routePrefix . '.pesanList', $chatSesi['id']) }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Update messages
                            let messagesHtml = '';
                            data.messages.forEach(msg => {
                                const isMember = msg.self === false;
                                const alignment = isMember ? 'justify-content-start' : 'justify-content-end';
                                const bgClass = isMember ? 'bg-white border' : 'bg-primary text-white';
                                const avatar = isMember 
                                    ? 'https://ui-avatars.com/api/?name=' + encodeURIComponent(msg.sender || 'Member') + '&background=6c757d&color=fff'
                                    : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(msg.sender || 'CS') + '&background=007bff&color=fff';
                                
                                let contentHtml = '';
                                if (msg.file_path) {
                                    if (msg.file_type === 'image') {
                                        contentHtml = `<a href="${msg.file_path}" target="_blank">
                                            <img src="${msg.file_path}" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                                        </a>`;
                                    } else {
                                        const fileName = msg.file_path.split('/').pop();
                                        contentHtml = `<a href="${msg.file_path}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-file"></i> ${fileName}
                                        </a>`;
                                    }
                                }
                                if (msg.text) {
                                    contentHtml += (contentHtml ? '<br>' : '') + msg.text;
                                }
                                
                                messagesHtml += `
                                    <div class="d-flex mb-1 ${alignment}">
                                        ${isMember ? `<div class="me-2 align-self-end">
                                            <img src="${avatar}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                        </div>` : ''}
                                        <div class="message-bubble">
                                            <div class="p-3 rounded ${bgClass}">
                                                ${isMember ? `<small class="text-muted fw-bold">${msg.sender || 'Member'}</small>` : ''}
                                                <div class="mt-1">${contentHtml}</div>
                                                <div class="text-end small ${isMember ? 'text-muted' : 'text-white-50'} mt-2">
                                                    ${msg.time}
                                                </div>
                                            </div>
                                        </div>
                                        ${!isMember ? `<div class="ms-2 align-self-end">
                                            <img src="${avatar}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                        </div>` : ''}
                                    </div>
                                `;
                            });
                            
                            chatMessages.innerHTML = messagesHtml;
                            
                            /* DISABLED - Using WebSocket now
                            // Update typing indicator
                            if (data.member_typing) {
                                typingIndicator.classList.add('active');
                            } else {
                                typingIndicator.classList.remove('active');
                            }
                            */
                            
                            scrollToBottom();
                        }
                    })
                    .catch(err => console.error('Error refreshing messages:', err));
            }

            // DISABLED: Polling sudah diganti dengan WebSocket
            // setInterval(refreshMessages, 2000);
            
            // WebSocket listener for real-time updates
            if (window.Echo) {
                const sesiId = {{ $chatSesi['id'] }};
                
                window.Echo.channel('chat.' + sesiId)
                    .listen('.message', (e) => {
                        console.log('New message received:', e);

                        // Prevent duplicate: CS message is already appended locally after send
                        if (currentUserRole !== 'admin' && e.senderRole === 'cs' && currentUserId !== null && String(e.senderId) === String(currentUserId)) {
                            return;
                        }
                        
                        // Hide typing indicator
                        typingIndicator.classList.remove('active');
                        
                        // Add message to chat
                        const isMember = e.senderRole !== 'cs';
                        const alignment = isMember ? 'justify-content-start' : 'justify-content-end';
                        const bgClass = isMember ? 'bg-white border' : 'bg-primary text-white';
                        const avatar = isMember 
                            ? 'https://ui-avatars.com/api/?name=' + encodeURIComponent(e.senderName || 'Member') + '&background=6c757d&color=fff'
                            : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(e.senderName || 'CS') + '&background=007bff&color=fff';
                        
                        let contentHtml = '';
                        if (e.filePath) {
                            if (e.fileType === 'image') {
                                contentHtml = `<a href="${e.filePath}" target="_blank">
                                    <img src="${e.filePath}" alt="gambar" style="max-width:180px;max-height:180px;border-radius:8px;object-fit:cover;">
                                </a>`;
                            } else {
                                const fileName = e.filePath.split('/').pop();
                                contentHtml = `<a href="${e.filePath}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-file"></i> ${fileName}
                                </a>`;
                            }
                        }
                        if (e.message) {
                            contentHtml += (contentHtml ? '<br>' : '') + e.message;
                        }
                        
                        const messageHtml = `
                            <div class="d-flex mb-1 ${alignment}" ${!isMember && e.messageId ? `data-message-id="${e.messageId}"` : ''}>
                                <div class="message-bubble">
                                    <div class="p-3 rounded ${bgClass}">
                                        ${isMember ? `<small class="text-muted fw-bold">${e.senderName || 'Member'}</small>` : ''}
                                        <div class="mt-1">${contentHtml}</div>
                                        <div class="text-end small ${isMember ? 'text-muted' : 'text-white-50'} mt-2">
                                            ${e.time}
                                            ${!isMember ? '<i class="fas fa-check-double ms-1 read-check" style="color: rgba(255,255,255,0.5);"></i>' : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                        scrollToBottom();
                        
                        // Auto mark as read if message is from member and current user is CS (NOT admin)
                        if (isMember && currentUserRole === 'cs') {
                            console.log('[CS] Auto-marking message as read');
                            fetch("{{ route($routePrefix . '.markRead', $chatSesi['id']) }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                console.log('[CS] Auto-marked as read:', data);
                            })
                            .catch(err => console.error('[CS] Error auto-marking as read:', err));
                        }
                    })
                    .listen('.typing', (e) => {
                        console.log('Typing event:', e);
                        
                        // Only show typing if it's from member
                        if (e.userRole === 'member') {
                            typingIndicator.classList.add('active');
                            
                            // Hide after 3 seconds
                            setTimeout(() => {
                                typingIndicator.classList.remove('active');
                            }, 3000);
                        }
                    })
                    .listen('.read', (e) => {
                        console.log('[CS] Read event:', e);
                        
                        // Update check mark color to blue for read messages
                        if (e.messageIds && Array.isArray(e.messageIds)) {
                            console.log('[CS] Updating checkmarks for messages:', e.messageIds.length);
                            e.messageIds.forEach(messageId => {
                                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                                if (messageElement) {
                                    const checkIcon = messageElement.querySelector('.read-check');
                                    if (checkIcon) {
                                        console.log('[CS] Updated checkmark for message:', messageId);
                                        checkIcon.style.color = '#34b7f1';
                                    }
                                }
                            });
                        }
                    });
            }
        });
    </script>
</x-layouts.app>