<!-- Session Closed Modal -->
<div id="sessionClosedModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; border-radius:12px; padding:24px; max-width:400px; width:90%; box-shadow:0 4px 20px rgba(0,0,0,0.15);">
    <div style="text-align:center; margin-bottom:20px;">
      <div style="width:60px; height:60px; background:#ff6b6b; border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center;">
        <i class="fa-solid fa-circle-xmark" style="font-size:32px; color:white;"></i>
      </div>
      <h3 style="margin:0 0 8px 0; color:#333; font-size:20px; font-weight:600;">Sesi Chat Telah Berakhir</h3>
      <p style="margin:0; color:#666; font-size:14px;">Sesi chat Anda telah ditutup oleh Customer Service. Silakan mulai chat baru jika ingin melanjutkan percakapan.</p>
    </div>
    
    <div style="display:flex; gap:12px; margin-top:24px;">
      <button id="closeSessionModalBtn" style="flex:1; padding:12px; border:1px solid #ddd; background:white; color:#333; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; transition:all 0.2s;">
        Tutup
      </button>
      <button id="newChatBtn" style="flex:1; padding:12px; border:none; background:#282828; color:white; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; transition:all 0.2s;">
        <i class="fa-solid fa-plus" style="margin-right:6px;"></i>
        Chat Baru
      </button>
    </div>
  </div>
</div>

<style>
#sessionClosedModal button:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#closeSessionModalBtn:hover {
  background: #f5f5f5;
  border-color: #ccc;
}

#newChatBtn:hover {
  background: #1a1a1a;
}
</style>
