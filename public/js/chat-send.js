// ============================================
// SEND MESSAGE FUNCTIONALITY
// ============================================

// Send message function
function sendMessage() {
  const text = input.value.trim();
  
  // Must have text or file
  if(!text && !selectedFile) return;
  
  // Disable send button to prevent double-send
  sendBtn.disabled = true;
  
  // Create FormData
  const formData = new FormData();
  if (text) formData.append('message', text);
  if (selectedFile) formData.append('file', selectedFile);
  
  // TIDAK pakai optimistic UI, tunggu WebSocket broadcast dari server
  // Karena member juga menerima broadcast sendiri (server tidak pakai toOthers())
  
  // Clear input and file
  input.value = "";
  selectedFile = null;
  filePreview.classList.remove('show');
  autoResize();
  
  // Kirim ke API
  fetch(chatSendRoute, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken
    },
    body: formData
  })
  .then(response => {
    return response.json();
  })
  .then(data => {
    if (data.success) {
      // Increment file counter after successful upload
      fileCounter++;
      
      // Update sesi ID untuk WebSocket subscription
      if (typeof window.updateSesiIdAfterSend === 'function') {
        window.updateSesiIdAfterSend();
      }

      // If embedded in split view, ask parent to refresh sessions list
      try {
        if (window.parent && window.parent !== window) {
          window.parent.postMessage({ type: 'chat:refreshSessions' }, window.location.origin);
        }
      } catch (_) {
        // ignore
      }
    } else {
      createBubble('Maaf, terjadi kesalahan. Silakan coba lagi.', false);
    }
    
    // Re-enable send button
    sendBtn.disabled = false;
  })
  .catch(error => {
    console.error('Upload error:', error);
    createBubble('Maaf, sistem sedang mengalami gangguan.', false);
    
    // Re-enable send button
    sendBtn.disabled = false;
  });
}

// Event listeners for sending
sendBtn.addEventListener("click", sendMessage);
input.addEventListener("keydown", e => { 
  if(e.key==="Enter" && !e.shiftKey){ 
    e.preventDefault(); 
    sendMessage(); 
  } 
});

// ============================================
// TYPING INDICATOR
// ============================================

let memberTypingTimeout;

// Send typing status to server
input.addEventListener("input", () => {
  // Don't send typing if session is closed
  if (window.sessionClosed) {
    return;
  }
  
  clearTimeout(memberTypingTimeout);
  
  // Send typing status
  fetch(chatTypingRoute, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Content-Type': 'application/json'
    }
  });
  
  // Clear typing after 3 seconds of no input
  memberTypingTimeout = setTimeout(() => {
    // Typing stopped
  }, 3000);
});

// Show typing indicator
function showTypingIndicator() {
  // Cek apakah typing indicator sudah ada
  const existing = document.getElementById("typingIndicator");
  if (existing) {
    return; // Sudah ada, jangan buat lagi
  }
  
  const wrapper = document.createElement("div");
  wrapper.className = "message-wrapper other typing-indicator-wrapper";
  wrapper.id = "typingIndicator";
  
  const container = document.createElement("div");
  const bubble = document.createElement("div");
  bubble.className = "bubble other";
  bubble.innerHTML = '<div style="display:flex;gap:4px;align-items:center;"><span style="width:8px;height:8px;background:#999;border-radius:50%;animation:bounce 1.4s infinite ease-in-out both;"></span><span style="width:8px;height:8px;background:#999;border-radius:50%;animation:bounce 1.4s infinite ease-in-out both;animation-delay:0.16s;"></span><span style="width:8px;height:8px;background:#999;border-radius:50%;animation:bounce 1.4s infinite ease-in-out both;animation-delay:0.32s;"></span></div>';
  
  // Cek apakah style sudah ada
  if (!document.getElementById('typing-bounce-style')) {
    const style = document.createElement('style');
    style.id = 'typing-bounce-style';
    style.textContent = '@keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }';
    document.head.appendChild(style);
  }
  
  container.appendChild(bubble);
  wrapper.appendChild(container);
  chatBody.appendChild(wrapper);
  scrollBottom();
}

// Remove typing indicator
function removeTypingIndicator() {
  const indicator = document.getElementById("typingIndicator");
  if (indicator) {
    indicator.remove();
  }
}
