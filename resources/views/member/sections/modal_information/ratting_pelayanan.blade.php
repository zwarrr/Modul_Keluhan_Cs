<!-- Rating Pelayanan Modal (Member Only) -->
<style>
  @keyframes fadeInScale {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes checkmarkDraw {
    0% {
      stroke-dashoffset: 100;
      opacity: 0;
    }
    50% {
      opacity: 1;
    }
    100% {
      stroke-dashoffset: 0;
      opacity: 1;
    }
  }
  
  @keyframes successPulse {
    0%, 100% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(107, 114, 128, 0.4);
    }
    50% {
      transform: scale(1.05);
      box-shadow: 0 0 0 10px rgba(107, 114, 128, 0);
    }
  }
  
  #ratingPelayananModal.show-modal .rating-modal-content {
    animation: fadeInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  
  #ratingPelayananModal.show-modal .rating-modal-overlay {
    animation: fadeIn 0.3s ease-out;
  }
  
  .success-icon-check {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
  }
  
  .success-state-active .success-icon-check {
    animation: checkmarkDraw 0.6s ease-out 0.2s forwards;
  }
  
  .success-state-active .success-icon-circle {
    animation: successPulse 1.5s ease-in-out;
  }
</style>

<div id="ratingPelayananModal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
  <div class="rating-modal-overlay absolute inset-0 bg-black/50" data-rating-overlay></div>

  <div class="rating-modal-content relative w-[92%] max-w-md rounded-2xl bg-white p-6 shadow-xl">
    <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 transition-colors" data-rating-close aria-label="Close">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>
    </button>

    <!-- Step: Rating -->
    <div data-rating-step="rate" class="py-2">
      <h3 class="text-2xl font-bold tracking-tight text-gray-900">Rate Pelayanan Kami!</h3>
      <p class="mt-3 text-base text-gray-600 leading-relaxed">Bantu kami meningkatkan kualitas pelayanan dengan memberikan rating di bawah ini.</p>

      <div class="mt-7 flex items-center justify-center gap-2" role="radiogroup" aria-label="Rating">
        @for($i = 1; $i <= 5; $i++)
          <button type="button" class="rating-star h-12 w-12 rounded-lg hover:bg-gray-50 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95" data-rating-value="{{ $i }}" aria-label="{{ $i }} star">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8 transition-all duration-200">
              <path class="rating-star-path" fill="#D1D5DB" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
          </button>
        @endfor
      </div>

      <div class="mt-7 flex items-center gap-3">
        <button type="button" class="flex-1 rounded-xl border-2 border-gray-300 bg-white px-5 py-3.5 text-base font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]" data-rating-cancel>Batal</button>
        <button type="button" class="flex-1 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 px-5 py-3.5 text-base font-semibold text-white shadow-lg shadow-gray-500/30 hover:shadow-gray-500/40 hover:from-gray-700 hover:to-gray-800 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-gray-500/30 disabled:transform-none transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]" data-rating-submit disabled>Kirim Rating</button>
      </div>

      <p class="mt-4 text-sm text-red-500 hidden" data-rating-error></p>
    </div>

    <!-- Step: Success -->
    <div data-rating-step="success" class="hidden text-center py-2">
      <div class="success-icon-circle mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-gray-50 to-gray-100 ring-4 ring-gray-100 ring-offset-4 ring-offset-white mb-1">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-600 success-icon-check">
          <path d="M20 6 9 17l-5-5" />
        </svg>
      </div>

      <h3 class="mt-6 text-2xl font-bold tracking-tight text-gray-900">Rating Terkirim!</h3>
      <p class="mt-3 text-base leading-relaxed text-gray-600 px-2">Terima kasih atas feedback kamu. Masukan ini sangat membantu kami dalam meningkatkan kualitas pelayanan.</p>

      <div class="mt-8">
        <button type="button" class="w-full rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 px-5 py-3.5 text-base font-semibold text-white shadow-lg shadow-gray-500/30 hover:shadow-gray-500/40 hover:from-gray-700 hover:to-gray-800 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]" data-rating-ok>
          Kembali ke Chat
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const modal = document.getElementById('ratingPelayananModal');
  if (!modal) return;

  const overlay = modal.querySelector('[data-rating-overlay]');
  const closeBtn = modal.querySelector('[data-rating-close]');
  const cancelBtn = modal.querySelector('[data-rating-cancel]');
  const submitBtn = modal.querySelector('[data-rating-submit]');
  const okBtn = modal.querySelector('[data-rating-ok]');
  const errEl = modal.querySelector('[data-rating-error]');
  const stars = Array.from(modal.querySelectorAll('[data-rating-value]'));
  const stepRate = modal.querySelector('[data-rating-step="rate"]');
  const stepSuccess = modal.querySelector('[data-rating-step="success"]');

  let selected = 0;
  let currentSesiId = null;

  function setError(msg) {
    if (!errEl) return;
    if (!msg) {
      errEl.classList.add('hidden');
      errEl.textContent = '';
      return;
    }
    errEl.textContent = msg;
    errEl.classList.remove('hidden');
  }

  function paintStars(hoverValue = 0) {
    const v = hoverValue || selected;
    stars.forEach((btn) => {
      const val = Number(btn.getAttribute('data-rating-value') || 0);
      const path = btn.querySelector('.rating-star-path');
      if (!path) return;
      path.setAttribute('fill', val <= v ? '#F59E0B' : '#D1D5DB');
    });
  }

  function open(sesiId) {
    currentSesiId = sesiId ? String(sesiId) : null;
    selected = 0;
    paintStars(0);
    setError('');
    if (submitBtn) submitBtn.disabled = true;
    if (stepSuccess) stepSuccess.classList.add('hidden');
    if (stepRate) stepRate.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'show-modal');
  }

  function close() {
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'show-modal');
    setError('');
    // Remove success-state-active when closing
    if (stepSuccess) stepSuccess.classList.remove('success-state-active');
  }

  stars.forEach((btn) => {
    btn.addEventListener('mouseenter', () => paintStars(Number(btn.getAttribute('data-rating-value') || 0)));
    btn.addEventListener('mouseleave', () => paintStars(0));
    btn.addEventListener('click', () => {
      selected = Number(btn.getAttribute('data-rating-value') || 0);
      paintStars(0);
      if (submitBtn) submitBtn.disabled = !(selected >= 1 && selected <= 5);
    });
  });

  async function submit() {
    if (!currentSesiId) {
      setError('Sesi tidak valid.');
      return;
    }
    if (!(selected >= 1 && selected <= 5)) {
      setError('Pilih rating 1 sampai 5.');
      return;
    }

    setError('');
    if (submitBtn) submitBtn.disabled = true;

    try {
      const res = await fetch("{{ route('chat.ratingPelayanan') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ session_id: currentSesiId, rating_pelayanan: selected })
      });
      const data = await res.json().catch(() => ({}));

      if (!res.ok || data.success !== true) {
        throw new Error(data.message || 'Gagal menyimpan rating');
      }

      // Prevent repeated prompts in the same browser for this session
      try { sessionStorage.setItem('rated_pelayanan_' + currentSesiId, '1'); } catch (_) {}

      // Show success state with animation
      if (stepRate) stepRate.classList.add('hidden');
      if (stepSuccess) {
        stepSuccess.classList.remove('hidden');
        // Trigger animation after a brief delay to ensure DOM update
        setTimeout(() => stepSuccess.classList.add('success-state-active'), 50);
      }
    } catch (e) {
      setError(e && e.message ? e.message : 'Gagal menyimpan rating');
      if (submitBtn) submitBtn.disabled = false;
    }
  }

  if (submitBtn) submitBtn.addEventListener('click', submit);

  if (okBtn) okBtn.addEventListener('click', close);

  function cancel() {
    // User can skip rating; don't persist to DB
    close();
  }

  if (overlay) overlay.addEventListener('click', cancel);
  if (cancelBtn) cancelBtn.addEventListener('click', cancel);
  if (closeBtn) closeBtn.addEventListener('click', cancel);

  // Expose global so WebSocket/end-session can trigger it
  window.showRatingPelayananModal = function (sesiId) {
    if (!sesiId) return;
    try {
      if (sessionStorage.getItem('rated_pelayanan_' + String(sesiId)) === '1') return;
    } catch (_) {}
    open(String(sesiId));
  };
})();
</script>
