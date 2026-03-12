<div class="hr-content form-container" style="display: none;">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-[#1e3a5f] rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-tie text-white text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800">HR Manager Login</h3>
        <p class="text-gray-500 text-sm mt-1">Sign in to access HR management system</p>
    </div>

    <form action="/hr-login" method="post" onsubmit="return validateHrForm()">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="HR Email" required>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <div class="password-wrapper">
                <input type="password" name="password" id="hr-password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('hr-password', this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Turnstile CAPTCHA -->
        <div class="cf-turnstile" id="hr-captcha" data-sitekey="0x4AAAAAACp0bLBkrAZE4ATN" data-theme="light"
            data-callback="enableHrSubmit">
        </div>

        <input type="hidden" name="cf-turnstile-response" id="hr-turnstile-response">

        <button type="submit" id="hrSubmitBtn" class="login-btn" disabled>
            Complete CAPTCHA First
        </button>
    </form>
</div>