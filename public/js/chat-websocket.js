// ============================================
// WEBSOCKET REAL-TIME COMMUNICATION
// ============================================

let currentSesiId = null;
let messagesLoaded = false;
let typingTimeout = null; // Track typing timeout

// Multi-session history state (member)
let loadedSessionsOffset = 0;
let hasMoreSessions = false;
let autoLoadedPrevSession = false;
let isLoadingOlder = false;
let historyItems = [];

// Initialize WebSocket connection saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    // Load initial messages first
    loadInitialMessages();
});

function buildMessagesUrl(offset, limit) {
    const u = new URL(chatMessagesRoute, window.location.origin);
    u.searchParams.set('sessions_offset', String(offset));
    u.searchParams.set('sessions_limit', String(limit));
    return u.toString();
}

function getMsgTimestamp(msg) {
    if (msg.sent_at) return msg.sent_at;
    if (msg.date && msg.time) return `${msg.date} ${msg.time}`;
    return null;
}

function renderHistory(items, opts = {}) {
    const preserveScroll = Boolean(opts.preserveScroll);
    const chatBodyEl = document.getElementById('chatBody');
    if (!chatBodyEl) return;

    const oldScrollHeight = chatBodyEl.scrollHeight;
    const oldScrollTop = chatBodyEl.scrollTop;

    // Reset date badge tracking for deterministic render
    if (typeof window.__resetChatDateBadges === 'function') {
        window.__resetChatDateBadges();
    }

    chatBodyEl.innerHTML = '';

    // "Lihat 2 sesi sebelumnya" control
    if (autoLoadedPrevSession && hasMoreSessions) {
        const wrap = document.createElement('div');
        wrap.className = 'date-badge';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Lihat 2 sesi sebelumnya';
        btn.style.cssText = 'border:none;background:transparent;font-weight:600;cursor:pointer;';
        btn.addEventListener('click', () => loadOlderSessions(2));
        const span = document.createElement('span');
        span.appendChild(btn);
        wrap.appendChild(span);
        chatBodyEl.appendChild(wrap);
    }

    items.forEach((msg) => {
        const type = msg.type || 'message';
        if (type === 'session_badge' || type === 'date_badge') {
            if (typeof createSessionBadge === 'function') {
                createSessionBadge(msg.label || (type === 'date_badge' ? 'Today' : 'Sesi'));
            }
            return;
        }
        const ts = getMsgTimestamp(msg);
        // Per-session badge only (no per-message date badges)
        createBubble(msg.text, msg.self, msg.file_path, msg.file_type, msg.status, false, ts, false, false, 'relative', msg.id);
    });

    if (preserveScroll) {
        const newScrollHeight = chatBodyEl.scrollHeight;
        chatBodyEl.scrollTop = newScrollHeight - oldScrollHeight + oldScrollTop;
    } else {
        scrollBottom();
    }
}

async function fetchSessionsSlice(offset, limit) {
    const url = buildMessagesUrl(offset, limit);
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.success !== true) {
        throw new Error(data.message || 'Failed to load messages');
    }
    return data;
}

// Load initial messages from server (latest session only)
async function loadInitialMessages() {
    try {
        // Member: load the last 2 sessions by default
        const data = await fetchSessionsSlice(0, 2);

        messagesLoaded = true;
        historyItems = Array.isArray(data.messages) ? data.messages : [];
        loadedSessionsOffset = Number(data.next_sessions_offset || 2);
        hasMoreSessions = Boolean(data.has_more_sessions);
        // We already loaded 2 sessions; enable the "lihat 2 sesi sebelumnya" control when available
        autoLoadedPrevSession = true;

        // Subscribe to WebSocket for active session
        if (data.active_session_id) {
            console.log('[WebSocket] Active session ID from server:', data.active_session_id);
            setSesiId(String(data.active_session_id));
        } else {
            console.warn('[WebSocket] NO active_session_id received from server!');
            console.log('[WebSocket] Server response:', data);
        }

        // Session closed state
        window.sessionClosed = Boolean(data.session_closed);
        // Note: member can still send a message to auto-start a new session.

        renderHistory(historyItems, { preserveScroll: false });
    } catch (err) {
        console.error('Error loading initial messages:', err);
    }
}

// Expose reload for other scripts (e.g., end-session button)
window.reloadChatHistory = function () {
    return loadInitialMessages();
};

// Check active session untuk dapat sesi_id
function checkActiveSesi() {
    fetch(buildMessagesUrl(0, 1))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.active_session_id) {
                setSesiId(String(data.active_session_id));
            }
        })
        .catch(err => console.error('Error checking sesi:', err));
}

