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

  /* Camera Modal */
  .camera-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    z-index: 1000;
    flex-direction: column;
  }
  .camera-modal.active { display: flex; }
  .camera-modal video {
    width: 100%;
    height: calc(100% - 120px);
    object-fit: cover;
    background: #000;
  }
  .camera-modal video.mirror {
    transform: scaleX(-1);
  }
  .camera-modal .camera-controls {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 20px;
    background: #1f1f1f;
  }
  .camera-modal .camera-controls button {
    background: white;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 24px;
    color: #333;
    transition: transform 0.2s;
  }
  .camera-modal .camera-controls button:hover { transform: scale(1.1); }
  .camera-modal .camera-controls button.capture {
    width: 70px;
    height: 70px;
    background: #25d366;
    color: white;
  }
  .camera-modal .camera-controls button.close {
    background: #f44336;
    color: white;
  }
  .camera-modal .camera-controls button.switch {
    background: #2196F3;
    color: white;
  }

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
</style>
</head>
<body>

<header>
  <!-- Back button
  <i class="fa-solid fa-arrow-left cursor-pointer"></i>
  -->
  <img class="avatar" src="https://i.pinimg.com/736x/67/e5/34/67e5342afddcacf45fcc102c2a12c11d.jpg" alt="avatar"/>
  <div class="user-info">
    <div class="name">Customer Service</div>
    <div class="status">online</div>
  </div>
  <!-- Icon
  <i class="fa-solid fa-phone cursor-pointer"></i>
  <i class="fa-solid fa-video cursor-pointer"></i>
  -->
  <i class="fa-solid fa-ellipsis-vertical cursor-pointer" id="ellipsisBtn"></i>
</header>

<!-- Dropdown menu -->
<div id="menuDropdown" class="menu-dropdown">
  <div id="clearChatBtn">
    <i class="fa-solid fa-trash-can" style="margin-right: 12px; width: 16px;"></i>
    <span>Clear Chat</span>
  </div>
  <div id="logoutBtn">
    <i class="fa-solid fa-right-from-bracket" style="margin-right: 12px; width: 16px;"></i>
    <span>Logout</span>
  </div>
</div>

<!-- Logout Form (Hidden) -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
  @csrf
</form>

<!-- Include Modals -->
@include('member.sections.modal_information.logout_modal')
@include('member.sections.modal_information.clear_chat_modal')
@include('member.sections.modal_information.loading_modal')

<main id="chatBody"></main>

<footer>
  <i id="emojiBtn" class="fa-regular fa-face-smile cursor-pointer"></i>
  <textarea id="messageInput" placeholder="Type a message" rows="1" autocomplete="off" spellcheck="false"></textarea>

  <label title="Attach File">
    <i class="fa-solid fa-paperclip"></i>
    <input type="file" id="fileInput" hidden />
  </label>

  <i class="fa-solid fa-camera cursor-pointer" id="cameraBtn" title="Open Camera"></i>

  <button id="sendBtn" title="Send Message">
    <i class="fa-solid fa-paper-plane"></i>
  </button>
</footer>

<!-- Camera Modal -->
<div id="cameraModal" class="camera-modal">
  <video id="cameraVideo" autoplay playsinline></video>
  <div class="camera-controls">
    <button id="closeCameraBtn" class="close" title="Close Camera">
      <i class="fa-solid fa-times"></i>
    </button>
    <button id="captureBtn" class="capture" title="Capture Photo">
      <i class="fa-solid fa-camera"></i>
    </button>
    <button id="switchCameraBtn" class="switch" title="Switch Camera">
      <i class="fa-solid fa-rotate"></i>
    </button>
  </div>
  <canvas id="cameraCanvas" style="display:none;"></canvas>
</div>

<script>
const chatBody = document.getElementById("chatBody");
const input = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");
const emojiBtn = document.getElementById("emojiBtn");
const fileInput = document.getElementById("fileInput");
const cameraBtn = document.getElementById("cameraBtn");
const ellipsisBtn = document.getElementById("ellipsisBtn");
const menuDropdown = document.getElementById("menuDropdown");

// Camera elements
const cameraModal = document.getElementById("cameraModal");
const cameraVideo = document.getElementById("cameraVideo");
const cameraCanvas = document.getElementById("cameraCanvas");
const captureBtn = document.getElementById("captureBtn");
const closeCameraBtn = document.getElementById("closeCameraBtn");
const switchCameraBtn = document.getElementById("switchCameraBtn");

let currentStream = null;
let facingMode = "user"; // "user" untuk depan, "environment" untuk belakang


