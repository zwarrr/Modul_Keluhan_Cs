<x-layouts.app>
    <x-slot:header>
        <style>
            .list-group-item {
                transition: background-color 0.5s ease;
            }
            .bg-light {
                background-color: #fffacd !important;
            }

            /* Prevent the session list from wrapping around floated controls */
            .mailbox-controls::after {
                content: "";
                display: block;
                clear: both;
            }

            /* Stronger guarantee: list always starts below controls */
            .list-group.list-group-flush {
                clear: both;
            }
        </style>
    </x-slot:header>

    <section class="content">
        <div class="row">
            <div class="col-md-3">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="mb-2">
                                <label for="filter_cs" class="form-label mb-0">CS</label>
                                <select name="cs" id="filter_cs" class="form-control form-control-sm">
                                    <option value="">Semua CS</option>
                                    @foreach($listCs as $cs)
                                        <option value="{{ $cs->id }}" {{ request('cs') == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_member" class="form-label mb-0">Member</label>
                                <select name="member" id="filter_member" class="form-control form-control-sm">
                                    <option value="">Semua Member</option>
                                    @foreach($listMember as $member)
                                        <option value="{{ $member->id }}" {{ request('member') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_status" class="form-label mb-0">Status</label>
                                <select name="status" id="filter_status" class="form-control form-control-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_periode" class="form-label mb-0">Periode</label>
                                <select name="periode" id="filter_periode" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    <option value="minggu" {{ request('periode') == 'minggu' ? 'selected' : '' }}>Minggu ini</option>
                                    <option value="bulan" {{ request('periode') == 'bulan' ? 'selected' : '' }}>Bulan ini</option>
                                    <option value="tahun" {{ request('periode') == 'tahun' ? 'selected' : '' }}>Tahun ini</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_sort" class="form-label mb-0">Urutkan</label>
                                <select name="sort" id="filter_sort" class="form-control form-control-sm">
                                    <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Pesan Terbaru</h3>

                        <div class="card-tools">
                            <form method="GET" action="{{ route('admin.sesi-chat.index') }}">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i
                                    class="far fa-square"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="far fa-trash-alt"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <a href="{{ route('admin.sesi-chat.index') }}" class="btn btn-default btn-sm"><i
                                    class="fas fa-sync-alt"></i></a>
                            <div class="float-right">
                                {{ $sessions->firstItem() ?? 0 }}-{{ $sessions->lastItem() ?? 0 }}/{{ $sessions->total() }}
                                <div class="btn-group ml-2">
                                    @if($sessions->previousPageUrl())
                                        <a href="{{ $sessions->previousPageUrl() }}" class="btn btn-default btn-sm">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default btn-sm" disabled>
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    @endif
                                    @if($sessions->nextPageUrl())
                                        <a href="{{ $sessions->nextPageUrl() }}" class="btn btn-default btn-sm">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default btn-sm" disabled>
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <!-- /.float-right -->
                        </div>
                        <!-- Chat List ala Telegram/WA -->
                        <div class="list-group list-group-flush">
                            @forelse($sessions as $session)
                                @php
                                    $memberIdFromUsers = $session->member ? $session->member->member_id : $session->member_id;
                                    $displayName = $memberIdFromUsers . ' | ' . $session->id;
                                    $statusBadge = [
                                        'open' => 'warning',
                                        'pending' => 'primary',
                                        'closed' => 'success'
                                    ][$session->status] ?? 'secondary';
                                @endphp
                                <a href="{{ route('admin.cs.chat.detail', $session->id) }}"
                                    class="list-group-item list-group-item-action d-flex align-items-center py-3"
                                    data-session-id="{{ $session->id }}">
                                    <div class="flex-shrink-0 mr-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=random&color=fff" alt="Avatar"
                                            class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                    </div>
                                    <div class="flex-grow-1 ml-2" style="min-width: 0;">
                                        <div class="d-flex align-items-center mb-1">
                                            <h6 class="mb-0 flex-grow-1 text-truncate" style="min-width: 0;">{{ $displayName }}</h6>
                                            <span class="badge badge-{{ $statusBadge }} flex-shrink-0 ml-2" style="width: 95px; display: inline-flex; align-items: center; justify-content: center;">{{ ucfirst($session->status) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="text-muted text-truncate flex-grow-1" style="min-width: 0; max-width: 80%;">
                                                <small>CS: {{ $session->cs ? $session->cs->name : 'Belum ditangani' }}</small> • 
                                                <span class="last-msg">{{ $session->last_message ?? 'Belum ada pesan' }}</span>
                                            </div>
                                            <small class="text-muted flex-shrink-0 ml-2" style="width: 95px; text-align: right;">{{ $session->last_activity ? \Carbon\Carbon::parse($session->last_activity)->format('H:i') : '-' }}</small>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="list-group-item text-center py-4">
                                    <p class="text-muted mb-0">Tidak ada sesi chat</p>
                                </div>
                            @endforelse
                        </div>
                        <!-- /.mail-box-messages -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer p-0">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i
                                    class="far fa-square"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="far fa-trash-alt"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i
                                    class="fas fa-sync-alt"></i></button>
                            <div class="float-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.float-right -->
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </section>

    <!-- Modal untuk View Chat -->
    <div class="modal fade" id="chatViewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-comments"></i> Chat: <span id="modalChatTitle"></span>
                        <span class="badge badge-info ml-2"><i class="fas fa-eye"></i> View Only</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" style="height: 70vh; overflow-y: auto;" id="chatContent">
                    <div class="text-center p-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3">Loading chat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentModalSesiId = null;
        
        function viewChat(sesiId, memberName) {
            // Set title
            document.getElementById('modalChatTitle').textContent = memberName;
            currentModalSesiId = sesiId;
            
            // Show modal - wait for jQuery to be ready
            if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#chatViewModal').modal('show');
            } else {
                // Fallback: use Bootstrap modal directly if jQuery not available
                const modalEl = document.getElementById('chatViewModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            }
            
            loadChatContent(sesiId);
        }
        
        function loadChatContent(sesiId) {
            // Load chat content via AJAX dari API
            fetch('/admin/sesi-chat/api/' + sesiId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<div class="p-3">';
                        
                        if (data.pesans && data.pesans.length > 0) {
                            data.pesans.forEach(pesan => {
                                const isCs = pesan.role === 'cs';
                                const alignClass = isCs ? 'justify-content-end' : 'justify-content-start';
                                const bgClass = isCs ? 'bg-primary text-white' : 'bg-white border';
                                
                                html += `
                                    <div class="d-flex mb-3 ${alignClass}">
                                        ${!isCs ? `
                                        <div class="me-2">
                                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(pesan.sender)}&background=6c757d&color=fff" 
                                                 class="rounded-circle" style="width:38px;height:38px;">
                                        </div>` : ''}
                                        <div style="max-width: 70%;">
                                            <div class="p-3 rounded ${bgClass}">
                                                ${!isCs ? `<small class="text-muted fw-bold">${pesan.sender}</small>` : ''}
                                                <div class="mt-1">${pesan.message}</div>
                                                <div class="text-end small mt-2 ${isCs ? 'text-white-50' : 'text-muted'}">
                                                    ${pesan.time}
                                                </div>
                                            </div>
                                        </div>
                                        ${isCs ? `
                                        <div class="ms-2">
                                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(pesan.sender)}&background=007bff&color=fff" 
                                                 class="rounded-circle" style="width:38px;height:38px;">
                                        </div>` : ''}
                                    </div>
                                `;
                            });
                        } else {
                            html += '<div class="alert alert-info">Belum ada pesan</div>';
                        }
                        
                        html += '</div>';
                        document.getElementById('chatContent').innerHTML = html;
                        
                        // Auto scroll to bottom
                        const chatContent = document.getElementById('chatContent');
                        chatContent.scrollTop = chatContent.scrollHeight;
                    } else {
                        document.getElementById('chatContent').innerHTML = '<div class="alert alert-warning m-3">Chat tidak ditemukan</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('chatContent').innerHTML = '<div class="alert alert-danger m-3">Error loading chat</div>';
                });
        }

        // WebSocket untuk real-time updates
        document.addEventListener('DOMContentLoaded', function() {
            // Polling untuk refresh session list setiap 10 detik
            setInterval(function() {
                console.log('[Admin] Polling refresh - reloading page to get latest sessions');
                location.reload();
            }, 10000);
            
            // Reset modal saat ditutup
            const modalElement = document.getElementById('chatViewModal');
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function () {
                    currentModalSesiId = null;
                    document.getElementById('chatContent').innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading chat...</p></div>';
                });
                
                // jQuery fallback jika ada
                if (typeof $ !== 'undefined' && $.fn.modal) {
                    $('#chatViewModal').on('hidden.bs.modal', function () {
                        currentModalSesiId = null;
                        document.getElementById('chatContent').innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading chat...</p></div>';
                    });
                }
            }
            
            if (window.Echo) {
                // Subscribe ke semua sesi chat yang ada di halaman
                const sessionIds = [
                    @foreach($sessions as $session)
                        {{ $session->id }},
                    @endforeach
                ];
                
                console.log('Starting to subscribe to channels:', sessionIds);
                
                sessionIds.forEach(sesiId => {
                    window.Echo.channel('chat.' + sesiId)
                        .listen('.message', (e) => {
                            console.log('New message in session ' + sesiId + ':', e);
                            
                            // Update list item untuk sesi ini
                            updateSessionListItem(sesiId, e.message, e.senderName);
                            
                            // Jika modal terbuka dan ini sesi yang sedang dilihat, reload chat
                            if (currentModalSesiId === sesiId) {
                                loadChatContent(sesiId);
                            }
                        })
                        .error((error) => {
                            console.error('Error subscribing to chat.' + sesiId + ':', error);
                        });
                });
                
                // Subscribe ke global channel untuk session baru
                window.Echo.channel('sessions')
                    .listen('.session.created', (e) => {
                        console.log('New session created:', e);
                        addNewSessionToList(e.session);
                    })
                    .error((error) => {
                        console.error('Error subscribing to sessions channel:', error);
                    });
                
                console.log('Subscribed to ' + sessionIds.length + ' chat channels + global sessions channel');
            } else {
                console.warn('Echo is not available. Real-time updates disabled.');
            }
        });
        
        function updateSessionListItem(sesiId, lastMessage, senderName) {
            console.log('Updating session list item:', sesiId, lastMessage);
            
            // Cari list item untuk sesi ini dengan data-session-id
            const listItem = document.querySelector(`a[data-session-id="${sesiId}"]`);
            if (listItem) {
                console.log('Found list item, updating...');
                
                // Update last message text
                const lastMsgSpan = listItem.querySelector('.last-msg');
                if (lastMsgSpan) {
                    const truncatedMsg = lastMessage.length > 50 ? lastMessage.substring(0, 50) + '...' : lastMessage;
                    lastMsgSpan.textContent = truncatedMsg;
                    console.log('Updated last message');
                }
                
                // Update last activity time
                const timeSmall = listItem.querySelector('.time-ago');
                if (timeSmall) {
                    timeSmall.textContent = 'baru saja';
                    console.log('Updated time');
                }
                
                // Move to top (untuk UX yang lebih baik)
                const listGroup = listItem.parentElement;
                if (listGroup && listGroup.firstChild !== listItem) {
                    listGroup.insertBefore(listItem, listGroup.firstChild);
                    console.log('Moved to top');
                }
                
                // Add flash animation
                listItem.classList.add('bg-light');
                setTimeout(() => {
                    listItem.classList.remove('bg-light');
                }, 1000);
            } else {
                console.warn('List item not found for session:', sesiId);
            }
        }
        
        function addNewSessionToList(session) {
            console.log('Adding new session to list:', session);
            
            // Cek apakah session sudah ada di list
            const existingItem = document.querySelector(`a[data-session-id="${session.id}"]`);
            if (existingItem) {
                console.log('Session already exists in list, updating instead');
                updateSessionListItem(session.id, session.last_message || 'Belum ada pesan', 'Member');
                return;
            }
            
            // Buat element baru
            const listGroup = document.querySelector('.list-group.list-group-flush');
            if (!listGroup) {
                console.error('List group not found');
                return;
            }
            
            const memberIdFromUsers = session.member_id || '-';
            const displayName = memberIdFromUsers + ' | ' + session.id;
            
            const statusBadgeMap = {
                'open': 'warning',
                'pending': 'primary',
                'closed': 'success'
            };
            const statusBadge = statusBadgeMap[session.status] || 'secondary';
            
            const newItem = document.createElement('a');
            newItem.href = '/admin/cs/chat/' + session.id;
            newItem.className = 'list-group-item list-group-item-action d-flex align-items-center py-3 bg-light';
            newItem.setAttribute('data-session-id', session.id);
            
            newItem.innerHTML = `
                <div class="flex-shrink-0 mr-3">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=random&color=fff" 
                         alt="Avatar" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                </div>
                <div class="flex-grow-1 ml-2" style="min-width: 0;">
                    <div class="d-flex align-items-center mb-1">
                        <h6 class="mb-0 flex-grow-1 text-truncate" style="min-width: 0;">${displayName}</h6>
                        <span class="badge badge-${statusBadge} flex-shrink-0 ml-2" style="width: 95px; display: inline-flex; align-items: center; justify-content: center;">${session.status.charAt(0).toUpperCase() + session.status.slice(1)}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-muted text-truncate flex-grow-1" style="min-width: 0; max-width: 80%;">
                            <small>CS: ${session.cs_name || 'Belum ditangani'}</small> • 
                            <span class="last-msg">${session.last_message || 'Belum ada pesan'}</span>
                        </div>
                        <small class="text-muted flex-shrink-0 ml-2 time-ago" style="width: 95px; text-align: right;">baru saja</small>
                    </div>
                </div>
            `;
            
            // Insert di paling atas
            listGroup.insertBefore(newItem, listGroup.firstChild);
            
            // Flash animation
            setTimeout(() => {
                newItem.classList.remove('bg-light');
            }, 2000);
            
            // Subscribe ke channel session baru ini
            if (window.Echo) {
                window.Echo.channel('chat.' + session.id)
                    .listen('.message', (e) => {
                        console.log('New message in new session ' + session.id + ':', e);
                        updateSessionListItem(session.id, e.message, e.senderName);
                        
                        if (currentModalSesiId === session.id) {
                            loadChatContent(session.id);
                        }
                    });
            }
            
            console.log('New session added to list');
        }
    </script>
</x-layouts.app>

