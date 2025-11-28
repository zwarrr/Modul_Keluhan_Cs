<!-- Login Failed Modal -->
<div id="loginFailedModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[10001] animate-fadeIn" style="display: none;">
  <div class="bg-white rounded-2xl w-[90%] max-w-[380px] shadow-2xl animate-slideUp overflow-hidden">
    <div class="p-8 pb-5 text-center border-b border-gray-100">
      <div class="w-20 h-20 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 text-white flex items-center justify-center mx-auto mb-4 text-4xl">
        <i class="fa-solid fa-times"></i>
      </div>
      <h3 class="text-xl font-semibold text-gray-800 m-0">Login Gagal!</h3>
      <p class="text-sm text-gray-600 mt-3 mb-0 font-normal">Terjadi kesalahan saat login</p>
    </div>
    
    <div class="p-5 text-center bg-gray-50">
      <p class="text-sm text-gray-700 m-0 mb-2 leading-relaxed" id="loginFailedMessage">ID yang Anda masukkan salah atau tidak terdaftar.</p>
      <p class="text-xs text-gray-500 m-0">Silakan periksa kembali ID Anda dan coba lagi.</p>
    </div>
    
    <div class="p-6 pt-4 flex gap-3 bg-white">
      <button type="button" class="flex-1 px-5 py-3 bg-gradient-to-br from-gray-400 to-gray-600 text-white border-0 rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-gray-500/25 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-gray-500/35" id="closeLoginFailedBtn">
        <i class="fa-solid fa-rotate-left"></i>
        Coba Lagi
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