// Emoji picker
const emojiPicker = document.createElement("div");
emojiPicker.style = "display:none; position:fixed; bottom:60px; left:16px; right:16px; background:#fff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); padding:10px; max-height:150px; overflow-y:auto; z-index:100;";
const emojis = ["😀","😁","😂","😎","😍","😡","😢","👍","🙏","🔥","❤️","🎉"];
emojis.forEach(e => {
  const span = document.createElement("span");
  span.textContent = e;
  span.style.cssText = "font-size:20px; cursor:pointer; margin:4px;";
  span.addEventListener("click", () => {
    input.value += e;
    autoResize();
    input.focus();
  });
  emojiPicker.appendChild(span);
});
document.body.appendChild(emojiPicker);

emojiBtn.addEventListener("click", () => {
  emojiPicker.style.display = emojiPicker.style.display === "none" ? "block" : "none";
});

// Auto resize
function autoResize() {
  input.style.height = "auto";
  input.style.height = input.scrollHeight + "px";
}
input.addEventListener("input", autoResize);

// Scroll to bottom
function scrollBottom() { setTimeout(() => { chatBody.scrollTop = chatBody.scrollHeight; }, 100); }

// Time
function getTime() {
  const d = new Date();
  return d.getHours().toString().padStart(2,"0") + ":" + d.getMinutes().toString().padStart(2,"0");
}

// Get date string for grouping
function getDateString(date) {
  return date.getFullYear() + '-' + 
         (date.getMonth() + 1).toString().padStart(2, '0') + '-' + 
         date.getDate().toString().padStart(2, '0');
}

// Format date badge
function formatDateBadge(date) {
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);
  
  const dateStr = getDateString(date);
  const todayStr = getDateString(today);
  const yesterdayStr = getDateString(yesterday);
  
  if (dateStr === todayStr) {
    return 'Today';
  } else if (dateStr === yesterdayStr) {
    return 'Yesterday';
  } else {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
  }
}

// Track last date badge shown
let lastDateBadge = null;

// Create date badge if needed
function addDateBadgeIfNeeded() {
  const now = new Date();
  const currentDateStr = getDateString(now);
  
  if (lastDateBadge !== currentDateStr) {
    const dateBadge = document.createElement('div');
    dateBadge.className = 'date-badge';
    dateBadge.setAttribute('data-date', currentDateStr);
    
    const span = document.createElement('span');
    span.textContent = formatDateBadge(now);
    
    dateBadge.appendChild(span);
    chatBody.appendChild(dateBadge);
    
    lastDateBadge = currentDateStr;
  }
}

// Create message bubble
function createBubble(text, self = true) {
  // Add date badge if needed
  addDateBadgeIfNeeded();
  
  // Clean up text: remove extra spaces and normalize line breaks
  let cleanText = text
    .replace(/\n{3,}/g, '\n\n')  // Max 2 line breaks
    .replace(/ {2,}/g, ' ')       // Replace multiple spaces with single space
    .trim();
  
  const wrapper = document.createElement("div");
  wrapper.className = "message-wrapper " + (self ? "self" : "other");

  const container = document.createElement("div");
  const bubble = document.createElement("div");
  bubble.className = "bubble " + (self ? "self" : "other");
  bubble.textContent = cleanText;

  const timestamp = document.createElement("div");
  timestamp.className = "timestamp " + (self ? "self" : "");
  timestamp.textContent = getTime();
  if (self) {
    const icon = document.createElement("i");
    icon.className = "fa-solid fa-check-double";
    icon.style.marginLeft = "4px"; icon.style.color = "#4fc3f7";
    timestamp.appendChild(icon);
  }

  container.appendChild(bubble);
  container.appendChild(timestamp);
  wrapper.appendChild(container);
  chatBody.appendChild(wrapper);
  scrollBottom();
}

// Send message
function sendMessage() {
  const text = input.value.trim();
  if(!text) return;
  
  // Tampilkan pesan user
  createBubble(text, true);
  input.value = "";
  autoResize();
  
  // Tampilkan typing indicator
  showTypingIndicator();
  
  // Kirim ke API
  fetch('{{ route("chat.send") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ message: text })
  })
  .then(response => response.json())
  .then(data => {
    removeTypingIndicator();
    if (data.success) {
      createBubble(data.message, false);
    } else {
      createBubble('Maaf, terjadi kesalahan. Silakan coba lagi.', false);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    removeTypingIndicator();
    createBubble('Maaf, sistem sedang mengalami gangguan.', false);
  });
}

