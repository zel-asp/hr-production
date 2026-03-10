<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>hr · flow · tasks </title>
        <link rel="stylesheet" href="/assets/css/output.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <body class="antialiased text-gray-700 bg-gray-100">
        <?php require base_path('view/partials/message.php'); ?>

        <?php
        // Get current tab from URL, default to 'dashboard'
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
        ?>

        <div class="flex h-screen overflow-hidden">
            <!-- Overlay for mobile -->
            <div id="overlay" class="overlay" onclick="toggleSidebar()"></div>

            <!-- Sidebar Navigation -->
            <aside id="sidebar"
                class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full sidebar-mobile lg:translate-x-0 sidebar-transition">
                <!-- Sidebar Header with Close Button for Mobile -->
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h1 class="text-lg font-bold text-gray-800">ESS Portal</h1>
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark fa-xl"></i>
                    </button>
                </div>

                <!-- User Info Card -->
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-semibold">
                            <?= strtoupper(substr($employeeInfo['full_name'] ?? 'E', 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                <?= htmlspecialchars($employeeInfo['full_name'] ?? 'Employee') ?>
                            </p>
                            <p class="text-xs text-gray-500 truncate">ID:
                                <?= htmlspecialchars($employeeInfo['employee_number'] ?? 'N/A') ?>
                            </p>
                        </div>
                    </div>
                    <?php if ($attendanceStatus != 'clocked_out' && $currentAttendance && isset($currentAttendance['clock_in'])): ?>
                        <div class="mt-2 flex items-center gap-1 text-xs text-green-600 bg-green-50 p-1.5 rounded">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Clocked in · <?= date('g:i A', strtotime($currentAttendance['clock_in'])) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 overflow-y-auto p-4">
                    <div class="space-y-1">
                        <a href="?tab=dashboard"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'dashboard' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-house w-5 <?= $currentTab == 'dashboard' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="?tab=attendance"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'attendance' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-clock w-5 <?= $currentTab == 'attendance' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Attendance</span>
                            <?php if ($attendanceStatus != 'clocked_out'): ?>
                                <span class="ml-auto w-2 h-2 bg-green-500 rounded-full"></span>
                            <?php endif; ?>
                        </a>

                        <a href="?tab=leave"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'leave' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-calendar-plus w-5 <?= $currentTab == 'leave' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Leave Requests</span>
                            <?php if (($leaveStats['pending_count'] ?? 0) > 0): ?>
                                <span
                                    class="ml-auto bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded-full"><?= $leaveStats['pending_count'] ?></span>
                            <?php endif; ?>
                        </a>

                        <a href="?tab=tasks"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'tasks' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-list-check w-5 <?= $currentTab == 'tasks' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>My Tasks</span>
                            <?php if (($taskStats['ongoing_count'] ?? 0) > 0): ?>
                                <span class="ml-auto bg-blue-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                    <?= $taskStats['ongoing_count'] ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="?tab=sched"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'sched' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-calendar w-5 <?= $currentTab == 'sched' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Schedule</span>
                        </a>

                        <a href="?tab=uploadFiles"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'uploadFiles' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-folder w-5 <?= $currentTab == 'uploadFiles' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Upload Files</span>
                        </a>

                        <?php if ($employeeInfo['role'] === 'mentor'): ?>
                            <a href="?tab=mentorship"
                                class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'mentorship' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <i
                                    class="fa-solid fa-users w-5 <?= $currentTab == 'mentorship' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                                <span>Mentorship</span>
                            </a>
                        <?php endif; ?>

                        <a href="?tab=announcements"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'announcements' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-bullhorn w-5 <?= $currentTab == 'announcements' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Announcements</span>
                        </a>


                        <a href="?tab=claims"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'claims' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-bullhorn w-5 <?= $currentTab == 'claims' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>Claims & Reimbursement</span>
                            <!-- <?php if (($taskStats['ongoing_count'] ?? 0) > 0): ?>
                                <span
                                    class="ml-auto bg-blue-500 text-white text-xs px-1.5 py-0.5 rounded-full"><?= $taskStats['ongoing_count'] ?></span>
                            <?php endif; ?> -->
                        </a>

                        <a href="?tab=profile"
                            class="sidebar-nav-link flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $currentTab == 'profile' ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i
                                class="fa-solid fa-user w-5 <?= $currentTab == 'profile' ? 'text-gray-900' : 'text-gray-400' ?>"></i>
                            <span>My Profile</span>
                        </a>
                    </div>
                </nav>

                <!-- Sidebar Footer with Logout -->
                <div class="p-4 border-t border-gray-200">
                    <form method="POST" action="/logout">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 lg:ml-64 overflow-y-auto bg-gray-50">
                <!-- Mobile Header with Hamburger -->
                <div
                    class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3 sticky top-0 z-30">
                    <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900">
                        <i class="fa-solid fa-bars fa-xl"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">ESS Portal</h1>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
                    <!-- COMPACT TIME IN/OUT CARD -->
                    <div class="bg-primary rounded-md p-4 text-white shadow-sm mb-6" id="attendanceCard">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div>
                                    <p class="text-blue-100 text-xs font-medium tracking-wider" id="shiftStatus">
                                        <?php if ($attendanceStatus == 'clocked_in'): ?>
                                            CURRENT SHIFT
                                        <?php elseif ($attendanceStatus == 'paused'): ?>
                                            PAUSED
                                        <?php else: ?>
                                            READY TO CLOCK IN
                                        <?php endif; ?>
                                    </p>
                                    <div class="text-2xl font-semibold flex items-end gap-1" id="timerDisplay">
                                        <?php
                                        $hours = floor($elapsedSeconds / 3600);
                                        $minutes = floor(($elapsedSeconds % 3600) / 60);
                                        $seconds = $elapsedSeconds % 60;
                                        ?>
                                        <span id="hours"><?= str_pad($hours, 2, '0', STR_PAD_LEFT) ?></span>
                                        <span class="text-lg">:</span>
                                        <span id="minutes"><?= str_pad($minutes, 2, '0', STR_PAD_LEFT) ?></span>
                                        <span class="text-lg">:</span>
                                        <span id="seconds"><?= str_pad($seconds, 2, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </div>

                                <div class="border-l border-white/20 pl-4">
                                    <p class="text-xs text-blue-200" id="dateDisplay">
                                        <?= date('D d M') ?>
                                    </p>
                                    <div id="shiftInfo"
                                        class="text-xs text-blue-200 <?= ($attendanceStatus != 'clocked_out') ? '' : 'hidden' ?>">
                                        <span id="shiftStartTime">
                                            <?php if ($currentAttendance && isset($currentAttendance['clock_in'])): ?>
                                                Started <?= date('g:i A', strtotime($currentAttendance['clock_in'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2" id="attendanceButtons">
                                <button id="clockInBtn"
                                    class="px-4 py-1.5 bg-white text-primary hover:bg-blue-50 rounded-md text-sm font-medium transition <?= $showClockIn ? '' : 'hidden' ?>"
                                    onclick="handleAttendance('clock_in')">
                                    <i class="fa-solid fa-right-to-bracket mr-1"></i>Clock In
                                </button>
                                <button id="pauseBtn"
                                    class="px-4 py-1.5 bg-white/20 hover:bg-white/30 rounded-md text-sm font-medium transition <?= ($attendanceStatus == 'clocked_in') ? '' : 'hidden' ?>"
                                    onclick="handleAttendance('pause')">
                                    <i class="fa-solid fa-pause mr-1"></i>Pause
                                </button>
                                <button id="resumeBtn"
                                    class="px-4 py-1.5 bg-white/20 hover:bg-white/30 rounded-md text-sm font-medium transition <?= ($attendanceStatus == 'paused') ? '' : 'hidden' ?>"
                                    onclick="handleAttendance('resume')">
                                    <i class="fa-solid fa-play mr-1"></i>Resume
                                </button>
                                <button id="clockOutBtn"
                                    class="px-4 py-1.5 bg-white text-primary hover:bg-blue-50 rounded-md text-sm font-medium transition <?= ($attendanceStatus != 'clocked_out') ? '' : 'hidden' ?>"
                                    onclick="handleAttendance('clock_out')">
                                    <i class="fa-solid fa-right-from-bracket mr-1"></i>Clock Out
                                </button>
                            </div>
                        </div>

                        <div id="overtimeIndicator"
                            class="mt-2 text-xs text-yellow-300 <?= ($elapsedSeconds >= 28800) ? '' : 'hidden' ?>">
                            <i class="fa-solid fa-clock mr-1"></i>
                            <span id="overtimeHours"><?= floor(($elapsedSeconds - 28800) / 3600) ?></span>h overtime
                        </div>
                    </div>

                    <!-- Dynamic Content Based on Tab -->
                    <?php if ($currentTab == 'dashboard'): ?>
                        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                            <!-- Left Column -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Personal Details Card -->
                                <div
                                    class="bg-white border border-gray-200 rounded-md p-5 flex flex-col sm:flex-row sm:flex-wrap sm:items-center justify-between gap-4 shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-[#e7edf5] p-3 rounded-md hidden sm:block">
                                            <i class="fa-solid fa-id-card text-primary text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">personal details</p>
                                            <div class="flex flex-wrap gap-x-6 gap-y-1 mt-1">
                                                <span class="text-sm">
                                                    <span class="font-medium text-gray-700">Dept:</span>
                                                    <?= htmlspecialchars($employeeInfo['department'] ?? 'N/A') ?>
                                                </span>
                                                <span class="text-sm">
                                                    <span class="font-medium text-gray-700">Name:</span>
                                                    <?= htmlspecialchars($employeeInfo['full_name'] ?? 'Sarah V.') ?>
                                                </span>
                                                <span class="text-sm">
                                                    <span class="font-medium text-gray-700">Start:</span>
                                                    <?= isset($employeeInfo['start_date']) && $employeeInfo['start_date'] ? date('d M Y', strtotime($employeeInfo['start_date'])) : 'Not set' ?>
                                                </span>
                                                <span class="text-sm">
                                                    <span class="font-medium text-gray-700">ID:</span>
                                                    <?= htmlspecialchars($employeeInfo['employee_number'] ?? 'N/A') ?>
                                                </span>
                                            </div>

                                            <?php if (!empty($employeeInfo['onboarding_status'])): ?>
                                                <div class="mt-2">
                                                    <span class="text-xs px-2 py-1 rounded-full 
                                                    <?php
                                                    switch ($employeeInfo['onboarding_status']) {
                                                        case 'Onboarded':
                                                            $statusClass = 'bg-green-50 text-green-700';
                                                            break;
                                                        case 'In Progress':
                                                            $statusClass = 'bg-blue-50 text-blue-700';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-amber-50 text-amber-700';
                                                    }
                                                    echo $statusClass;
                                                    ?>">
                                                        <i class="fa-solid fa-circle-check mr-1"></i>
                                                        <?= htmlspecialchars($employeeInfo['onboarding_status']) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button id="openProfileModalBtn" onclick="openModal('profileModal')"
                                            class="text-primary text-sm font-medium bg-[#e7edf5] px-4 py-2 rounded-md hover:bg-[#d9e2ed] transition w-full sm:w-auto">
                                            <i class="fa-solid fa-eye mr-1"></i>view profile
                                        </button>
                                    </div>
                                </div>

                                <!-- Stats Cards -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Requests Stats Card -->
                                    <div class="stat-card bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500 text-sm">Leave requests</span>
                                            <span class="bg-[#f5efe2] text-[#996e2e] p-1.5 rounded-md">
                                                <i class="fa-solid fa-clipboard"></i>
                                            </span>
                                        </div>

                                        <!-- Three Column Stats -->
                                        <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                                            <!-- Pending -->
                                            <div class="bg-amber-50 rounded-lg p-2">
                                                <p class="text-2xl font-bold text-amber-600">
                                                    <?= $leaveStats['pending_count'] ?? 0 ?>
                                                </p>
                                                <p class="text-xs text-gray-500">pending</p>
                                            </div>

                                            <!-- Approved -->
                                            <div class="bg-green-50 rounded-lg p-2">
                                                <p class="text-2xl font-bold text-green-600">
                                                    <?= $leaveStats['approved_count'] ?? 0 ?>
                                                </p>
                                                <p class="text-xs text-gray-500">approved</p>
                                            </div>

                                            <!-- Total -->
                                            <div class="bg-gray-50 rounded-lg p-2">
                                                <p class="text-2xl font-bold text-gray-700">
                                                    <?= $leaveStats['total_requests'] ?? 0 ?>
                                                </p>
                                                <p class="text-xs text-gray-500">total</p>
                                            </div>
                                        </div>

                                        <!-- Request Progress Bar -->
                                        <?php if (($leaveStats['total_requests'] ?? 0) > 0): ?>
                                            <div class="mt-4">
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-gray-500">Approval rate</span>
                                                    <span class="text-gray-700 font-medium">
                                                        <?php $approvalRate = ($leaveStats['total_requests'] > 0) ? round((($leaveStats['approved_count'] ?? 0) / $leaveStats['total_requests']) * 100) : 0; ?>
                                                        <?= $approvalRate ?>%
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-green-500 h-2.5 rounded-full"
                                                        style="width: <?= $approvalRate ?>%">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs mt-1">
                                                    <span class="text-amber-600"><?= $leaveStats['pending_count'] ?? 0 ?>
                                                        pending</span>
                                                    <?php if (($leaveStats['rejected_count'] ?? 0) > 0): ?>
                                                        <span class="text-red-600"><?= $leaveStats['rejected_count'] ?>
                                                            rejected</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-400 text-center mt-4">No requests submitted yet</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Task Progress Card -->
                                    <div class="stat-card bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500 text-sm">My tasks</span>
                                            <span class="bg-[#e0eee5] text-[#2b6b4a] p-1.5 rounded-md">
                                                <i class="fa-solid fa-list-check"></i>
                                            </span>
                                        </div>

                                        <!-- Task Summary -->
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-3xl font-bold text-gray-800">
                                                <?= $taskStats['total_tasks'] ?? 0 ?>
                                                <span class="text-sm font-normal text-gray-400 ml-1">total</span>
                                            </p>

                                            <!-- Mini Status Circles -->
                                            <div class="flex gap-1">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                                    <span
                                                        class="text-sm font-bold text-amber-600"><?= $taskStats['not_started_count'] ?? 0 ?></span>
                                                </div>
                                                <div
                                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span
                                                        class="text-sm font-bold text-blue-600"><?= $taskStats['ongoing_count'] ?? 0 ?></span>
                                                </div>
                                                <div
                                                    class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <span
                                                        class="text-sm font-bold text-green-600"><?= $taskStats['completed_count'] ?? 0 ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Overall Progress Bar -->
                                        <?php if (($taskStats['total_tasks'] ?? 0) > 0): ?>
                                            <div class="mt-4">
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-gray-500">Overall progress</span>
                                                    <span class="text-gray-700 font-medium">
                                                        <?php $taskProgress = round((($taskStats['completed_count'] ?? 0) / $taskStats['total_tasks']) * 100); ?>
                                                        <?= $taskProgress ?>%
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-3">
                                                    <?php
                                                    $notStartedWidth = ($taskStats['total_tasks'] > 0) ? round((($taskStats['not_started_count'] ?? 0) / $taskStats['total_tasks']) * 100) : 0;
                                                    $ongoingWidth = ($taskStats['total_tasks'] > 0) ? round((($taskStats['ongoing_count'] ?? 0) / $taskStats['total_tasks']) * 100) : 0;
                                                    $completedWidth = ($taskStats['total_tasks'] > 0) ? round((($taskStats['completed_count'] ?? 0) / $taskStats['total_tasks']) * 100) : 0;
                                                    ?>
                                                    <div class="flex h-3 rounded-full overflow-hidden">
                                                        <?php if ($notStartedWidth > 0): ?>
                                                            <div class="bg-amber-400" style="width: <?= $notStartedWidth ?>%"></div>
                                                        <?php endif; ?>
                                                        <?php if ($ongoingWidth > 0): ?>
                                                            <div class="bg-blue-400" style="width: <?= $ongoingWidth ?>%"></div>
                                                        <?php endif; ?>
                                                        <?php if ($completedWidth > 0): ?>
                                                            <div class="bg-green-400" style="width: <?= $completedWidth ?>%"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Legend -->
                                            <div class="flex gap-3 mt-3 text-xs">
                                                <span class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-amber-400 rounded-full"></span>
                                                    <span class="text-gray-500">Not Started
                                                        (<?= $taskStats['not_started_count'] ?? 0 ?>)</span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                                    <span class="text-gray-500">Ongoing
                                                        (<?= $taskStats['ongoing_count'] ?? 0 ?>)</span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                                    <span class="text-gray-500">Done
                                                        (<?= $taskStats['completed_count'] ?? 0 ?>)</span>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Urgent Alert -->
                                        <?php if (($taskStats['urgent_count'] ?? 0) > 0): ?>
                                            <div class="mt-3 bg-red-50 border border-red-100 rounded-md p-2">
                                                <p class="text-xs text-red-600 flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                                    <span class="font-medium"><?= $taskStats['urgent_count'] ?></span> urgent
                                                    task<?= $taskStats['urgent_count'] > 1 ? 's' : '' ?> need attention
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                                    <h2 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                        <span class="title-accent"></span>quick actions
                                    </h2>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        <button id="openLeaveModalBtn" onclick="openModal('leaveModal')"
                                            class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                            <span class="bg-white p-2 rounded-md shadow-sm">
                                                <i class="fa-solid fa-calendar-plus fa-lg text-primary"></i>
                                            </span>
                                            <span class="text-xs font-medium text-gray-700">request leave</span>
                                        </button>

                                        <button id="openAttendanceModalBtn" onclick="openModal('attendanceModal')"
                                            class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                            <span class="bg-white p-2 rounded-md shadow-sm">
                                                <i class="fa-solid fa-calendar-check fa-lg text-primary"></i>
                                            </span>
                                            <span class="text-xs font-medium text-gray-700">attendance</span>
                                        </button>

                                        <button id="openPayslipModalBtn" onclick="openModal('payslipModal')"
                                            class="action-tile flex flex-col items-center gap-2 p-4 bg-[#f2f5f9] rounded-md hover:bg-[#e0e9f2] transition group">
                                            <span class="bg-white p-2 rounded-md shadow-sm">
                                                <i class="fa-solid fa-file-lines fa-lg text-primary"></i>
                                            </span>
                                            <span class="text-xs font-medium text-gray-700">payslips</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Recent Requests / Tasks Tabs WITH ALL BUTTONS RESTORED -->
                                <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                                    <!-- Tab Headers -->
                                    <div class="flex items-center border-b border-gray-200 mb-4 overflow-x-auto">
                                        <button id="tabRequestsBtn"
                                            class="tab-btn text-sm py-2 px-4 -mb-px transition font-medium whitespace-nowrap <?= $currentSubTab == 'requests' ? 'text-primary border-b-2 border-primary' : 'text-gray-500' ?>"
                                            data-tab="requests" onclick="window.switchTab('requests')">
                                            <i class="fa-solid fa-clipboard mr-1"></i>recent requests
                                        </button>
                                        <button id="tabTasksBtn"
                                            class="tab-btn text-sm py-2 px-4 -mb-px transition font-medium whitespace-nowrap <?= $currentSubTab == 'tasks' ? 'text-primary border-b-2 border-primary' : 'text-gray-500' ?>"
                                            data-tab="tasks" onclick="window.switchTab('tasks')">
                                            <i class="fa-solid fa-list-check mr-1"></i>my tasks
                                        </button>
                                    </div>

                                    <!-- Requests Panel (with Delete button) -->
                                    <div id="requestsPanel"
                                        class="tab-panel <?= ($currentSubTab ?? 'requests') == 'requests' ? '' : 'hidden' ?>">
                                        <div class="space-y-3">
                                            <?php if (!empty($recentLeaveRequests)): ?>
                                                <?php foreach ($recentLeaveRequests as $request): ?>
                                                    <div
                                                        class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-[#f2f5f9] rounded-md group hover:bg-[#e8eef5] transition gap-3">
                                                        <div class="flex items-center gap-3 flex-1">
                                                            <?php
                                                            $badgeClass = 'bg-[#dbeafe] text-primary-hover';
                                                            $typeDisplay = strtolower(str_replace(' ', '', $request['leave_type']));

                                                            if ($request['leave_type'] == 'Sick Leave') {
                                                                $badgeClass = 'bg-[#f0e7fc] text-[#5940a0]';
                                                            } elseif ($request['leave_type'] == 'Personal Day') {
                                                                $badgeClass = 'bg-gray-200 text-gray-800';
                                                            } elseif ($request['leave_type'] == 'Remote Work') {
                                                                $badgeClass = 'bg-[#e0eee5] text-[#2b6b4a]';
                                                            }

                                                            $statusClass = 'text-amber-700 bg-amber-50';
                                                            if ($request['status'] == 'Approved') {
                                                                $statusClass = 'text-green-700 bg-green-50';
                                                            } elseif ($request['status'] == 'Rejected') {
                                                                $statusClass = 'text-red-700 bg-red-50';
                                                            } elseif ($request['status'] == 'Cancelled') {
                                                                $statusClass = 'text-gray-700 bg-gray-100';
                                                            }
                                                            ?>

                                                            <span
                                                                class="<?= $badgeClass ?> text-xs font-medium px-2.5 py-1 rounded-md">
                                                                <?= $typeDisplay ?>
                                                            </span>

                                                            <div class="flex-1">
                                                                <p class="text-sm font-medium">
                                                                    <?= htmlspecialchars($request['leave_type']) ?> ·
                                                                    <?= date('M d', strtotime($request['start_date'])) ?>
                                                                    <?php if ($request['start_date'] != $request['end_date']): ?>
                                                                        – <?= date('M d', strtotime($request['end_date'])) ?>
                                                                    <?php endif; ?>
                                                                    (<?= $request['total_days'] ?>
                                                                    day<?= $request['total_days'] > 1 ? 's' : '' ?>)
                                                                </p>
                                                                <p class="text-xs text-gray-400">
                                                                    submitted
                                                                    <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                                                    <?php if (!empty($request['reason'])): ?>
                                                                        · <?= htmlspecialchars(substr($request['reason'], 0, 30)) ?>...
                                                                    <?php endif; ?>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-2 self-end sm:self-auto">
                                                            <?php if ($request['status'] == 'Pending'): ?>
                                                                <form action="/remove-request" method="POST">
                                                                    <input type="hidden" name="request_id"
                                                                        value="<?= $request['id'] ?>">
                                                                    <input type="hidden" name="csrf_token"
                                                                        value="<?= $_SESSION['csrf_token'] ?>">
                                                                    <input type="hidden" name="__method" value="DELETE">
                                                                    <button
                                                                        class="opacity-0 group-hover:opacity-100 transition bg-red-600 hover:bg-red-700 text-white p-2 rounded-md text-xs"
                                                                        title="Delete request" type="submit"
                                                                        onclick="return confirm('Are you sure you want to delete this request?')">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <span
                                                                class="<?= $statusClass ?> px-3 py-1 text-xs font-medium rounded-md">
                                                                <?= $request['status'] ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-sm text-gray-500 text-center py-4">No recent requests</p>
                                            <?php endif; ?>
                                        </div>

                                        <a href="/?tab=leave" id="viewAllRequestsBtn"
                                            class="inline-block text-sm text-primary hover:underline sm:block mt-5">
                                            View all request
                                        </a>
                                    </div>

                                    <!-- Tasks Panel (with Start, Done buttons) -->
                                    <div id="tasksPanel" class="tab-panel hidden">
                                        <div class="space-y-3">
                                            <?php if (!empty($tasks)): ?>
                                                <?php foreach (array_slice($tasks, 0, 3) as $task): ?>
                                                    <div
                                                        class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-[#f2f5f9] rounded-md gap-3">
                                                        <div class="flex items-center gap-3">
                                                            <?php
                                                            $iconClass = 'fa-solid fa-circle-check';
                                                            $bgClass = 'bg-[#e1eaf1] text-primary';

                                                            switch ($task['task_type']) {
                                                                case 'training_module':
                                                                    $iconClass = 'fa-solid fa-clock';
                                                                    $bgClass = 'bg-[#f0e7fc] text-[#5940a0]';
                                                                    break;
                                                                case 'paperwork':
                                                                    $iconClass = 'fa-solid fa-file-lines';
                                                                    $bgClass = 'bg-gray-200 text-gray-800';
                                                                    break;
                                                                case 'equipment_setup':
                                                                    $iconClass = 'fa-solid fa-circle-check';
                                                                    $bgClass = 'bg-[#dbeafe] text-primary-hover';
                                                                    break;
                                                            }

                                                            $statusBadgeClass = 'text-amber-700 bg-amber-50';
                                                            switch ($task['status']) {
                                                                case 'Ongoing':
                                                                    $statusBadgeClass = 'text-blue-700 bg-blue-50';
                                                                    break;
                                                                case 'Completed':
                                                                    $statusBadgeClass = 'text-green-700 bg-green-50';
                                                                    break;
                                                            }
                                                            ?>

                                                            <span
                                                                class="<?= $bgClass ?> text-xs font-medium px-2.5 py-1 rounded-md">
                                                                <i class="<?= $iconClass ?> mr-1"></i>
                                                                <?= htmlspecialchars($task['task_type']) ?>
                                                            </span>

                                                            <div>
                                                                <p class="text-sm font-medium">
                                                                    <?= htmlspecialchars($task['task_description']) ?>
                                                                </p>
                                                                <p class="text-xs text-gray-400">
                                                                    due <?= date('M j', strtotime($task['due_date'])) ?> ·
                                                                    <?= htmlspecialchars($task['priority']) ?>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-2 self-end sm:self-auto">
                                                            <?php if ($task['status'] == 'Not Started'): ?>
                                                                <form method="POST" action="/tasks/start">
                                                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                                    <input type="hidden" name="csrf_token"
                                                                        value="<?= $_SESSION['csrf_token'] ?>">
                                                                    <input type="hidden" name="__method" value="PATCH">
                                                                    <input type="hidden" name="action" value="start">
                                                                    <input type="hidden" name="redirect"
                                                                        value="<?= $_SERVER['REQUEST_URI'] ?>">
                                                                    <button type="submit"
                                                                        class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-700 transition">
                                                                        Start
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <?php if ($task['status'] == 'Ongoing'): ?>
                                                                <form method="POST" action="/tasks/complete">
                                                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                                    <input type="hidden" name="csrf_token"
                                                                        value="<?= $_SESSION['csrf_token'] ?>">
                                                                    <input type="hidden" name="__method" value="PATCH">
                                                                    <input type="hidden" name="action" value="complete">
                                                                    <input type="hidden" name="redirect"
                                                                        value="<?= $_SERVER['REQUEST_URI'] ?>">
                                                                    <button type="submit"
                                                                        class="bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-700 transition">
                                                                        Done
                                                                    </button>
                                                                </form>
                                                            <?php elseif ($task['status'] == 'Completed'): ?>
                                                                <button
                                                                    class="bg-gray-400 text-white px-3 py-1 rounded-md text-xs cursor-not-allowed"
                                                                    disabled>
                                                                    Done
                                                                </button>
                                                            <?php endif; ?>

                                                            <span
                                                                class="<?= $statusBadgeClass ?> px-3 py-1 text-xs font-medium rounded-md">
                                                                <?= htmlspecialchars($task['status']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-sm text-gray-500 text-center py-4">No tasks assigned</p>
                                            <?php endif; ?>
                                        </div>

                                        <a href="/?tab=tasks"
                                            class="inline-block text-sm text-primary hover:underline sm:block mt-5">
                                            View All Tasks
                                        </a>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <!-- Admin Notes -->
                                    <div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
                                        <div class="flex items-center justify-between mb-3">
                                            <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                                                <span class="title-accent"></span>Admin Notes
                                            </h2>
                                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-md">
                                                <i class="far fa-sticky-note mr-1"></i>3 notes
                                            </span>
                                        </div>

                                        <div class="space-y-3">
                                            <!-- Note 1 -->
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user-shield text-purple-600 text-xs"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <p class="text-xs font-medium text-purple-700">System
                                                                Administrator</p>
                                                            <span class="text-[10px] text-gray-400">March 15, 2025</span>
                                                        </div>
                                                        <p class="text-sm text-gray-600">Please ensure all employee
                                                            documents are uploaded before the payroll cutoff on March 20.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Note 2 -->
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user-shield text-purple-600 text-xs"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <p class="text-xs font-medium text-purple-700">System
                                                                Administrator</p>
                                                            <span class="text-[10px] text-gray-400">March 14, 2025</span>
                                                        </div>
                                                        <p class="text-sm text-gray-600">Updated company policies have been
                                                            posted. All employees must acknowledge by end of week.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Note 3 -->
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user-shield text-purple-600 text-xs"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <p class="text-xs font-medium text-purple-700">System
                                                                Administrator</p>
                                                            <span class="text-[10px] text-gray-400">March 12, 2025</span>
                                                        </div>
                                                        <p class="text-sm text-gray-600">System maintenance scheduled for
                                                            Sunday, March 17 from 2AM to 4AM. Expect brief downtime.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                    <?php elseif ($currentTab == 'attendance'): ?>
                        <?php require base_path('view/ess/section/allAttendance.php'); ?>

                    <?php elseif ($currentTab == 'leave'): ?>
                        <?php require base_path('view/ess/section/allRequest.php'); ?>


                    <?php elseif ($currentTab == 'tasks'): ?>
                        <?php require base_path('view/ess/section/allTask.php'); ?>


                    <?php elseif ($currentTab == 'sched'): ?>
                        <?php require base_path('view/ess/section/schedule.php'); ?>

                    <?php elseif ($currentTab == 'announcements'): ?>
                        <?php require base_path('view/ess/section/notes.php'); ?>

                    <?php elseif ($currentTab == 'claims'): ?>
                        <?php require base_path('view/ess/section/claims.php'); ?>

                    <?php elseif ($currentTab == 'mentorship'): ?>
                        <?php require base_path('view/ess/section/mentorship.php'); ?>

                    <?php elseif ($currentTab == 'uploadFiles'): ?>
                        <?php require base_path('view/ess/section/uploadFiles.php'); ?>


                    <?php elseif ($currentTab == 'profile'): ?>
                        <?php require base_path('view/ess/section/profile.php'); ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        <!-- Modals -->
        <?php require base_path('view/ess/modals/leave.php'); ?>
        <?php require base_path('view/ess/modals/attendance.php'); ?>
        <?php require base_path('view/ess/modals/payslip.php'); ?>

        <script>
            window.attendanceConfig = {
                currentAttendanceId: <?= json_encode($currentAttendance['id'] ?? null) ?>,
                currentStatus: '<?= $attendanceStatus ?>',
                pauseTotal: <?= $pauseTotal ?>,
                elapsedSeconds: <?= $elapsedSeconds ?>,
                csrfToken: '<?= $_SESSION['csrf_token'] ?>',
                <?php if ($currentAttendance && isset($currentAttendance['clock_in'])): ?>
                                                shiftStartTime: '<?= $currentAttendance['clock_in'] ?>'
                <?php endif; ?>
            };
        </script>

        <script src="/assets/js/timeInOut.js"></script>
        <script src="/assets/js/ess.js"></script>
        <script type="module" src="/assets/js/claimsUpload.js"></script>
        <script type="module" src="/assets/js/files.js"></script>
    </body>

</html>