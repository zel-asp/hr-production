<!-- Employee Login Form -->
<div class="employee-content form-container" style="display: flex;">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-[#1e3a5f] rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user text-white text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800">Employee Login</h3>
        <p class="text-gray-500 text-sm mt-1">Sign in to access your employee portal</p>
    </div>
    <form action="/employee-login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="input-group">
            <i class="fas fa-id-card"></i>
            <input type="email" name="email" placeholder="Employee Email">
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password">
        </div>

        <button type="submit" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
        </button>
    </form>
</div>