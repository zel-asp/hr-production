<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HR Portal · Employee & HR</title>
        <link rel="stylesheet" href="/assets/css/output.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <body class="bg-body min-h-screen flex items-center justify-center p-5 relative">
        <div class="fixed inset-0 bg-noise pointer-events-none"></div>

        <div class="w-full max-w-md">
            <!-- Logo area -->
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 bg-[#1e3a5f] rounded-lg flex items-center justify-center shadow-sm">
                        <span class="text-white text-xl font-semibold tracking-tight">HR</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold text-[#132435] tracking-tight">HR Portal</h1>
                        <p class="text-sm text-[#546e7a] mt-0.5">Employee & HR Manager access</p>
                    </div>
                </div>
            </div>

            <!-- Card container -->
            <div
                class="bg-white rounded-xl border border-[#e2e8f0] shadow-[0_8px_20px_-8px_rgba(0,20,40,0.08),0_2px_4px_-1px_rgba(0,0,0,0.02)] p-7">
                <!-- Toggle buttons - JS only for this -->
                <div class="bg-[#f5f9ff] rounded-lg p-1 mb-6 border border-[#dde3ed] flex">
                    <button id="show-employee"
                        class="flex-1 py-2.5 text-sm font-medium rounded-md bg-white shadow-sm text-[#1e3a5f] border border-[#d0d9e6]">Employee</button>
                    <button id="show-hr" class="flex-1 py-2.5 text-sm font-medium text-primary hover:text-[#1e3a5f]">HR
                        Manager</button>
                </div>

                <!-- Employee Login Form -->
                <?php require base_path('view/auth/forms/employeeForm.php'); ?>

                <!-- HR Login Form  -->
                <?php require base_path('view/auth/forms/hrForm.php'); ?>

            </div>
        </div>

        <!-- JavaScript only for toggling between forms -->
        <script src="/assets/js/auth.js"></script>

    </body>

</html>