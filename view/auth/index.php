<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HR Portal · Employee & HR</title>
        <link rel="stylesheet" href="/public/assets/css/output.css">
        <link rel="stylesheet" href="/public/assets/css/auth.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <style>
        /* Minimal CSS for password toggle */
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 40px !important;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 5px;
        }

        .toggle-password:hover {
            color: #1e3a5f;
        }

        /* Turnstile container */
        .cf-turnstile {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }

        /* Disabled button state */
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <body class="bg-gray-50 min-h-screen flex items-center justify-center p-5">
        <?php require base_path('view/partials/message.php'); ?>

        <div class="split-container employee-mode" id="splitContainer">
            <!-- Left Panel - Description (Hidden on mobile) -->
            <div class="panel panel-left relative overflow-hidden">
                <div class="floating-icon">
                    <i class="fas fa-building"></i>
                </div>

                <!-- Employee Description -->
                <div class="employee-content" style="display: flex; flex-direction: column; height: 100%;">
                    <div>
                        <div class="badge">
                            <i class="fas fa-user-clock mr-2"></i>Employee Self Service
                        </div>
                        <h2 class="title-large">Employee Portal</h2>
                        <p class="text-white/90 mb-8 text-lg">Access your personal dashboard, manage your work life, and
                            stay connected with your team.</p>
                    </div>

                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="fas fa-file-invoice feature-icon"></i>
                            <div class="feature-text">
                                <h3>Payslips & Tax Forms</h3>
                                <p>View and download your payslips, tax documents, and compensation history</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-calendar-alt feature-icon"></i>
                            <div class="feature-text">
                                <h3>Leave Management</h3>
                                <p>Apply for time off, check leave balance, and track request status</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <div class="feature-text">
                                <h3>Performance</h3>
                                <p>Track your goals, view feedback, and monitor your development</p>
                            </div>
                        </div>
                    </div>

                    <button class="switch-btn" onclick="switchToHR()">
                        Switch to HR Login <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- HR Description -->
                <div class="hr-content" style="display: none; flex-direction: column; height: 100%;">
                    <div>
                        <div class="badge">
                            <i class="fas fa-user-tie mr-2"></i>HR Management
                        </div>
                        <h2 class="title-large">HR Command Center</h2>
                        <p class="text-white/90 mb-8 text-lg">Powerful tools for HR professionals to manage the entire
                            employee lifecycle.</p>
                    </div>

                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <div class="feature-text">
                                <h3>Recruitment</h3>
                                <p>Post jobs, track applicants, and manage the hiring pipeline</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-user-plus feature-icon"></i>
                            <div class="feature-text">
                                <h3>Onboarding</h3>
                                <p>Streamline new hire paperwork, training, and orientation</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-chart-pie feature-icon"></i>
                            <div class="feature-text">
                                <h3>Analytics</h3>
                                <p>Access workforce data, retention metrics, and HR insights</p>
                            </div>
                        </div>
                    </div>

                    <button class="switch-btn" onclick="switchToEmployee()">
                        <i class="fas fa-arrow-left"></i> Switch to Employee Login
                    </button>
                </div>
            </div>

            <!-- Right Panel - Forms -->
            <div class="panel panel-right">
                <!-- Mobile Toggle Buttons -->
                <div class="mobile-toggle-group">
                    <button class="mobile-toggle-btn active" id="mobileEmployeeBtn" onclick="switchToEmployee()">
                        <i class="fas fa-user mr-2"></i>Employee
                    </button>
                    <button class="mobile-toggle-btn" id="mobileHRBtn" onclick="switchToHR()">
                        <i class="fas fa-user-tie mr-2"></i>HR
                    </button>
                </div>

                <!-- Employee Login Form -->
                <?php require base_path('view/auth/forms/employeeForm.php'); ?>

                <!-- HR Login Form -->
                <?php require base_path('view/auth/forms/hrForm.php'); ?>

            </div>
        </div>

        <script src="/public/assets/js/auth.js"></script>
        <!-- Add Turnstile script -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

        <!-- Add password toggle and form handling -->
        <script>
            // Password toggle function
            function togglePassword(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // Enable submit after CAPTCHA
            function enableEmployeeSubmit(token) {
                const btn = document.getElementById('employeeSubmitBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Sign In';
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            function enableHrSubmit(token) {
                const btn = document.getElementById('hrSubmitBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Sign In';
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            // Reset CAPTCHA when switching tabs
            function switchTab(tab) {
                // Your existing tab switching code
                if (tab === 'employee') {
                    document.querySelector('.employee-content').style.display = 'flex';
                    document.querySelector('.hr-content').style.display = 'none';
                    if (window.turnstile) {
                        turnstile.reset('#employee-captcha');
                        document.getElementById('employeeSubmitBtn').disabled = true;
                        document.getElementById('employeeSubmitBtn').innerHTML = 'Complete CAPTCHA First';
                        document.getElementById('employeeSubmitBtn').classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    document.querySelector('.employee-content').style.display = 'none';
                    document.querySelector('.hr-content').style.display = 'flex';
                    if (window.turnstile) {
                        turnstile.reset('#hr-captcha');
                        document.getElementById('hrSubmitBtn').disabled = true;
                        document.getElementById('hrSubmitBtn').innerHTML = 'Complete CAPTCHA First';
                        document.getElementById('hrSubmitBtn').classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            }
        </script>
    </body>

</html>