function attachScrollHandler() {
    const chatBodyEl = document.getElementById('chatBody');
    if (!chatBodyEl) return;
    if (chatBodyEl.dataset.historyScrollBound === '1') return;
    chatBodyEl.dataset.historyScrollBound = '1';

    // Intentionally disabled: member uses explicit "Lihat 2 sesi sebelumnya"
}

async function loadOlderSessions(limit) {
    if (isLoadingOlder) return;
    if (!hasMoreSessions) return;

    isLoadingOlder = true;
    try {
        const data = await fetchSessionsSlice(loadedSessionsOffset, limit);
        const newItems = Array.isArray(data.messages) ? data.messages : [];

        historyItems = newItems.concat(historyItems);
        loadedSessionsOffset = Number(data.next_sessions_offset || (loadedSessionsOffset + limit));
        hasMoreSessions = Boolean(data.has_more_sessions);

        // Keep the control available as long as there are more sessions
        autoLoadedPrevSession = true;

        renderHistory(historyItems, { preserveScroll: true });
    } catch (err) {
        console.error('Error loading older sessions:', err);
    } finally {
        isLoadingOlder = false;
    }
}

// Initialize WebSocket listeners
function initWebSocket(sesiId) {
    if (!window.Echo) {
        console.error('[WebSocket] Laravel Echo NOT initialized!');
        return;
    }
    
    console.log('[WebSocket] ===== SUBSCRIBING TO CHANNEL =====');
    console.log('[WebSocket] Channel name: chat.' + sesiId);
    console.log('[WebSocket] Echo object:', window.Echo);
    
    // Subscribe to chat channel
    const channel = window.Echo.channel('chat.' + sesiId);
    console.log('[WebSocket] Channel object:', channel);
    
    channel
        .listen('.message', (e) => {
            console.log('[WebSocket] ===== MESSAGE EVENT RECEIVED =====');
            console.log('[WebSocket] Full event data:', e);
            console.log('[WebSocket] Message:', e.message);
            console.log('[WebSocket] Sender role:', e.senderRole);
            console.log('[WebSocket] Message ID:', e.messageId);
            
            // Render semua pesan dari WebSocket (termasuk pesan sendiri)
            // Karena server tidak pakai toOthers(), jadi member juga terima broadcast sendiri
            const isSelf = e.senderRole === 'member';
            const status = e.status || 'sent';
            const messageId = e.messageId || e.message_id || null;
            createBubble(e.message, isSelf, e.filePath, e.fileType, status, false, e.sent_at, true, false, 'relative', messageId);
            scrollBottom();

            // If embedded in split view, ask parent to refresh sessions list
            try {
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({ type: 'chat:refreshSessions' }, window.location.origin);
                }
            } catch (_) {
                // ignore
            }
            
            // Hide typing indicator jika ada
            removeTypingIndicator();
        })
        .listen('.typing', (e) => {
            console.log('User typing:', e);
            
            // Jangan tampilkan typing dari diri sendiri
            if (e.userId == memberIdValue) {
                return;
            }
            
            // Tampilkan typing indicator
            if (e.userRole === 'cs') {
                showTypingIndicator();
                
                // Clear previous timeout
                if (typingTimeout) {
                    clearTimeout(typingTimeout);
                }
                
                // Auto-hide after 3 seconds
                typingTimeout = setTimeout(() => {
                    removeTypingIndicator();
                    typingTimeout = null;
                }, 3000);
            }
        })
        .listen('.read', (e) => {
            console.log('[WebSocket] ===== READ EVENT RECEIVED =====');
            console.log('[WebSocket] Full event data:', e);
            console.log('[WebSocket] messageIds:', e.messageIds);
            
            // Update historyItems data terlebih dahulu
            if (e.messageIds && Array.isArray(e.messageIds)) {
                console.log('[WebSocket] Updating historyItems for messages:', e.messageIds.length);
                historyItems.forEach(msg => {
                    if (msg.id && e.messageIds.includes(msg.id)) {
                        msg.status = 'read';
                        console.log('[WebSocket] Updated status in historyItems for message:', msg.id);
                    }
                });
            } else {
                // Jika tidak ada messageIds, update semua pesan member
                historyItems.forEach(msg => {
                    if (msg.self) {
                        msg.status = 'read';
                    }
                });
            }
            
            // Update DOM: specific messages
            if (e.messageIds && Array.isArray(e.messageIds)) {
                console.log('[WebSocket] Updating DOM for specific messages:', e.messageIds.length);
                e.messageIds.forEach(messageId => {
                    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (messageElement) {
                        const checkIcon = messageElement.querySelector('.fa-check-double');
                        if (checkIcon) {
                            console.log('[WebSocket] Updated checkmark color for message:', messageId);
                            checkIcon.style.color = '#4fc3f7';
                        }
                    }
                });
            }
            
            // Fallback: update all member checkmarks to blue
            const checkmarks = document.querySelectorAll('.timestamp.self .fa-check-double');
            console.log('[WebSocket] Updating all checkmarks as fallback:', checkmarks.length);
            checkmarks.forEach(icon => {
                icon.style.color = '#4fc3f7'; // Blue color
            });
            
            // Update list sidebar jika ada
            try {
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({ type: 'chat:refreshSessions' }, window.location.origin);
                }
            } catch (_) {
                // ignore
            }
        })
        .listen('.session.closed', (e) => {
            console.log('Session closed by CS:', e);

            // Show rating modal for member (if available)
            const sesiId = e && (e.sesiId || e.sesi_id || e.sesiID);
            
            // More reliable embed detection: check URL param instead of window.parent
            const urlParams = new URLSearchParams(window.location.search);
            const isEmbedded = urlParams.get('embed') === '1';

            console.log('[Rating Debug] sesiId:', sesiId, 'isEmbedded:', isEmbedded);
            console.log('[Rating Debug] URL:', window.location.href);
            console.log('[Rating Debug] showRatingPelayananModal available:', typeof window.showRatingPelayananModal);

            try {
                if (isEmbedded) {
                    // Desktop iframe mode: ask parent to show the modal
                    console.log('[Rating Debug] Desktop iframe: Sending message to parent');
                    window.parent.postMessage({ type: 'chat:showRatingPelayanan', sesiId }, window.location.origin);
                } else {
                    // Mobile/standalone: show modal directly
                    console.log('[Rating Debug] Mobile standalone: attempting to show modal');
                    const tryShowModal = () => {
                        if (typeof window.showRatingPelayananModal === 'function') {
                            console.log('[Rating Debug] Calling showRatingPelayananModal with sesiId:', sesiId);
                            window.showRatingPelayananModal(sesiId);
                        } else {
                            console.warn('[Rating Debug] Function not ready, retrying in 500ms');
                            // Modal script might not be loaded yet, retry after a short delay
                            setTimeout(() => {
                                if (typeof window.showRatingPelayananModal === 'function') {
                                    console.log('[Rating Debug] Retry SUCCESS: Calling showRatingPelayananModal');
                                    window.showRatingPelayananModal(sesiId);
                                } else {
                                    console.error('[Rating Debug] FAILED: showRatingPelayananModal still not available after retry');
                                    console.error('[Rating Debug] Modal element check:', document.getElementById('ratingPelayananModal'));
                                }
                            }, 500);
                        }
                    };
                    tryShowModal();
                }
            } catch (err) {
                console.error('[Rating Debug] EXCEPTION:', err);
            }

            // Refresh history to show session badge boundary
            loadInitialMessages();
        });
}

