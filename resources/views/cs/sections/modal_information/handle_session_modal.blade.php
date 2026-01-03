<!-- Handle Session Modal -->
<div id="handleSessionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 30px; max-width: 450px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3); position: relative; top: 50%; transform: translateY(-50%);">
    <div style="text-align: center; margin-bottom: 20px;">
      <i class="fa-solid fa-headset" style="font-size: 56px; color: #2563eb; margin-bottom: 15px;"></i>
      <h3 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 10px;">Tangani Chat Ini?</h3>
      <p style="color: #6b7280; font-size: 15px; line-height: 1.5;">Apakah Anda ingin menangani chat dari member ini?</p>
    </div>
    
    <div style="display: flex; gap: 12px; margin-top: 25px;">
      <button id="cancelHandleBtn" style="flex: 1; padding: 12px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 8px; font-weight: 500; font-size: 15px; cursor: pointer; transition: all 0.2s;">
        Tidak
      </button>
      <button id="confirmHandleBtn" style="flex: 1; padding: 12px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 15px; cursor: pointer; transition: all 0.2s;">
        Tangani
      </button>
    </div>
  </div>
</div>

<style>
  #cancelHandleBtn:hover {
    background: #d1d5db !important;
  }
  #confirmHandleBtn:hover {
    background: #1d4ed8 !important;
  }
</style>
