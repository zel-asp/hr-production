<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>hr · flow · tasks</title>
        <link rel="stylesheet" href="/assets/css/output.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <body class="antialiased text-gray-700 bg-amber-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-primary rounded-md flex items-center justify-center shadow-sm">
                        <span class="text-white text-xl font-semibold tracking-tight">hr</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Employee Self Service</h1>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Welcome back, <span class="font-medium text-primary">Alex Chen</span> · ID EMP‑8742
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-md text-sm shadow-sm border border-gray-200">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        clocked in · 09:14
                    </span>
                </div>
            </div>

            <!-- Main Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Details Card -->
                    <div
                        class="bg-white border border-gray-200 rounded-md p-5 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="bg-[#e7edf5] p-3 rounded-md hidden sm:block">
                                <i class="fa-regular fa-id-card text-primary text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">personal details</p>
                                <div class="flex flex-wrap gap-x-6 gap-y-1 mt-1">
                                    <span class="text-sm"><span class="font-medium text-gray-700">Dept:</span> Product
                                        Engineering</span>
                                    <span class="text-sm"><span class="font-medium text-gray-700">Manager:</span> Sarah
                                        V.</span>
                                    <span class="text-sm"><span class="font-medium text-gray-700">Start:</span> 12 Apr
                                        2021</span>
                                </div>
                            </div>
                        </div>
                        <button id="openProfileModalBtn"
                            class="text-primary text-sm font-medium bg-[#e7edf5] px-4 py-2 rounded-md hover:bg-[#d9e2ed] transition">
                            <i class="fa-regular fa-eye mr-1"></i>profile
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="stat-card bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 text-sm">available leave</span>
                                <span class="bg-[#e1eaf1] text-primary p-1.5 rounded-md">
                                    <i class="fa-regular fa-calendar"></i>
                                </span>
                            </div>
                            <p class="text-3xl font-semibold text-gray-800 mt-2">
                                18<span class="text-sm font-normal text-gray-400 ml-1">days</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">+2 accrued this quarter</p>
                        </div>

                        <div class="stat-card bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 text-sm">pending</span>
                                <span class="bg-[#f5efe2] text-[#996e2e] p-1.5 rounded-md">
                                    <i class="fa-regular fa-clipboard"></i>
                                </span>
                            </div>
                            <p class="text-3xl font-semibold text-gray-800 mt-2">3</p>
                            <p class="text-xs text-gray-400 mt-1">2 waiting approval</p>
                        </div>

                        <div class="stat-card bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 text-sm">this month</span>
                                <span class="bg-[#e0eee5] text-[#2b6b4a] p-1.5 rounded-md">
                                    <i class="fa-regular fa-clock"></i>
                                </span>
                            </div>
                            <p class="text-3xl font-semibold text-gray-800 mt-2">
                                136<span class="text-sm font-normal text-gray-400 ml-1">hrs</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">worked · 8.5h avg</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                        <h2 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                            <span class="title-accent"></span>quick actions
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <button id="openLeaveModalBtn"
                                class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                <span class="bg-white p-2 rounded-md shadow-sm">
                                    <i class="fa-regular fa-calendar-plus fa-lg text-primary"></i>
                                </span>
                                <span class="text-xs font-medium text-gray-700">request leave</span>
                            </button>

                            <button id="openAttendanceModalBtn"
                                class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                <span class="bg-white p-2 rounded-md shadow-sm">
                                    <i class="fa-regular fa-calendar-check fa-lg text-primary"></i>
                                </span>
                                <span class="text-xs font-medium text-gray-700">attendance</span>
                            </button>

                            <button id="openPayslipModalBtn"
                                class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                <span class="bg-white p-2 rounded-md shadow-sm">
                                    <i class="fa-regular fa-file-lines fa-lg text-primary"></i>
                                </span>
                                <span class="text-xs font-medium text-gray-700">payslips</span>
                            </button>

                            <button id="openSettingsModalBtn"
                                class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                <span class="bg-white p-2 rounded-md shadow-sm">
                                    <i class="fa-solid fa-sliders fa-lg text-primary"></i>
                                </span>
                                <span class="text-xs font-medium text-gray-700">settings</span>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Requests / Tasks Tabs -->
                    <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                        <!-- Tab Headers -->
                        <div class="flex items-center border-b border-gray-200 mb-4">
                            <button id="tabRequestsBtn" class="tab-btn text-sm py-2 px-4 -mb-px tab-active transition"
                                data-tab="requests">
                                <i class="fa-regular fa-clipboard mr-1"></i>recent requests
                            </button>
                            <button id="tabTasksBtn" class="tab-btn text-sm py-2 px-4 -mb-px tab-inactive transition"
                                data-tab="tasks">
                                <i class="fas fa-regular fa-list-check mr-1"></i>view tasks
                            </button>
                        </div>

                        <!-- Requests Panel -->
                        <div id="requestsPanel" class="tab-panel block">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-[#dbeafe] text-primary-hover text-xs font-medium px-2.5 py-1 rounded-md">annual</span>
                                        <div>
                                            <p class="text-sm font-medium">Vacation · May 10–15</p>
                                            <p class="text-xs text-gray-400">submitted 2d ago</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-[#f0e7fc] text-[#5940a0] text-xs font-medium px-2.5 py-1 rounded-md">sick</span>
                                        <div>
                                            <p class="text-sm font-medium">Apr 22 (1 day)</p>
                                            <p class="text-xs text-gray-400">approved</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-green-700 bg-green-50 px-3 py-1 text-xs font-medium rounded-md">approved</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-md">remote</span>
                                        <div>
                                            <p class="text-sm font-medium">WFH May 2 & 3</p>
                                            <p class="text-xs text-gray-400">pending review</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
                                </div>
                            </div>
                            <button id="viewAllRequestsBtn"
                                class="text-sm text-primary hover:underline hidden sm:block mt-5">
                                view all requests
                            </button>
                        </div>

                        <!-- Tasks Panel -->
                        <div id="tasksPanel" class="tab-panel hidden">
                            <div class="space-y-3">
                                <!-- Task 1 -->
                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-[#e1eaf1] text-primary text-xs font-medium px-2.5 py-1 rounded-md">
                                            <i class="fa-regular fa-circle-check mr-1"></i>todo
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium">Complete benefits enrollment</p>
                                            <p class="text-xs text-gray-400">due May 1 · high</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
                                </div>

                                <!-- Task 2 -->
                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-[#f0e7fc] text-[#5940a0] text-xs font-medium px-2.5 py-1 rounded-md">
                                            <i class="fa-regular fa-clock mr-1"></i>review
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium">Q2 goal setting (self-assessment)</p>
                                            <p class="text-xs text-gray-400">due May 10 · medium</p>
                                        </div>
                                    </div>
                                    <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">in
                                        progress</span>
                                </div>

                                <!-- Task 3 -->
                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-[#dbeafe] text-primary-hover text-xs font-medium px-2.5 py-1 rounded-md">
                                            <i class="fa-regular fa-file-lines mr-1"></i>training
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium">Security awareness training</p>
                                            <p class="text-xs text-gray-400">due May 5 · mandatory</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-green-700 bg-green-50 px-3 py-1 text-xs font-medium rounded-md">completed</span>
                                </div>

                                <!-- Task 4 -->
                                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-md">
                                            <i class="fa-regular fa-calendar mr-1"></i>hr
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium">Update emergency contact</p>
                                            <p class="text-xs text-gray-400">due Apr 30 · low</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
                                </div>
                            </div>
                            <button id="viewAllTasksBtn"
                                class="text-sm text-primary hover:underline hidden sm:block mt-5">
                                view all tasks
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Current Shift Card -->
                    <div class="bg-primary rounded-md p-5 text-white shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-medium tracking-wider">CURRENT SHIFT</p>
                                <p class="text-3xl font-semibold mt-1 flex items-end gap-2">
                                    09:14 <span class="text-sm font-normal text-blue-200">AM</span>
                                </p>
                                <p class="text-blue-200 text-xs mt-1">Mon 21 Oct 2024 · week 43</p>
                            </div>
                            <span class="bg-white/20 p-3 rounded-md">
                                <i class="fa-regular fa-clock fa-xl"></i>
                            </span>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button
                                class="flex-1 bg-white/20 hover:bg-white/30 py-2 rounded-md text-sm font-medium transition">Pause</button>
                            <button
                                class="flex-1 bg-white text-primary hover:bg-blue-50 py-2 rounded-md text-sm font-medium transition">Clock
                                out</button>
                        </div>
                    </div>

                    <!-- Upcoming Time Off -->
                    <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="title-accent"></span>upcoming time off
                        </h2>
                        <div class="border-l-2 border-[#9bb7d4] pl-3 space-y-2">
                            <div>
                                <p class="text-sm font-medium">May 10–15</p>
                                <p class="text-xs text-gray-400">Annual · 5 days</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Jun 3</p>
                                <p class="text-xs text-gray-400">Personal (half day)</p>
                            </div>
                        </div>
                        <hr class="my-3 border-gray-100">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Next accrual: <span class="font-medium">May 1</span></span>
                            <span class="text-primary font-medium">+1.5 days</span>
                        </div>
                    </div>

                    <!-- HR Announcements -->
                    <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                                <span class="title-accent"></span>HR announcements
                            </h2>
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-md">2 new</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <span class="bg-[#ecf3fa] h-fit p-1.5 rounded-md">
                                    <i class="fa-regular fa-pen-to-square text-primary"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-medium">Open enrollment ends Fri</p>
                                    <p class="text-xs text-gray-400">benefits</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <span class="bg-[#fcf0e0] h-fit p-1.5 rounded-md">
                                    <i class="fa-regular fa-newspaper text-[#b26023]"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-medium">Q4 townhall · wed 2pm</p>
                                    <p class="text-xs text-gray-400">mandatory</p>
                                </div>
                            </div>
                        </div>
                        <button
                            class="w-full mt-4 text-xs text-primary bg-[#e7edf5] py-2 rounded-md hover:bg-[#d9e2ed] transition">
                            <i class="fa-regular fa-check-circle mr-1"></i>mark read
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- leave modal -->
        <?php require base_path('view/ess/modals/profile.php'); ?>

        <!-- leave modal -->
        <?php require base_path('view/ess/modals/leave.php'); ?>

        <!-- leave modal -->
        <?php require base_path('view/ess/modals/attendance.php'); ?>
        <!-- leave modal -->
        <?php require base_path('view/ess/modals/payslip.php'); ?>

        <!-- leave modal -->
        <?php require base_path('view/ess/modals/setting.php'); ?>

        <!-- leave modal -->
        <?php require base_path('view/ess/modals/allRequest.php'); ?>

        <!-- leave modal -->
        <?php require base_path('view/ess/modals/allTask.php'); ?>

        <script src="/assets/js/ess.js"></script>
    </body>

</html>