<x-layouts.app>


    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <form method="GET" class="card card-body p-2 mb-2 shadow-sm border-0">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="filter_read" class="form-label mb-0">Status Pesan</label>
                                <select name="read" id="filter_read" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    <option value="unread" {{ request('read') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                                    <option value="read" {{ request('read') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_sort" class="form-label mb-0">Urutkan</label>
                                <select name="sort" id="filter_sort" class="form-control form-control-sm">
                                    <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter_search" class="form-label mb-0">Cari Member</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Nama member..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
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
                } else if (chat.status === 'onprogress') {
                    statusClass = 'badge-info';
                    statusText = 'On Progress';
                } else if (chat.status === 'closed') {
                    statusClass = 'badge-success';
                    statusText = 'Closed';
                }
                html += `
                <a href="${window.route_cs_chat_detail.replace(':id', chat.id)}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="flex-shrink-0 me-3">
                        <img src="${chat.avatar}" alt="Avatar" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">${chat.name}</h6>
                                <span class="badge ${statusClass} mt-1">${statusText}</span>
                            </div>
                            <small class="text-muted">${chat.last_time}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted text-truncate" style="max-width:220px;">${chat.last_message}</span>
                            ${chat.unread > 0 ? `<span class="badge badge-danger ms-2">${chat.unread}</span>` : ''}
                        </div>
                    </div>
                </a>
                `;
            });
        }
        document.getElementById('chat-list').innerHTML = html;
    }

    function fetchChatList() {
        const params = new URLSearchParams(window.location.search);
        fetch(`{{ route('cs.chat.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            renderChatList(data.data);
        });
    }

    // Route helper for detail
    window.route_cs_chat_detail = "{{ route('cs.chat.detail', [':id']) }}";

    document.addEventListener('DOMContentLoaded', function() {
        fetchChatList();
        // Optional: polling/live
        setInterval(fetchChatList, 10000);
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