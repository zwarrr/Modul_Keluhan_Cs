<!-- End Session Confirmation Modal -->
<div id="endSessionConfirmModal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
  <div class="absolute inset-0 bg-black/50" data-end-session-overlay></div>
  
  <div class="relative bg-white rounded-2xl w-[90%] max-w-[420px] shadow-2xl overflow-hidden animate-slideUp">
    <div class="p-8 pb-5 text-center border-b border-gray-100">
      <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center mx-auto mb-4 text-3xl">
        <i class="fa-solid fa-circle-question"></i>
      </div>
      <h3 class="text-xl font-semibold text-gray-800 m-0">Konfirmasi Akhiri Sesi</h3>
      <p class="text-sm text-gray-600 mt-3 mb-0 font-normal">Apakah Anda yakin ingin mengakhiri sesi chat ini? Setelah sesi ditutup, Anda akan diminta memberikan rating pelayanan.</p>
    </div>
    
    <div class="p-6 pt-4 flex gap-3 bg-white">
      <button type="button" class="flex-1 px-5 py-3 bg-gray-100 text-gray-600 border-0 rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 hover:bg-gray-200 hover:-translate-y-px" id="cancelEndSessionBtn">
        <i class="fa-solid fa-times"></i>
        Batal
      </button>
      <button type="button" class="flex-1 px-5 py-3 bg-gradient-to-br from-red-400 to-red-600 text-white border-0 rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-red-500/25 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-red-500/35 active:translate-y-0" id="confirmEndSessionBtn">
        <i class="fa-solid fa-circle-xmark"></i>
        Ya, Akhiri Sesi
      </button>
    </div>
  </div>
</div>

<style>
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  @keyframes slideUp {
    from { 
      opacity: 0;
      transform: translateY(20px);
    }
    to { 
      opacity: 1;
      transform: translateY(0);
    }
  }

  #endSessionConfirmModal.show-modal .animate-slideUp {
    animation: slideUp 0.3s ease;
  }
  
  #endSessionConfirmModal.show-modal {
    animation: fadeIn 0.2s ease;
  }
</style>

<script>
// Check if in embed mode
const isEmbedMode = window.self !== window.top;

// Show end session confirmation modal
window.showEndSessionConfirmModal = function() {
  const modal = document.getElementById('endSessionConfirmModal');
  if (modal) {
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'show-modal');
    
    // If in iframe, tell parent to show overlay
    if (isEmbedMode) {
      try {
        window.parent.postMessage({ type: 'chat:showModalOverlay' }, window.location.origin);
      } catch (e) {}
    }
  }
};

// Hide end session confirmation modal
window.hideEndSessionConfirmModal = function() {
  const modal = document.getElementById('endSessionConfirmModal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'show-modal');
    
    // If in iframe, tell parent to hide overlay
    if (isEmbedMode) {
      try {
        window.parent.postMessage({ type: 'chat:hideModalOverlay' }, window.location.origin);
      } catch (e) {}
    }
  }
};

// Cancel button and overlay click
document.addEventListener('DOMContentLoaded', () => {
  const cancelBtn = document.getElementById('cancelEndSessionBtn');
  const confirmBtn = document.getElementById('confirmEndSessionBtn');
  const overlay = document.querySelector('[data-end-session-overlay]');
  
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      window.hideEndSessionConfirmModal();
    });
  }

  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      window.hideEndSessionConfirmModal();
      
      // Call the actuallyEndSession function
      // In desktop mode, modal is in parent but actuallyEndSession is in iframe
      // In mobile mode, both are in the same window
      if (window.actuallyEndSession) {
        // Mobile mode - function exists in same window
        window.actuallyEndSession();
      } else {
        // Desktop mode - need to tell iframe to execute
        const iframe = document.querySelector('iframe[src*="chatroom"]');
        if (iframe && iframe.contentWindow) {
          try {
            iframe.contentWindow.postMessage({ type: 'chat:executeEndSession' }, window.location.origin);
          } catch (e) {}
        }
      }
    });
  }
  
  // Click overlay to close
  if (overlay) {
    overlay.addEventListener('click', () => {
      window.hideEndSessionConfirmModal();
    });
  }
});
</script>
