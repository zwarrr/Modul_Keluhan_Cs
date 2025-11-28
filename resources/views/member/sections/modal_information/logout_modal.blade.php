<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] animate-fadeIn" style="display: none;">
  <div class="bg-white rounded-2xl w-[90%] max-w-[420px] shadow-2xl animate-slideUp overflow-hidden">
    <div class="p-8 pb-5 text-center border-b border-gray-100">
      <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-400 to-red-600 text-white flex items-center justify-center mx-auto mb-4 text-3xl">
        <i class="fa-solid fa-right-from-bracket"></i>
      </div>
      <h3 class="text-xl font-semibold text-gray-800 m-0">Logout Sekarang</h3>
      <p class="text-sm text-gray-600 mt-3 mb-0 font-normal">Apakah Anda yakin ingin logout?</p>
    </div>
    <!--
    <div class="p-5 text-center bg-gray-50">
      <p class="text-sm text-gray-700 m-0 mb-2 leading-relaxed">Anda akan keluar dari akun dan sesi chat akan berakhir.</p>
      <p class="text-xs text-gray-500 m-0">Anda perlu login kembali untuk melanjutkan chat.</p>
    </div> -->
    
    <div class="p-6 pt-4 flex gap-3 bg-white">
      <button type="button" class="flex-1 px-5 py-3 bg-gray-100 text-gray-600 border-0 rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 hover:bg-gray-200 hover:-translate-y-px" id="cancelLogoutBtn">
        <i class="fa-solid fa-times"></i>
        Batal
      </button>
      <button type="button" class="flex-1 px-5 py-3 bg-gradient-to-br from-red-400 to-red-600 text-white border-0 rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-red-500/25 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-red-500/35 active:translate-y-0" id="confirmLogoutBtn">
        <i class="fa-solid fa-right-from-bracket"></i>
        Ya, Logout
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

  .animate-fadeIn {
    animation: fadeIn 0.2s ease;
  }

  .animate-slideUp {
    animation: slideUp 0.3s ease;
  }
</style>
