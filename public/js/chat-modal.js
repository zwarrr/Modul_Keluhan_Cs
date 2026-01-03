// ============================================
// MODAL HANDLERS
// ============================================

const logoutBtn = document.getElementById("logoutBtn");
const logoutModal = document.getElementById("logoutModal");
const confirmLogoutBtn = document.getElementById("confirmLogoutBtn");
const cancelLogoutBtn = document.getElementById("cancelLogoutBtn");
const logoutForm = document.getElementById("logoutForm");

// Logout button handler
if (logoutBtn) {
  logoutBtn.addEventListener("click", () => {
    if (logoutModal) {
      logoutModal.style.display = "flex";
    } else if (logoutForm) {
      // Fallback if modal markup isn't on this page
      logoutForm.submit();
    }

    if (typeof menuDropdown !== "undefined" && menuDropdown) {
      menuDropdown.classList.remove("show");
    }
  });
}

// Confirm logout
if (confirmLogoutBtn && logoutForm) {
  confirmLogoutBtn.addEventListener("click", () => {
    logoutForm.submit();
  });
}

// Cancel logout
if (cancelLogoutBtn && logoutModal) {
  cancelLogoutBtn.addEventListener("click", () => {
    logoutModal.style.display = "none";
  });
}

// Close logout modal on overlay click
if (logoutModal) {
  logoutModal.addEventListener("click", (e) => {
    if (e.target.id === "logoutModal") {
      logoutModal.style.display = "none";
    }
  });
}

// ============================================
// LOADING MODAL FUNCTION
// ============================================

function showLoadingModal(title, message, duration, callback) {
  const loadingModal = document.getElementById('loadingModal');
  const loadingTitle = document.getElementById('loadingTitle');
  const loadingMessage = document.getElementById('loadingMessage');
  
  loadingTitle.textContent = title;
  loadingMessage.textContent = message;
  loadingModal.style.display = 'flex';
  
  if (duration && callback) {
    setTimeout(() => {
      loadingModal.style.display = 'none';
      callback();
    }, duration);
  }
}

function hideLoadingModal() {
  const loadingModal = document.getElementById('loadingModal');
  loadingModal.style.display = 'none';
}
