# Soketi WebSocket Server Setup

## Cara Jalankan Soketi Server

### 1. Buka Terminal Baru (PowerShell)
```powershell
soketi start
```

Server akan running di: `http://127.0.0.1:6001`

### 2. Jalankan Laravel Server (Terminal Lain)
```powershell
php artisan serve
```

### 3. Test WebSocket
- Login sebagai member
- Kirim pesan
- Buka tab baru, login sebagai CS
- Balas pesan dari CS
- Cek real-time message tanpa refresh!

## Konfigurasi Soketi

Default config sudah ada di `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

## Fitur WebSocket yang Aktif

✅ **Real-time messaging** - Pesan langsung muncul tanpa polling
✅ **Typing indicator** - Tampilkan "sedang mengetik..." real-time
✅ **Auto greeting** - Greeting dari CS otomatis muncul
✅ **Session closed notification** - Notifikasi sesi ditutup real-time

## Troubleshooting

### Jika Soketi tidak jalan:
```powershell
# Install ulang soketi
npm install -g @soketi/soketi

# Jalankan dengan verbose logging
soketi start --debug
```

### Jika pesan tidak muncul:
1. Check browser console untuk error
2. Pastikan Soketi server running
3. Pastikan `npm run build` sudah dijalankan
4. Clear browser cache

## Stop Soketi
Tekan `Ctrl + C` di terminal Soketi

---

**Note:** Polling JavaScript sudah di-comment di `room_chat_section.blade.php`.
Sekarang sistem pakai WebSocket real-time via Soketi! 🚀