// Typing indicator functions
function showTypingIndicator() {
  const wrapper = document.createElement("div");
  wrapper.className = "message-wrapper other typing-indicator-wrapper";
  wrapper.id = "typingIndicator";
  
  const container = document.createElement("div");
  const bubble = document.createElement("div");
  bubble.className = "bubble other";
  bubble.innerHTML = '<div style="display:flex;gap:4px;align-items:center;"><span style="width:8px;height:8px;background:#999;border-radius:50%;animation:bounce 1.4s infinite ease-in-out both;"></span><span style="width:8px;height:8px;background:#999;border-radius:50%;animation:bounce 1.4s infinite ease-in-out both;animation-delay:0.16s;"></span><span style="width:8px;height:8px;background:#999;border-radius:50%;animation:bounce 1.4s infinite ease-in-out both;animation-delay:0.32s;"></span></div>';
  
  const style = document.createElement('style');
  style.textContent = '@keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }';
  document.head.appendChild(style);
  
  container.appendChild(bubble);
  wrapper.appendChild(container);
  chatBody.appendChild(wrapper);
  scrollBottom();
}

function removeTypingIndicator() {
  const indicator = document.getElementById("typingIndicator");
  if (indicator) {
    indicator.remove();
  }
}

sendBtn.addEventListener("click", sendMessage);
input.addEventListener("keydown", e => { if(e.key==="Enter" && !e.shiftKey){ e.preventDefault(); sendMessage(); } });

// File upload
fileInput.addEventListener("change", e => { const file = e.target.files[0]; if(!file) return; sendImage(URL.createObjectURL(file), true); });

// Camera functions
async function startCamera() {
  try {
    if (currentStream) {
      currentStream.getTracks().forEach(track => track.stop());
    }
    
    const constraints = {
      video: {
        facingMode: facingMode,
        width: { ideal: 1280 },
        height: { ideal: 720 }
      },
      audio: false
    };
    
    currentStream = await navigator.mediaDevices.getUserMedia(constraints);
    cameraVideo.srcObject = currentStream;
    
    // Tambahkan efek mirror untuk kamera depan (selfie)
    if (facingMode === "user") {
      cameraVideo.classList.add("mirror");
    } else {
      cameraVideo.classList.remove("mirror");
    }
    
    cameraModal.classList.add("active");
  } catch (error) {
    alert("Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.");
    console.error("Camera error:", error);
  }
}

function stopCamera() {
  if (currentStream) {
    currentStream.getTracks().forEach(track => track.stop());
    currentStream = null;
  }
  cameraModal.classList.remove("active");
}

function capturePhoto() {
  const context = cameraCanvas.getContext("2d");
  cameraCanvas.width = cameraVideo.videoWidth;
  cameraCanvas.height = cameraVideo.videoHeight;
  
  // Jika kamera depan, flip gambar agar sesuai dengan preview mirror
  if (facingMode === "user") {
    context.translate(cameraCanvas.width, 0);
    context.scale(-1, 1);
  }
  
  context.drawImage(cameraVideo, 0, 0);
  
  cameraCanvas.toBlob(blob => {
    const url = URL.createObjectURL(blob);
    sendImage(url, true);
    stopCamera();
  }, "image/jpeg", 0.9);
}

function switchCamera() {
  facingMode = facingMode === "user" ? "environment" : "user";
  startCamera();
}

cameraBtn.addEventListener("click", startCamera);
captureBtn.addEventListener("click", capturePhoto);
closeCameraBtn.addEventListener("click", stopCamera);
switchCameraBtn.addEventListener("click", switchCamera);

// File & Camera - REMOVED OLD CAMERA INPUT

function sendImage(url, self = true) {
  const wrapper = document.createElement("div");
  wrapper.className = "message-wrapper " + (self ? "self" : "other");

  const container = document.createElement("div");
  const bubble = document.createElement("div");
  bubble.className = "bubble " + (self ? "self" : "other");
  bubble.style.padding = "4px";

  const img = document.createElement("img");
  img.src = url;
  img.style.maxHeight = "160px"; img.style.borderRadius="12px"; img.style.display="block"; img.style.maxWidth="100%";

  const timestamp = document.createElement("div");
  timestamp.className = "timestamp " + (self ? "self" : "");
  timestamp.textContent = getTime();
  if(self){ const icon = document.createElement("i"); icon.className="fa-solid fa-check-double"; icon.style.marginLeft="4px"; icon.style.color="#4fc3f7"; timestamp.appendChild(icon); }

  bubble.appendChild(img);
  container.appendChild(bubble);
  container.appendChild(timestamp);
  wrapper.appendChild(container);
  chatBody.appendChild(wrapper);
  scrollBottom();
}

