<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Chat Room</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
  body, html {
    margin: 0; padding: 0; height: 100%;
    background: #ece5dd;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  #chatBody {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    padding: 10px 16px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #aaa transparent;
  }
  #chatBody::-webkit-scrollbar { width: 6px; }
  #chatBody::-webkit-scrollbar-thumb { background-color: #aaa; border-radius: 3px; }

  .message-wrapper { display: flex; margin-bottom: 8px; max-width: 100%; }
  .message-wrapper.self { justify-content: flex-end; }
  .message-wrapper.other { justify-content: flex-start; }

  .bubble {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    white-space: pre-wrap;
    word-break: break-word;
    font-size: 14px;
    line-height: 1.5;
    box-shadow: 0 1px 1px rgb(0 0 0 / 0.1);
    position: relative;
    max-width: 100%;
    background-clip: padding-box;
    text-align: justify;
    text-justify: inter-word;
  }
  .bubble.self { background-color: #dcf8c6; border-radius: 20px 20px 4px 20px; }
  .bubble.other { background-color: white; border-radius: 20px 20px 20px 4px; box-shadow: 0 1px 0.5px rgba(0,0,0,0.13); }

  .timestamp { font-size: 10px; color: #999; margin-top: 4px; text-align: right; user-select: none; opacity: 0.7; }
  .timestamp.self i { color: #4fc3f7; font-size: 12px; margin-left: 4px; vertical-align: middle; }

  /* Date Badge */
  .date-badge {
    text-align: center;
    margin: 16px 0;
  }
  .date-badge span {
    background: rgba(0, 0, 0, 0.08);
    color: #667781;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
    box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
  }

  header {
    background-color: #282828;
    color: white;
    display: flex;
    align-items: center;
    padding: 10px 16px;
    gap: 12px;
    box-shadow: 0 2px 5px rgb(0 0 0 / 0.2);
    position: sticky;
    top: 0;
    z-index: 10;
  }
  header img.avatar { width: 40px; height: 40px; border-radius: 9999px; object-fit: cover; border: 2px solid white; }
  header .user-info { flex-grow: 1; display: flex; flex-direction: column; justify-content: center; gap: 2px; line-height: 1.2; }
  header .user-info .name { font-weight: 600; font-size: 16px; margin: 0; }
  header .user-info .status { font-size: 12px; opacity: 0.8; margin: 0; }

  footer {
    background: #f0f0f0;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-top: 1px solid #ddd;
    position: sticky;
    bottom: 0;
    z-index: 10;
  }
  footer textarea {
    flex-grow: 1;
    min-height: 38px;
    max-height: 120px;
    resize: none;
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 18px;
    border: 1px solid #ccc;
    outline: none;
    overflow-y: hidden;
    font-family: inherit;
    line-height: 1.4;
  }
  footer textarea:focus {
    border-color: #282828;
    box-shadow: 0 0 3px #282828aa;
  }
  footer label { cursor: pointer; color: #555; font-size: 20px; display: flex; align-items: center; }
  footer label:hover { color: #282828; }
  footer i.fa-camera { cursor: pointer; color: #555; font-size: 20px; transition: color 0.2s; }
  footer i.fa-camera:hover { color: #282828; }
  footer button#sendBtn {
    background-color: #282828;
    width: 44px; height: 44px;
    border-radius: 9999px;
    border: none;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    box-shadow: 0 3px 8px rgba(40, 40, 40, 0.5);
    cursor: pointer;
    transition: background-color 0.2s;
  }
  footer button#sendBtn:hover { background-color: #1a1a1a; }



  /* Dropdown menu for ellipsis */
  .menu-dropdown {
    display: none;
    position: fixed;
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    min-width: 200px;
    z-index: 1000;
    right: 16px;
    top: 60px;
  }
  .menu-dropdown.show { display: block; }
  .menu-dropdown div { 
    padding: 14px 20px; 
    cursor: pointer; 
    font-size: 14px;
    color: #303030;
    display: flex;
    align-items: center;
    transition: background 0.2s ease;
  }
  .menu-dropdown div:hover { background: #f5f5f5; }
  .menu-dropdown div i { color: #666; font-size: 16px; }
  .menu-dropdown div:first-child { border-radius: 4px 4px 0 0; }
  .menu-dropdown div:last-child { border-radius: 0 0 4px 4px; }

  /* File Preview */
  #filePreview {
    display: none;
    position: fixed;
    bottom: 60px;
    left: 16px;
    right: 16px;
    background: white;
    border-radius: 8px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 100;
  }
  #filePreview.show { display: block; }
  #filePreview .preview-content {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  #filePreview img {
    max-width: 80px;
    max-height: 80px;
    border-radius: 4px;
    object-fit: cover;
  }
  #filePreview .file-icon {
    width: 60px;
    height: 60px;
    background: #e0e0e0;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: #666;
  }
  #filePreview .file-info {
    flex: 1;
    min-width: 0;
  }
  #filePreview .file-name {
    font-size: 14px;
    font-weight: 500;
    color: #303030;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  #filePreview .file-size {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
  }
  #filePreview .remove-file {
    cursor: pointer;
    color: #f44336;
    font-size: 20px;
    padding: 8px;
  }
  #filePreview .remove-file:hover {
    color: #d32f2f;
  }

  /* Message with file */
  .bubble img.message-image {
    max-width: 100%;
    border-radius: 8px;
    margin-top: 4px;
    cursor: pointer;
  }
  .bubble .file-attachment {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    background: rgba(0,0,0,0.05);
    border-radius: 8px;
    margin-top: 4px;
  }
  .bubble .file-attachment .file-icon {
    width: 40px;
    height: 40px;
    background: rgba(0,0,0,0.1);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .bubble .file-attachment .file-info {
    flex: 1;
    min-width: 0;
  }
  .bubble .file-attachment .file-name {
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .bubble .file-attachment .file-size {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 2px;
  }
  .bubble .file-attachment a {
    color: inherit;
    text-decoration: none;
  }
</style>
</head>
<body>

<header>
  @if(!request()->boolean('embed'))
    <i class="fa-solid fa-arrow-left cursor-pointer" onclick="window.location.href='{{ route('chat.list') }}'"></i>
  @endif
  <img class="avatar" src="{{ asset('img/logo_tms.png') }}" alt="avatar"/>
  <div class="user-info">
    <div class="name">Customer Service</div>
    <div class="status">online</div>
  </div>
  <!-- <i class="fa-solid fa-phone cursor-pointer"></i>
  <i class="fa-solid fa-video cursor-pointer"></i> -->
  <i class="fa-solid fa-ellipsis-vertical cursor-pointer" id="ellipsisBtn"></i>
</header>

<!-- Dropdown menu -->
<div id="menuDropdown" class="menu-dropdown">
  <!-- Clear Chat feature disabled
  <div id="clearChatBtn">
    <i class="fa-solid fa-trash-can" style="margin-right: 12px; width: 16px;"></i>
    <span>Clear Chat</span>
  </div>
  -->
  <div id="endSessionBtn">
    <i class="fa-solid fa-circle-xmark" style="margin-right: 12px; width: 16px;"></i>
    <span>Akhiri sesi chat</span>
  </div>
  <!-- Logout feature disabled
  <div id="logoutBtn">
    <i class="fa-solid fa-right-from-bracket" style="margin-right: 12px; width: 16px;"></i>
    <span>Logout</span>
  </div> -->
</div>

<!-- Logout Form (Hidden) -->
<form id="logoutForm" action="{{ route('member.logout') }}" method="POST" style="display: none;">
  @csrf
</form>

<!-- Include Modals -->
<!-- Clear Chat modal disabled -->
{{-- @include('member.sections.modal_information.clear_chat_modal') --}}

@include('member.sections.modal_information.loading_modal')

<!-- End Session Confirmation Modal: include in non-embed mode (mobile/standalone) -->
@if(!request()->boolean('embed'))
  @include('member.sections.modal_information.end_session_confirm_modal')
@endif

<!-- Rating modal: always include in non-embed mode (mobile/standalone) -->
@if(!request()->boolean('embed'))
  @include('member.sections.modal_information.ratting_pelayanan')
  <script>
    // Debug: Verify modal is loaded in mobile
    console.log('[Rating Modal] Included in page, checking function...');
    setTimeout(() => {
      console.log('[Rating Modal] showRatingPelayananModal available:', typeof window.showRatingPelayananModal);
      console.log('[Rating Modal] Modal element:', document.getElementById('ratingPelayananModal'));
    }, 100);
  </script>
@endif

<main id="chatBody"></main>

<!-- File Preview -->
<div id="filePreview">
  <div class="preview-content">
    <div id="previewContainer"></div>
    <div class="file-info">
      <div class="file-name" id="fileName"></div>
      <div class="file-size" id="fileSize"></div>
    </div>
    <i class="fa-solid fa-times remove-file" id="removeFileBtn"></i>
  </div>
</div>

<footer>
  <i id="emojiBtn" class="fa-regular fa-face-smile cursor-pointer"></i>
  <textarea id="messageInput" placeholder="Type a message" rows="1" autocomplete="off" spellcheck="false"></textarea>

  <label title="Attach File">
    <i class="fa-solid fa-paperclip"></i>
    <input type="file" id="fileInput" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar" hidden />
  </label>

  <i class="fa-solid fa-camera cursor-pointer" id="cameraBtn" title="Open Camera"></i>

  <button id="sendBtn" title="Send Message">
    <i class="fa-solid fa-paper-plane"></i>
  </button>
</footer>

@include('member.sections.camera_section')

<script>
// ============================================
// GLOBAL VARIABLES AND CONSTANTS
// ============================================

// DOM Elements
const chatBody = document.getElementById("chatBody");
const input = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");
const emojiBtn = document.getElementById("emojiBtn");
const fileInput = document.getElementById("fileInput");
const cameraBtn = document.getElementById("cameraBtn");
const ellipsisBtn = document.getElementById("ellipsisBtn");
const menuDropdown = document.getElementById("menuDropdown");
const filePreview = document.getElementById("filePreview");
const removeFileBtn = document.getElementById("removeFileBtn");

// State variables
let selectedFile = null;
let fileCounter = 1;
let sessionClosed = false;
let currentSessionHasCS = false; // Track if current session is handled by CS

// Member info for comparison with WebSocket events
const memberIdValue = "{{ auth('member')->user()->member_id ?? '' }}";
const currentMemberId = {{ auth('member')->user()->id ?? 'null' }};

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Routes (from Laravel)
const chatSendRoute = '{{ route("chat.send") }}';
const chatMessagesRoute = '{{ route("chat.messages") }}';
const chatTypingRoute = '{{ route("chat.typing") }}';
const chatNewSessionRoute = '{{ route("chat.newSession") }}';
const chatEndSessionRoute = '{{ route("chat.endSession") }}';

// If embedded in desktop split view: disable browser context menu and forward to parent
if ({{ request()->boolean('embed') ? 'true' : 'false' }}) {
  // Listen for messages from parent to execute end session
  window.addEventListener('message', (event) => {
    if (event.origin !== window.location.origin) return;
    const data = event.data || {};
    
    if (data.type === 'chat:executeEndSession') {
      if (typeof window.actuallyEndSession === 'function') {
        window.actuallyEndSession();
      }
    }
  });

  const notifyParentShowCloseMenu = (e) => {
    try {
      if (window.parent && window.parent !== window) {
        window.parent.postMessage(
          {
            type: 'chat:showCloseMenuFromIframe',
            x: e.clientX,
            y: e.clientY
          },
          window.location.origin
        );
      }
    } catch (_) {
      // ignore
    }
  };

  document.addEventListener('contextmenu', (e) => {
    e.preventDefault();
    e.stopPropagation();
    notifyParentShowCloseMenu(e);
  });

  document.addEventListener('dblclick', (e) => {
    e.preventDefault();
    e.stopPropagation();
    notifyParentShowCloseMenu(e);
  });

  const notifyParentHideMenu = () => {
    try {
      if (window.parent && window.parent !== window) {
        window.parent.postMessage({ type: 'chat:hideMenuFromIframe' }, window.location.origin);
      }
    } catch (_) {
      // ignore
    }
  };

  // Normal interactions should not keep/show the menu
  document.addEventListener('click', () => notifyParentHideMenu(), true);
  document.addEventListener('scroll', () => notifyParentHideMenu(), true);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') notifyParentHideMenu();
  });
}

// Function to check if current session has CS handler
window.checkSessionHasCS = async function() {
  try {
    const response = await fetch(chatMessagesRoute + '?sessions_offset=0&sessions_limit=1');
    const data = await response.json();
    
    if (data.success && data.active_session_id) {
      // Fetch session details to check cs_id
      const sessionResponse = await fetch(`{{ url('/api/member/session') }}/${data.active_session_id}`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        }
      });
      const sessionData = await sessionResponse.json();
      
      currentSessionHasCS = sessionData.cs_id !== null && sessionData.cs_id !== undefined;
    } else {
      currentSessionHasCS = false;
    }
    
    // Update button state
    const endSessionBtn = document.getElementById('endSessionBtn');
    if (endSessionBtn) {
      if (currentSessionHasCS) {
        endSessionBtn.style.opacity = '1';
        endSessionBtn.style.cursor = 'pointer';
        endSessionBtn.style.pointerEvents = 'auto';
      } else {
        endSessionBtn.style.opacity = '0.5';
        endSessionBtn.style.cursor = 'not-allowed';
        endSessionBtn.style.pointerEvents = 'none';
      }
    }
  } catch (err) {
    console.error('Error checking session CS:', err);
  }
};

