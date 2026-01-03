<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Member</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .wave-area {
            position: absolute;
            bottom: -20px;
            width: 100%;
            height: 130px;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }
    </style>
</head>

<body class="bg-gray-200 flex justify-center items-center min-h-screen px-4">
    
    <!-- Include Modals -->
    @include('auth.modal_information.login_loading_modal')
    @include('auth.modal_information.login_success_modal')
    @include('auth.modal_information.login_failed_modal')
    
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl overflow-hidden">

        <div class="relative h-56" style="background-color: #282828;">

            <div class="absolute right-7 bottom-6" style="z-index: 10;">
                <h2 class="text-3xl font-bold" style="color: #282828;">Sign in</h2>
            </div>

            <!-- DEFAULT WAVE = SVG -->
            <!-- <img src="wave.png" class="w-full h-full object-cover"> -->
            <div class="wave-area">
                <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-full">
                    <path d="M0,40 C150,120 380,-20 500,50 L500,250 L0,250 Z" fill="white"></path>
                </svg>
            </div>

        </div>

        <div class="px-7 pb-10 pt-4">
            <form id="loginForm" action="{{ route('member.login') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label class="text-sm font-semibold text-gray-700">ID Member</label>
                    <div class="relative mt-1">
                        <i class="fa-regular fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text"
                            name="member_id"
                            id="member_id"
                            class="w-full border border-gray-300 rounded-xl py-3 pl-10 pr-4 text-sm focus:ring-2 outline-none"
                            style="focus:ring-color: #282828;"
                            placeholder="Type your member ID"
                            value="{{ old('member_id') }}"
                            required
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700">Password</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="password"
                            id="password"
                            name="password"
                            class="w-full border border-gray-300 rounded-xl py-3 pl-10 pr-10 text-sm focus:ring-2 outline-none"
                            style="focus:ring-color: #282828;"
                            placeholder="******"
                            required
                        >
                        <i id="togglePassword" class="fa-regular fa-eye absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer"></i>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-7 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4" style="accent-color: #282828;">
                        <span class="text-gray-600">Remember me</span>
                    </label>
                    <a href="#" class="text-gray-600" style="color: #282828;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Forgot Password?</a>
                </div>

                <button type="submit" class="w-full text-white py-3 rounded-xl font-semibold text-sm shadow active:scale-95 transition" style="background-color: #282828;" onmouseover="this.style.backgroundColor='#1a1a1a'" onmouseout="this.style.backgroundColor='#282828'">
                    Sign In
                </button>

            </form>
            <!--
            <p class="text-center mt-6 text-sm text-gray-600">
                Don't have an account?
                <a href="#" class="font-semibold hover:underline" style="color: #282828;">Sign Up</a>
            </p> -->
        </div>

    </div>

    <script>
        const toggle = document.getElementById("togglePassword");
        const pass = document.getElementById("password");

        toggle.addEventListener("click", () => {
            pass.type = pass.type === "password" ? "text" : "password";
            toggle.classList.toggle("fa-eye-slash");
        });

        // Login Form Handler with AJAX
        const loginForm = document.getElementById('loginForm');
        
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            
            // Show loading modal
            showLoginLoading(1500);
            
            // Wait for loading animation
            setTimeout(async () => {
                try {
                    const response = await fetch('{{ route("member.login") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        // Login berhasil
                        showLoginSuccess(data.message || 'Login berhasil!');
                    } else {
                        // Login gagal
                        showLoginFailed(data.message || 'ID atau password salah!');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showLoginFailed('Terjadi kesalahan saat login. Silakan coba lagi.');
                }
            }, 1500);
        });
        
        // Show Login Loading
        function showLoginLoading(duration) {
            const loadingModal = document.getElementById('loginLoadingModal');
            const loadingProgress = document.getElementById('loginLoadingProgress');
            
            loadingModal.style.display = 'flex';
            loadingProgress.style.width = '0%';
            
            const startTime = Date.now();
            const progressInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min((elapsed / duration) * 100, 100);
                loadingProgress.style.width = progress + '%';
                
                if (progress >= 100) {
                    clearInterval(progressInterval);
                }
            }, 50);
        }
        
        // Show Login Success Modal
        function showLoginSuccess(message) {
            document.getElementById('loginLoadingModal').style.display = 'none';
            
            // Set title and subtitle from backend response
            document.getElementById('loginSuccessTitle').textContent = message.title || 'Login Berhasil!';
            document.getElementById('loginSuccessSubtitle').textContent = message.subtitle || 'Selamat datang kembali';
            
            document.getElementById('loginSuccessModal').style.display = 'flex';
        }
        
        // Show Login Failed Modal
        function showLoginFailed(message) {
            document.getElementById('loginLoadingModal').style.display = 'none';
            document.getElementById('loginFailedMessage').textContent = message;
            document.getElementById('loginFailedModal').style.display = 'flex';
        }
        
        // Proceed after successful login
        function proceedAfterLogin() {
            window.location.href = '{{ route("chat.list") }}';
        }
        
        // Close failed modal and try again
        document.getElementById('closeLoginFailedBtn').addEventListener('click', () => {
            document.getElementById('loginFailedModal').style.display = 'none';
        });
    </script>

</body>
</html>
