<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[10000]" style="display: none;">
  <div class="bg-white rounded-2xl w-[90%] max-w-[320px] shadow-2xl overflow-hidden">
    <div class="p-8 text-center">
      <!-- Spinner Animation -->
      <div class="relative w-20 h-20 mx-auto mb-6">
        <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-transparent border-t-red-500 rounded-full animate-spin"></div>
      </div>
      
      <!-- Loading Text -->
      <h3 class="text-lg font-semibold text-gray-800 mb-2" id="loadingTitle">Memproses...</h3>
      <p class="text-sm text-gray-600" id="loadingMessage">Mohon tunggu sebentar</p>
      
      <!-- Progress Bar (Optional) -->
      <div class="mt-4 w-full bg-gray-200 rounded-full h-2 overflow-hidden">
        <div id="loadingProgress" class="bg-gradient-to-r from-red-400 to-red-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
      </div>
    </div>
  </div>
</div>

<style>
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  .animate-spin {
    animation: spin 1s linear infinite;
  }
</style>