// Function to actually end session (called after confirmation)
window.actuallyEndSession = function() {
  // Hide modal - check if function exists (mobile mode) or tell parent (desktop mode)
  if (typeof window.hideEndSessionConfirmModal === 'function') {
    window.hideEndSessionConfirmModal();
  } else {
    // Desktop mode - tell parent to hide modal
    try {
      if (window.parent && window.parent !== window) {
        window.parent.postMessage({ type: 'chat:hideEndSessionModal' }, window.location.origin);
      }
    } catch (e) {}
  }
  
  fetch(chatEndSessionRoute, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Content-Type': 'application/json'
    }
  })
  .then(async (response) => {
    const data = await response.json().catch(() => ({}));
    if (!response.ok || data.success === false) {
      throw new Error(data.message || 'Gagal mengakhiri sesi');
    }

    // Refresh merged timeline so session badge appears immediately
    if (typeof window.reloadChatHistory === 'function') {
      window.reloadChatHistory();
    }

    // Show rating modal after member closes session
    if (data.sesi_id && typeof window.showRatingPelayananModal === 'function') {
      console.log('[End Session] Showing rating modal for session:', data.sesi_id);
      setTimeout(() => {
        window.showRatingPelayananModal(data.sesi_id);
      }, 500);
    }

    // If embedded in split view, refresh sessions list
    try {
      if (window.parent && window.parent !== window) {
        window.parent.postMessage({ type: 'chat:refreshSessions' }, window.location.origin);
      }
    } catch (_) {
      // ignore
    }
    
    // Reset session CS check
    currentSessionHasCS = false;
    window.checkSessionHasCS();
  })
  .catch((err) => {
    console.error(err);
    alert(err.message || 'Gagal mengakhiri sesi chat. Silakan coba lagi.');
  });
};

