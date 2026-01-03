// ============================================
// CHAT UI UTILITIES
// ============================================

// Auto resize textarea
function autoResize() {
  input.style.height = "auto";
  input.style.height = input.scrollHeight + "px";
}

// Scroll to bottom
function scrollBottom() { 
  setTimeout(() => { 
    chatBody.scrollTop = chatBody.scrollHeight; 
  }, 100); 
}

// Get current time (HH:mm) - accepts optional timestamp
function getTime(timestamp = null) {
  const d = timestamp ? new Date(timestamp) : new Date();
  return d.getHours().toString().padStart(2,"0") + ":" + d.getMinutes().toString().padStart(2,"0");
}

// Get date string for grouping (YYYY-MM-DD)
function getDateString(date) {
  return date.getFullYear() + '-' + 
         (date.getMonth() + 1).toString().padStart(2, '0') + '-' + 
         date.getDate().toString().padStart(2, '0');
}

// Format date badge
// - mode: 'relative' => Today/Yesterday when applicable
// - mode: 'absolute' => DD-MM-YYYY
function formatDateBadge(date, mode = 'relative') {
  const dd = String(date.getDate()).padStart(2, '0');
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const yyyy = String(date.getFullYear());
  const absolute = `${dd}-${mm}-${yyyy}`;

  if (mode !== 'relative') {
    return absolute;
  }

  const now = new Date();
  const todayStr = getDateString(now);
  const msgStr = getDateString(date);
  if (msgStr === todayStr) return 'Today';

  const yesterday = new Date(now);
  yesterday.setDate(now.getDate() - 1);
  const yesterdayStr = getDateString(yesterday);
  if (msgStr === yesterdayStr) return 'Yesterday';

  return absolute;
}

// Track last date badge shown
let lastDateBadge = null;

// Expose reset for full re-render scenarios
window.__resetChatDateBadges = function () {
  lastDateBadge = null;
};

// Create date badge if needed - accepts message timestamp
function addDateBadgeIfNeeded(messageTimestamp = null, mode = 'relative') {
  // Jika tidak ada timestamp, gunakan waktu sekarang
  const messageDate = messageTimestamp ? new Date(messageTimestamp) : new Date();
  const currentDateStr = getDateString(messageDate);
  
  if (lastDateBadge !== currentDateStr) {
    const dateBadge = document.createElement('div');
    dateBadge.className = 'date-badge';
    dateBadge.setAttribute('data-date', currentDateStr);
    
    const span = document.createElement('span');
    span.textContent = formatDateBadge(messageDate, mode);
    
    dateBadge.appendChild(span);
    chatBody.appendChild(dateBadge);
    
    lastDateBadge = currentDateStr;
  }
}

// ============================================
// EMOJI PICKER
// ============================================

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

// ============================================
// MENU DROPDOWN
// ============================================

ellipsisBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  menuDropdown.classList.toggle("show");
});

document.addEventListener("click", e => {
  if(!menuDropdown.contains(e.target) && e.target !== ellipsisBtn){
    menuDropdown.classList.remove("show");
  }
});

// Input auto-resize event
input.addEventListener("input", autoResize);

// ============================================
// MESSAGE BUBBLE CREATION
// ============================================

// Create session badge (shown only for closed sessions)
function createSessionBadge(label) {
  const badge = document.createElement('div');
  badge.className = 'date-badge';
  const span = document.createElement('span');
  span.textContent = label;
  badge.appendChild(span);
  chatBody.appendChild(badge);
}

// Create message bubble
function createBubble(text, self, filePath = null, fileType = null, status = 'sent', isGreeting = false, messageTimestamp = null, shouldScroll = true, showDateBadge = true, dateBadgeMode = 'relative', messageId = null) {
  if (showDateBadge) {
    addDateBadgeIfNeeded(messageTimestamp, dateBadgeMode);
  }
  
  // Greeting message selalu ditampilkan sebagai pesan dari CS (other)
  const displaySelf = isGreeting ? false : self;
  
  const wrapper = document.createElement("div");
  wrapper.className = "message-wrapper " + (displaySelf ? "self" : "other");
  
  // Tambahkan data-message-id untuk tracking
  if (messageId) {
    wrapper.setAttribute('data-message-id', messageId);
  }

  const container = document.createElement("div");
  const bubble = document.createElement("div");
  bubble.className = "bubble " + (displaySelf ? "self" : "other");
  
  // Add file/image first if exists (gambar di atas)
  if (filePath) {
    if (fileType === 'image') {
      const img = document.createElement('img');
      img.src = filePath;
      img.className = 'message-image';
      img.alt = 'Image';
      img.onclick = () => window.open(filePath, '_blank');
      bubble.appendChild(img);
    } else {
      const fileDiv = document.createElement('div');
      fileDiv.className = 'file-attachment';
      
      const fileIcon = document.createElement('div');
      fileIcon.className = 'file-icon';
      fileIcon.innerHTML = '<i class="fa-solid fa-file"></i>';
      
      const fileInfo = document.createElement('div');
      fileInfo.className = 'file-info';
      
      const fileName = document.createElement('div');
      fileName.className = 'file-name';
      const name = filePath.split('/').pop();
      fileName.textContent = name;
      
      const link = document.createElement('a');
      link.href = filePath;
      link.target = '_blank';
      link.download = name;
      
      fileInfo.appendChild(fileName);
      fileDiv.appendChild(fileIcon);
      fileDiv.appendChild(fileInfo);
      link.appendChild(fileDiv);
      bubble.appendChild(link);
    }
  }
  
  // Add text below image if provided (teks di bawah)
  if (text && text.trim()) {
    const textNode = document.createElement("div");
    let cleanText = text
      .replace(/\\n{3,}/g, '\\n\\n')
      .replace(/ {2,}/g, ' ')
      .trim();
    textNode.textContent = cleanText;
    textNode.style.textAlign = "justify";
    textNode.style.textJustify = "inter-word";
    textNode.style.marginTop = filePath ? "8px" : "0"; // Tambah spacing jika ada gambar
    bubble.appendChild(textNode);
  }

  const timestampDiv = document.createElement("div");
  timestampDiv.className = "timestamp " + (displaySelf ? "self" : "");
  timestampDiv.textContent = getTime(messageTimestamp);
  if (displaySelf) {
    const icon = document.createElement("i");
    icon.className = "fa-solid fa-check-double";
    icon.style.marginLeft = "4px";
    icon.style.color = status === 'read' ? '#4fc3f7' : '#999';
    timestampDiv.appendChild(icon);
  }

  container.appendChild(bubble);
  container.appendChild(timestampDiv);
  wrapper.appendChild(container);
  chatBody.appendChild(wrapper);
  if (shouldScroll) {
    scrollBottom();
  }
}

