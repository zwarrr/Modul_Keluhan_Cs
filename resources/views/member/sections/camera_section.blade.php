<!-- Camera Modal -->
<div id="cameraModal" class="hidden fixed inset-0 bg-black/95 z-[1000] flex flex-col">
  <video id="cameraVideo" autoplay playsinline class="w-full h-[calc(100%-120px)] object-cover bg-black"></video>
  <div class="flex justify-around items-center p-5 bg-[#1f1f1f]">
    <!-- Close Button -->
    <button id="closeCameraBtn" 
            class="w-[60px] h-[60px] rounded-full flex items-center justify-center cursor-pointer text-2xl text-white transition-all duration-200 bg-red-500/30 border-[3px] border-red-500 backdrop-blur-md hover:scale-110 hover:bg-red-500/50" 
            title="Close Camera">
      <i class="fa-solid fa-times"></i>
    </button>
    
    <!-- Capture Button with Ring -->
    <button id="captureBtn" 
            class="relative w-[70px] h-[70px] rounded-full flex items-center justify-center cursor-pointer text-2xl text-white transition-all duration-200 bg-transparent border-[5px] border-white hover:scale-110" 
            title="Capture Photo">
      <div class="absolute w-[55px] h-[55px] bg-white rounded-full -z-10"></div>
      <i class="fa-solid fa-camera"></i>
    </button>
    
    <!-- Switch Camera Button -->
    <button id="switchCameraBtn" 
            class="w-[60px] h-[60px] rounded-full flex items-center justify-center cursor-pointer text-2xl text-white transition-all duration-200 bg-blue-500/30 border-[3px] border-blue-500 backdrop-blur-md hover:scale-110 hover:bg-blue-500/50" 
            title="Switch Camera">
      <i class="fa-solid fa-rotate"></i>
    </button>
  </div>
  <canvas id="cameraCanvas" class="hidden"></canvas>
</div>

<style>
  #cameraModal.active { display: flex; }
  #cameraVideo.mirror { transform: scaleX(-1); }
</style>

<script>
// Camera elements
const cameraModal = document.getElementById("cameraModal");
const cameraVideo = document.getElementById("cameraVideo");
const cameraCanvas = document.getElementById("cameraCanvas");
const captureBtn = document.getElementById("captureBtn");
const closeCameraBtn = document.getElementById("closeCameraBtn");
const switchCameraBtn = document.getElementById("switchCameraBtn");

let currentStream = null;
let facingMode = "user"; // "user" untuk depan, "environment" untuk belakang

// Start camera function
async function startCamera() {
  try {
    if (currentStream) {
      currentStream.getTracks().forEach(track => track.stop());
    }
    
    const constraints = {
      video: {
        facingMode: facingMode,
        width: { ideal: 1280 },
        height: { ideal: 720 }
      },
      audio: false
    };
    
    currentStream = await navigator.mediaDevices.getUserMedia(constraints);
    cameraVideo.srcObject = currentStream;
    
    // Tambahkan efek mirror untuk kamera depan (selfie)
    if (facingMode === "user") {
      cameraVideo.classList.add("mirror");
    } else {
      cameraVideo.classList.remove("mirror");
    }
    
    cameraModal.classList.add("active");
  } catch (error) {
    alert("Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.");
    console.error("Camera error:", error);
  }
}

// Stop camera function
function stopCamera() {
  if (currentStream) {
    currentStream.getTracks().forEach(track => track.stop());
    currentStream = null;
  }
  cameraModal.classList.remove("active");
}

// Capture photo function
function capturePhoto() {
  const context = cameraCanvas.getContext("2d");
  cameraCanvas.width = cameraVideo.videoWidth;
  cameraCanvas.height = cameraVideo.videoHeight;
  
  // Jika kamera depan, flip gambar agar sesuai dengan preview mirror
  if (facingMode === "user") {
    context.translate(cameraCanvas.width, 0);
    context.scale(-1, 1);
  }
  
  context.drawImage(cameraVideo, 0, 0);
  
  cameraCanvas.toBlob(blob => {
    // Convert blob to file with new format: camera_memberId_date_number.jpg
    const today = new Date();
    const dateStr = today.getDate().toString().padStart(2, '0') + '-' + 
                    (today.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                    today.getFullYear();
    const fileName = `camera_${memberIdValue}_${dateStr}_${fileCounter}.jpg`;
    const file = new File([blob], fileName, { type: 'image/jpeg' });
    
    // Set as selected file
    selectedFile = file;
    
    // Show preview
    const previewContainer = document.getElementById('previewContainer');
    const fileNameEl = document.getElementById('fileName');
    const fileSizeEl = document.getElementById('fileSize');
    
    fileNameEl.textContent = fileName;
    fileSizeEl.textContent = formatFileSize(file.size);
    
    previewContainer.innerHTML = '';
    const img = document.createElement('img');
    img.src = URL.createObjectURL(blob);
    previewContainer.appendChild(img);
    
    filePreview.classList.add('show');
    
    stopCamera();
  }, "image/jpeg", 0.9);
}

// Switch camera function
function switchCamera() {
  facingMode = facingMode === "user" ? "environment" : "user";
  startCamera();
}

// Event listeners
cameraBtn.addEventListener("click", startCamera);
captureBtn.addEventListener("click", capturePhoto);
closeCameraBtn.addEventListener("click", stopCamera);
switchCameraBtn.addEventListener("click", switchCamera);
</script>
