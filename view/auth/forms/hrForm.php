<div class="hr-content form-container" style="display: none;">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-[#1e3a5f] rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-tie text-white text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800">HR Manager Login</h3>
        <p class="text-gray-500 text-sm mt-1">Sign in to access HR management system</p>
    </div>

    <form action="/hr-login" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="hr_email" placeholder="HR Email" value="">
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="hr_password" placeholder="Password" value="">
        </div>

        <button type="submit" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
        </button>
    </form>
</div>