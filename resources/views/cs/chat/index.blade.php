<x-layouts.app>
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
                                <label for="filter_read" class="form-label mb-0">Status Pesan</label>
                                <select name="read" id="filter_read" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    <option value="unread" {{ request('read') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                                    <option value="read" {{ request('read') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_sort" class="form-label mb-0">Urutkan</label>
                                <select name="sort" id="filter_sort" class="form-control form-control-sm">
                                    <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_search" class="form-label mb-0">Cari Member</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Nama member..." value="{{ request('search') }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm btn-block mt-3">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Chat Member</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="chat-list">
                            <div class="text-center py-5 text-muted" id="loading-chat">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
    function renderChatList(chatList) {
        let html = '';
        if(chatList.length === 0) {
            html = '<div class="text-center py-5 text-muted">Tidak ada chat ditemukan.</div>';
        } else {
            chatList.forEach(function(chat) {
                let statusClass = 'badge-secondary';
                let statusText = chat.status;
                if (chat.status === 'open') {
                    statusClass = 'badge-warning';
                    statusText = 'Open';
                } else if (chat.status === 'pending') {
                    statusClass = 'badge-info';
                    statusText = 'Pending';
                } else if (chat.status === 'closed') {
                    statusClass = 'badge-success';
                    statusText = 'Closed';
                }
                const statusWidth = 70;
                const unreadBadge = `<span class="badge badge-danger" style="display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0; line-height: 18px;">${chat.unread}</span>`;
                const unreadPlaceholder = `<span class="badge badge-danger" style="display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0; line-height: 18px; visibility: hidden;">0</span>`;
                html += `
                <a href="${window.route_cs_chat_detail.replace(':id', chat.id)}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="flex-shrink-0 mr-3">
                        <img src="${chat.avatar}" alt="Avatar" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                    </div>
                    <div class="flex-grow-1 ml-2" style="min-width: 0; position: relative; padding-right: 110px;">
                        <div style="position: absolute; right: 0; top: 0; width: 95px; text-align: center; display: grid; grid-template-columns: ${statusWidth}px 18px; column-gap: 6px; justify-content: center; align-items: center;">
                            <span class="badge ${statusClass}" style="grid-column: 1; width: ${statusWidth}px; display: inline-flex; align-items: center; justify-content: center;">${statusText}</span>
                            ${chat.unread > 0 ? unreadBadge : unreadPlaceholder}
                            <small class="text-muted d-block" style="grid-column: 1; text-align: center; margin-top: 4px;">${chat.last_time || '-'}</small>
                        </div>

                        <h6 class="mb-0 text-truncate" style="min-width: 0;">${chat.name}</h6>
                        <span class="text-muted text-truncate d-block" style="min-width: 0; max-width: 80%;">${chat.last_message}</span>
                    </div>
                </a>
                `;
            });
        }
        document.getElementById('chat-list').innerHTML = html;
    }

    function fetchChatList() {
        const params = new URLSearchParams(window.location.search);
        const routeIndex = "{{ auth()->user()->role === 'admin' ? route('admin.cs.chat.index') : route('cs.chat.index') }}";
        fetch(`${routeIndex}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('HTTP error ' + res.status);
            }
            return res.json();
        })
        .then(data => {
            if (data && data.data) {
                renderChatList(data.data);
            } else {
                renderChatList([]);
            }
        })
        .catch(err => {
            console.error('Error fetching chat list:', err);
            document.getElementById('chat-list').innerHTML = '<div class="text-center py-5 text-danger">Gagal memuat daftar chat. Silakan refresh halaman.</div>';
        });
    }

    // Route helper for detail
    window.route_cs_chat_detail = "{{ auth()->user()->role === 'admin' ? route('admin.cs.chat.detail', [':id']) : route('cs.chat.detail', [':id']) }}";

    document.addEventListener('DOMContentLoaded', function() {
        fetchChatList();
        // Polling every 10 seconds as backup
        setInterval(fetchChatList, 10000);
        
        // Listen for refresh messages from detail page
        window.addEventListener('message', function(event) {
            if (event.origin !== window.location.origin) return;
            if (event.data && event.data.type === 'refreshChatList') {
                console.log('[CS Index] Received refresh request from detail page');
                fetchChatList();
            }
        });
        
        // WebSocket: Listen for read events to refresh list immediately
        if (window.Echo) {
            // Subscribe to all active sessions for real-time unread count updates
            // Note: In production, consider using presence channel or dedicated notifications channel
            window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                if (states.current === 'connected') {
                    console.log('[CS Index] WebSocket connected, refreshing chat list');
                    fetchChatList();
                }
            });
        }
        
        // Filter form submit pakai AJAX (optional, fallback reload)
        document.querySelector('form').addEventListener('submit', function(e){
            e.preventDefault();
            const params = new URLSearchParams(new FormData(this));
            history.replaceState(null, '', `?${params.toString()}`);
            fetchChatList();
        });
    });
    </script>
        </div>
    </section>
</x-layouts.app>
