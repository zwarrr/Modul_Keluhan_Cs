// ============================================
// FILE UPLOAD AND PREVIEW HANDLER
// ============================================

// Format file size to human readable
function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// File input handler
fileInput.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  // Validate file size (10MB max)
  if (file.size > 10 * 1024 * 1024) {
    alert('Ukuran file maksimal 10MB');
    fileInput.value = '';
    return;
  }
  
  // Validate file type
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
                        'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/plain', 'application/zip', 'application/x-rar-compressed'];
  
  if (!allowedTypes.includes(file.type)) {
    alert('Tipe file tidak didukung');
    fileInput.value = '';
    return;
  }
  
  // Rename file with new format: file_memberId_date_number.ext
  const today = new Date();
  const dateStr = today.getDate().toString().padStart(2, '0') + '-' + 
                  (today.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                  today.getFullYear();
  const fileExt = file.name.split('.').pop();
  const isImage = file.type.startsWith('image/');
  const prefix = isImage ? 'camera' : 'file';
  const newFileName = `${prefix}_${memberIdValue}_${dateStr}_${fileCounter}.${fileExt}`;
  
  // Create new File object with new name
  const renamedFile = new File([file], newFileName, { type: file.type });
  selectedFile = renamedFile;
  
  // Show preview
  const previewContainer = document.getElementById('previewContainer');
  const fileNameEl = document.getElementById('fileName');
  const fileSizeEl = document.getElementById('fileSize');
  
  fileNameEl.textContent = newFileName;
  fileSizeEl.textContent = formatFileSize(file.size);
  
  previewContainer.innerHTML = '';
  
  if (isImage) {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    previewContainer.appendChild(img);
  } else {
    const fileIcon = document.createElement('div');
    fileIcon.className = 'file-icon-large';
    fileIcon.innerHTML = '<i class="fa-solid fa-file" style="font-size: 48px; color: #666;"></i>';
    previewContainer.appendChild(fileIcon);
  }
  
  filePreview.classList.add('show');
  fileInput.value = '';
});

// Remove file preview
removeFileBtn.addEventListener('click', () => {
  selectedFile = null;
  filePreview.classList.remove('show');
  fileInput.value = '';
});