// End session button - show confirmation modal first
const endSessionBtn = document.getElementById('endSessionBtn');
if (endSessionBtn) {
  endSessionBtn.addEventListener('click', () => {
    // Close dropdown
    if (menuDropdown) {
      menuDropdown.classList.remove('show');
    }

    // Check if session has CS before allowing end
    if (!currentSessionHasCS) {
      alert('Sesi belum ditangani oleh Customer Service. Anda tidak dapat mengakhiri sesi ini.');
      return;
    }

    // Show confirmation modal
    // If in desktop embed mode, tell parent to show modal
    const isEmbedMode = {{ request()->boolean('embed') ? 'true' : 'false' }};
    if (isEmbedMode && window.parent && window.parent !== window) {
      try {
        window.parent.postMessage({ type: 'chat:showEndSessionModal' }, window.location.origin);
      } catch (e) {}
    } else {
      // Mobile/standalone mode
      if (typeof window.showEndSessionConfirmModal === 'function') {
        window.showEndSessionConfirmModal();
      }
    }
  });
}

// Confirm end session button in modal - REMOVED (now handled in modal file itself)

// Check session CS status on page load and auto-check every 10 seconds
document.addEventListener('DOMContentLoaded', () => {
  // Check session CS status on page load
  window.checkSessionHasCS();
  
  // Auto-check setiap 10 detik untuk realtime update
  setInterval(() => {
    window.checkSessionHasCS();
  }, 10000); // 10 detik
});

// Also check when messages are loaded
window.addEventListener('messagesLoaded', () => {
  window.checkSessionHasCS();
});
</script>

<!-- Include JS modules -->
<!-- WebSocket Real-time -->
@vite(['resources/js/app.js'])

<!-- UI utilities and message rendering (needed by WebSocket) -->
<script src="{{ asset('js/chat-ui.js') }}"></script>
<script src="{{ asset('js/chat-file-handler.js') }}"></script>

<!-- Polling (COMMENTED - using WebSocket instead) -->
<!--
<script src="{{ asset('js/chat-messages.js') }}"></script>
-->

<!-- WebSocket implementation -->
<script src="{{ asset('js/chat-websocket.js') }}"></script>
<script src="{{ asset('js/chat-send.js') }}"></script>
<script src="{{ asset('js/chat-modal.js') }}"></script>

</body>
</html>