// Ellipsis menu
ellipsisBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  menuDropdown.classList.toggle("show");
});

document.addEventListener("click", e => {
  if(!menuDropdown.contains(e.target) && e.target !== ellipsisBtn){
    menuDropdown.classList.remove("show");
  }
});

// Clear chat functionality
document.getElementById("clearChatBtn").addEventListener("click", (e) => {
  e.preventDefault();
  menuDropdown.classList.remove("show");
  document.getElementById("clearChatModal").style.display = "flex";
});

// Clear Chat Modal Actions - Step 1
document.getElementById("cancelClearBtn").addEventListener("click", () => {
  document.getElementById("clearChatModal").style.display = "none";
});

document.getElementById("confirmClearBtn").addEventListener("click", () => {
  // Close first modal and open second modal
  document.getElementById("clearChatModal").style.display = "none";
  document.getElementById("clearChatFinalModal").style.display = "flex";
});

// Close clear modal on overlay click
document.getElementById("clearChatModal").addEventListener("click", (e) => {
  if (e.target.id === "clearChatModal") {
    document.getElementById("clearChatModal").style.display = "none";
  }
});

// Clear Chat Modal Actions - Step 2 (Final)
document.getElementById("cancelFinalClearBtn").addEventListener("click", () => {
  // Close final modal and back to first modal
  document.getElementById("clearChatFinalModal").style.display = "none";
  document.getElementById("clearChatModal").style.display = "flex";
});

document.getElementById("confirmFinalClearBtn").addEventListener("click", () => {
  // Close modal and show loading
  document.getElementById("clearChatFinalModal").style.display = "none";
  
  // Count messages to determine loading time
  const messageCount = chatBody.children.length;
  const baseTime = 500; // minimum 500ms
  const timePerMessage = 50; // 50ms per message
  const loadingTime = Math.min(baseTime + (messageCount * timePerMessage), 3000); // max 3 seconds
  
  // Show loading modal
  showLoadingModal('Menghapus Pesan', 'Menghapus ' + messageCount + ' pesan...', loadingTime, () => {
    // Clear chat after loading
    chatBody.innerHTML = '';
  });
});

// Close final clear modal on overlay click
document.getElementById("clearChatFinalModal").addEventListener("click", (e) => {
  if (e.target.id === "clearChatFinalModal") {
    document.getElementById("clearChatFinalModal").style.display = "none";
  }
});

// Logout functionality
document.getElementById("logoutBtn").addEventListener("click", (e) => {
  e.preventDefault();
  menuDropdown.classList.remove("show");
  document.getElementById("logoutModal").style.display = "flex";
});

// Logout Modal Actions
document.getElementById("cancelLogoutBtn").addEventListener("click", () => {
  document.getElementById("logoutModal").style.display = "none";
});

document.getElementById("confirmLogoutBtn").addEventListener("click", () => {
  // Close modal and show loading
  document.getElementById("logoutModal").style.display = "none";
  
  // Show loading modal for logout
  showLoadingModal('Logout', 'Mengakhiri sesi Anda...', 1500, () => {
    // Submit logout form after loading
    document.getElementById("logoutForm").submit();
  });
});

// Close logout modal on overlay click
document.getElementById("logoutModal").addEventListener("click", (e) => {
  if (e.target.id === "logoutModal") {
    document.getElementById("logoutModal").style.display = "none";
  }
});

// Loading Modal Function
function showLoadingModal(title, message, duration, callback) {
  const loadingModal = document.getElementById('loadingModal');
  const loadingTitle = document.getElementById('loadingTitle');
  const loadingMessage = document.getElementById('loadingMessage');
  const loadingProgress = document.getElementById('loadingProgress');
  
  // Set text
  loadingTitle.textContent = title;
  loadingMessage.textContent = message;
  
  // Show modal
  loadingModal.style.display = 'flex';
  
  // Animate progress bar
  loadingProgress.style.width = '0%';
  
  const startTime = Date.now();
  const progressInterval = setInterval(() => {
    const elapsed = Date.now() - startTime;
    const progress = Math.min((elapsed / duration) * 100, 100);
    loadingProgress.style.width = progress + '%';
    
    if (progress >= 100) {
      clearInterval(progressInterval);
    }
  }, 50);
  
  // Hide modal and execute callback after duration
  setTimeout(() => {
    clearInterval(progressInterval);
    loadingProgress.style.width = '100%';
    
    setTimeout(() => {
      loadingModal.style.display = 'none';
      if (callback) callback();
    }, 200);
  }, duration);
}
</script>
</body>
</html>
