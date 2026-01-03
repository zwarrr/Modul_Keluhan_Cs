// ============================================
// MESSAGE RENDERING AND BUBBLES
// ============================================

const renderedMessageIds = new Set();

// Create message bubble
function createBubble(text, self, filePath = null, fileType = null, status = 'sent', isGreeting = false, messageTimestamp = null, shouldScroll = true, showDateBadge = true, dateBadgeMode = 'relative', messageId = null) {
  addDateBadgeIfNeeded();
  
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
  
  // Add text if provided
  if (text && text.trim()) {
    const textNode = document.createElement("div");
    let cleanText = text
      .replace(/\\n{3,}/g, '\\n\\n')
      .replace(/ {2,}/g, ' ')
      .trim();
    textNode.textContent = cleanText;
    textNode.style.textAlign = "justify";
    textNode.style.textJustify = "inter-word";
    bubble.appendChild(textNode);
  }
  
  // Add file/image if exists
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

  const timestamp = document.createElement("div");
  timestamp.className = "timestamp " + (displaySelf ? "self" : "");
  timestamp.textContent = getTime();
  if (displaySelf) {
    const icon = document.createElement("i");
    icon.className = "fa-solid fa-check-double";
    icon.style.marginLeft = "4px";
    icon.style.color = status === 'read' ? '#4fc3f7' : '#999';
    timestamp.appendChild(icon);
  }

  container.appendChild(bubble);
  container.appendChild(timestamp);
  wrapper.appendChild(container);
  chatBody.appendChild(wrapper);
  scrollBottom();
}

// ============================================
// MESSAGE POLLING AND RENDERING
// ============================================

let lastMessageCount = 0;
let currentlyShowingTyping = false;

// Poll messages from server
function pollMessages() {
  fetch(chatMessagesRoute, {
    method: 'GET',
    headers: {
      'X-CSRF-TOKEN': csrfToken
    }
  })
  .then(response => response.json())
  .then(data => {
    console.log('Polling data:', data);
    if (!data.success) return;
    
    if (data.messages !== undefined) {
      console.log('Messages count:', data.messages.length, 'Last count:', lastMessageCount);
      // Clear chat body jika ada pesan baru yang berbeda ATAU jika lastMessageCount berubah
      if (data.messages.length !== lastMessageCount) {
        console.log('Rendering messages...');
        chatBody.innerHTML = '';
        lastDateBadge = null;
        renderedMessageIds.clear();
        
        // Render semua pesan
        data.messages.forEach((msg, index) => {
          console.log('Rendering message', index, ':', msg);
          // Add date badge based on message date
          const msgDate = new Date(msg.date);
          const dateStr = getDateString(msgDate);
          
          if (lastDateBadge !== dateStr) {
            const dateBadge = document.createElement('div');
            dateBadge.className = 'date-badge';
            dateBadge.setAttribute('data-date', dateStr);
            
            const span = document.createElement('span');
            span.textContent = formatDateBadge(msgDate);
            
            dateBadge.appendChild(span);
            chatBody.appendChild(dateBadge);
            
            lastDateBadge = dateStr;
          }
          
          // Greeting message selalu ditampilkan sebagai pesan dari CS
          const displaySelf = msg.is_greeting ? false : msg.self;
          
          // Create message bubble
          const wrapper = document.createElement("div");
          wrapper.className = "message-wrapper " + (displaySelf ? "self" : "other");

          const container = document.createElement("div");
          const bubble = document.createElement("div");
          bubble.className = "bubble " + (displaySelf ? "self" : "other");
          
          // Add text if exists
          if (msg.text && msg.text.trim()) {
            const textNode = document.createElement("div");
            textNode.style.textAlign = "justify";
            textNode.style.textJustify = "inter-word";
            
            let cleanText = msg.text
              .replace(/\\n{3,}/g, '\\n\\n')
              .replace(/ {2,}/g, ' ')
              .trim();
            textNode.textContent = cleanText;
            bubble.appendChild(textNode);
          }
          
          // Add file/image if exists
          if (msg.file_path) {
            if (msg.file_type === 'image') {
              const img = document.createElement('img');
              img.src = msg.file_path;
              img.className = 'message-image';
              img.alt = 'Image';
              img.onclick = () => window.open(msg.file_path, '_blank');
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
              const name = msg.file_path.split('/').pop();
              fileName.textContent = name;
              
              const link = document.createElement('a');
              link.href = msg.file_path;
              link.target = '_blank';
              link.download = name;
              
              fileInfo.appendChild(fileName);
              fileDiv.appendChild(fileIcon);
              fileDiv.appendChild(fileInfo);
              link.appendChild(fileDiv);
              bubble.appendChild(link);
            }
          }

          const timestamp = document.createElement("div");
          timestamp.className = "timestamp " + (displaySelf ? "self" : "");
          timestamp.textContent = msg.time;
          if (displaySelf) {
            const icon = document.createElement("i");
            icon.className = "fa-solid fa-check-double";
            icon.style.marginLeft = "4px";
            // Abu-abu jika 'sent', biru jika 'read'
            icon.style.color = msg.status === 'read' ? "#4fc3f7" : "#999";
            timestamp.appendChild(icon);
          }

          container.appendChild(bubble);
          container.appendChild(timestamp);
          wrapper.appendChild(container);
          chatBody.appendChild(wrapper);
        });
        
        lastMessageCount = data.messages.length;
        scrollBottom();
      }
    }
    
    // Check if session is closed AFTER rendering messages
    if (data.session_closed && !sessionClosed) {
      sessionClosed = true;
      // Don't disable input, let user try to type
      // But keep the placeholder as normal
      input.placeholder = "Ketik pesan...";
      // Show modal to inform user
      document.getElementById("sessionClosedModal").style.display = "flex";
    }
      
    // Handle typing indicator (only if session is not closed)
    if (!sessionClosed) {
      if (data.cs_typing && !currentlyShowingTyping) {
        showTypingIndicator();
        currentlyShowingTyping = true;
      } else if (!data.cs_typing && currentlyShowingTyping) {
        removeTypingIndicator();
        currentlyShowingTyping = false;
      }
    }
  })
  .catch(error => {
    console.error('Polling error:', error);
  });
}

// Start polling every 3 seconds
setInterval(pollMessages, 3000);

// Load messages on page load
pollMessages();