// Update sesi ID dan subscribe to channel
function setSesiId(sesiId) {
    if (!sesiId) {
        console.log('[WebSocket] No sesi ID provided');
        return;
    }
    
    console.log('[WebSocket] ===== SET SESI ID =====');
    console.log('[WebSocket] New sesi ID:', sesiId);
    console.log('[WebSocket] Current sesi ID:', currentSesiId);
    
    if (currentSesiId === sesiId) {
        console.log('[WebSocket] Already subscribed to sesi:', sesiId);
        return;
    }
    
    // Unsubscribe dari channel lama
    if (currentSesiId && window.Echo) {
        console.log('[WebSocket] Leaving old channel: chat.' + currentSesiId);
        window.Echo.leave('chat.' + currentSesiId);
    }
    
    currentSesiId = sesiId;
    console.log('[WebSocket] Updated currentSesiId to:', currentSesiId);
    
    // Subscribe ke channel baru
    initWebSocket(sesiId);
}

// Hook into sendMessage untuk update sesi ID setelah kirim pesan
// (Ini dipanggil otomatis dari chat-send.js setelah pesan terkirim)
window.updateSesiIdAfterSend = function() {
    setTimeout(() => {
        fetch(buildMessagesUrl(0, 1))
            .then(response => response.json())
            .then(data => {
                if (data.active_session_id) {
                    const newSessionId = String(data.active_session_id);
                    
                    // Jika belum ada session atau session berubah
                    if (!currentSesiId || newSessionId !== currentSesiId) {
                        console.log('Updating sesi ID after send:', newSessionId);
                        setSesiId(newSessionId);
                        
                        // LANGSUNG load messages setelah subscribe (buat dapat greeting)
                        setTimeout(() => {
                            loadInitialMessages();
                        }, 800); // Tambahin delay biar greeting udah masuk DB
                    }
                }
            })
            .catch(err => console.error('Error updating sesi:', err));
    }, 500);
};


