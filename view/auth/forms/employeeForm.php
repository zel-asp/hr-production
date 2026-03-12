<!-- Employee Login Form -->
<style>
    /* Add this to your stylesheet */

    /* Make Turnstile container match input styling */
    .cf-turnstile {
        width: 100%;
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    /* Style the iframe container to match input dimensions */
    .cf-turnstile iframe {
        border-radius: 10px !important;
        border: 1px solid #e0e0e0 !important;
        background-color: white;
        transition: all 0.3s ease;
        width: 100% !important;
        max-width: 304px;
        /* Turnstile's natural width */
    }

    /* Add hover effect to match inputs */
    .cf-turnstile iframe:hover {
        border-color: #1e3a5f !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
    }

    /* Center alignment fix */
    .input-group.cf-turnstile {
        display: flex;
        justify-content: center;
        padding: 0;
        border: none;
        background: transparent;
    }

    /* Remove any default input-group styles from the Turnstile container */
    .input-group.cf-turnstile {
        border: none;
        box-shadow: none;
    }

    /* Ensure consistent spacing */
    .input-group.cf-turnstile {
        margin-bottom: 1.5rem;
    }
</style>

<div class="employee-content form-container" style="display: flex;">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-[#1e3a5f] rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user text-white text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800">Employee Login</h3>
        <p class="text-gray-500 text-sm mt-1">Sign in to access your employee portal</p>
    </div>

    <!-- Add Cloudflare Turnstile Script -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <form action="/employee-login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="input-group">
            <i class="fas fa-id-card"></i>
            <input type="email" name="email" placeholder="Employee Email" required>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <!-- Cloudflare Turnstile Widget -->
        <div class="turnstile-wrapper">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAACp0bLBkrAZE4ATN" data-theme="light" data-size="normal">
            </div>
        </div>

        <button type="submit" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
        </button>
    </form>
</div>