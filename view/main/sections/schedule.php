<div class="tab-content" id="shift-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Attendance & Shift Management</h2>
            <p class="text-gray-500 text-sm mt-1">Upload attendance records and manage shift requests</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Download Schedule Template Button -->
            <a href="/assets/template/Template.xlsx"
                class="px-4 py-2 text-sm font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                Download Schedule Template
            </a>

        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Today's Schedule</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1"><?= $schedulesToday ?? 0 ?></p>
                    <p class="text-xs text-gray-400 mt-1">Employees scheduled</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">This Week</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1"><?= $schedulesThisWeek ?? 0 ?></p>
                    <p class="text-xs text-gray-400 mt-1">Upcoming shifts</p>
                </div>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week text-indigo-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Upcoming</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1"><?= $schedulesUpcoming ?? 0 ?></p>
                    <p class="text-xs text-gray-400 mt-1">Future schedules</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-emerald-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Past Schedules</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1"><?= $schedulesPast ?? 0 ?></p>
                    <p class="text-xs text-gray-400 mt-1">Completed shifts</p>
                </div>
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-gray-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Excel Upload Section -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">

        <div class="p-6">
            <form id="uploadForm" enctype="multipart/form-data" method="POST" action="/upload-attendance">
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-gray-300 transition-colors duration-200"
                    id="dropZone">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-file-excel text-3xl text-blue-500"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-800 mb-2">Drag & drop your Excel file here</h4>
                        <p class="text-sm text-gray-400 mb-4">or click to browse (supports .xlsx, .xls)</p>

                        <!-- File Input -->
                        <input type="file" name="attendance_file" id="fileInput" class="hidden" accept=".xlsx,.xls"
                            onchange="handleFileSelect(this)">

                        <!-- Upload Button -->
                        <button type="button" onclick="document.getElementById('fileInput').click()"
                            class="btn-primary">
                            <i class="fas fa-upload"></i>
                            Choose File
                        </button>
                    </div>
                </div>

                <!-- File Info (hidden by default) -->
                <div id="fileInfo" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-excel text-green-600"></i>
                            </div>
                            <div>
                                <p id="fileName" class="text-sm font-medium text-gray-800"></p>
                                <p id="fileSize" class="text-xs text-gray-400"></p>
                            </div>
                        </div>
                        <button type="button" onclick="clearFile()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Upload Button -->
                <div class="mt-4 flex justify-end">
                    <button type="submit" id="submitBtn" disabled class="btn-primary opacity-50 cursor-not-allowed">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Upload and Process
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule List Section -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between"
            id="employeeSched">
            <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Employee Schedule</h3>
                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full border border-blue-200">
                    <?= $totalSchedules ?? 0 ?> total schedules
                </span>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-3">
                <select name="schedule_filter"
                    onchange="window.location.href='?tab=shift&schedule_filter='+this.value+'&schedule_dept=<?= $scheduleDepartmentFilter ?? '' ?>&schedule_employee=<?= $scheduleEmployeeFilter ?? '' ?>&schedule_page=1#employeeSched'"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="upcoming" <?= ($scheduleFilter ?? 'upcoming') == 'upcoming' ? 'selected' : '' ?>>All
                        Upcoming</option>
                    <option value="today" <?= ($scheduleFilter ?? '') == 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= ($scheduleFilter ?? '') == 'week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= ($scheduleFilter ?? '') == 'month' ? 'selected' : '' ?>>This Month</option>
                </select>

                <select name="schedule_dept"
                    onchange="window.location.href='?tab=shift&schedule_filter=<?= $scheduleFilter ?? 'upcoming' ?>&schedule_dept='+this.value+'&schedule_employee=<?= $scheduleEmployeeFilter ?? '' ?>&schedule_page=1#employeeSched'"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Departments</option>
                    <?php if (!empty($scheduleDepartments)): ?>
                        <?php foreach ($scheduleDepartments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department']) ?>" <?= ($scheduleDepartmentFilter ?? '') == $dept['department'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['department']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <div class="relative">
                    <input type="text" placeholder="Search employee..."
                        value="<?= htmlspecialchars($scheduleEmployeeFilter ?? '') ?>"
                        onkeypress="if(event.key === 'Enter') window.location.href='?tab=shift&schedule_filter=<?= $scheduleFilter ?? 'upcoming' ?>&schedule_dept=<?= $scheduleDepartmentFilter ?? '' ?>&schedule_employee='+this.value+'&schedule_page=1#employeeSched'"
                        class="text-xs border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 w-40 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <i class="fas fa-search absolute left-2.5 top-2 text-gray-400 text-xs"></i>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Schedule Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="pb-3">Employee</th>
                            <th class="pb-3">Date</th>
                            <th class="pb-3">Shift</th>
                            <th class="pb-3">Time In</th>
                            <th class="pb-3">Time Out</th>
                            <th class="pb-3">Department</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($employeeSchedules ?? [])): ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    <i class="fas fa-calendar-times text-3xl mb-2 opacity-50"></i>
                                    <p class="text-sm">No schedules found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employeeSchedules as $schedule): ?>
                                <tr class="text-sm hover:bg-gray-50 transition-colors duration-150">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    <?= htmlspecialchars($schedule['full_name'] ?? '') ?>
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    <?= htmlspecialchars($schedule['employee_number'] ?? '') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-medium text-gray-800">
                                            <?= htmlspecialchars($schedule['formatted_date'] ?? '') ?>
                                        </p>
                                    </td>
                                    <td class="py-3">
                                        <?php if (!empty($schedule['shift_name'])): ?>
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($schedule['shift_name']) ?>
                                            </p>
                                            <p class="text-xs text-gray-400"><?= htmlspecialchars($schedule['shift_code'] ?? '') ?>
                                            </p>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">No shift assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-medium text-gray-800">
                                            <?= htmlspecialchars($schedule['formatted_time_in'] ?? '—') ?>
                                        </p>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-medium text-gray-800">
                                            <?= htmlspecialchars($schedule['formatted_time_out'] ?? '—') ?>
                                        </p>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="text-xs px-2 py-1 bg-gray-50 text-gray-600 rounded-lg border border-gray-200">
                                            <?= htmlspecialchars($schedule['department'] ?? '—') ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="text-xs px-2 py-1 rounded-full border <?= htmlspecialchars($schedule['status_class'] ?? 'bg-gray-100 text-gray-600') ?>">
                                            <?= htmlspecialchars($schedule['date_status'] ?? 'Scheduled') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($totalSchedulePages) && $totalSchedulePages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Showing <?= (($schedulePage - 1) * $schedulePerPage) + 1 ?> -
                        <?= min($schedulePage * $schedulePerPage, $totalSchedules) ?>
                        of <?= $totalSchedules ?> schedules
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($schedulePage > 1): ?>
                            <a href="?tab=shift&schedule_page=<?= $schedulePage - 1 ?>&schedule_filter=<?= $scheduleFilter ?? 'upcoming' ?>&schedule_dept=<?= $scheduleDepartmentFilter ?? '' ?>&schedule_employee=<?= $scheduleEmployeeFilter ?? '' ?>"
                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:border-gray-300 transition-colors duration-200">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalSchedulePages; $i++): ?>
                            <?php if ($i == $schedulePage): ?>
                                <span
                                    class="w-8 h-8 rounded-lg bg-gray-800 text-white flex items-center justify-center text-xs font-medium"><?= $i ?></span>
                            <?php elseif ($i >= $schedulePage - 2 && $i <= $schedulePage + 2): ?>
                                <a href="?tab=shift&schedule_page=<?= $i ?>&schedule_filter=<?= $scheduleFilter ?? 'upcoming' ?>&schedule_dept=<?= $scheduleDepartmentFilter ?? '' ?>&schedule_employee=<?= $scheduleEmployeeFilter ?? '' ?>"
                                    class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-xs text-gray-600 hover:border-gray-300 transition-colors duration-200">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($schedulePage < $totalSchedulePages): ?>
                            <a href="?tab=shift&schedule_page=<?= $schedulePage + 1 ?>&schedule_filter=<?= $scheduleFilter ?? 'upcoming' ?>&schedule_dept=<?= $scheduleDepartmentFilter ?? '' ?>&schedule_employee=<?= $scheduleEmployeeFilter ?? '' ?>"
                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:border-gray-300 transition-colors duration-200">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Shift Swap Requests Panel -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Shift Swap Requests</h3>
            <span class="text-xs bg-amber-50 text-amber-600 px-2 py-1 rounded-full border border-amber-200">
                <?= $shiftSwapPendingCount ?> pending
            </span>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                <?php if (!empty($shiftSwapRequests)): ?>
                    <?php foreach ($shiftSwapRequests as $request): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-arrows-rotate text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($request['requester_name']) ?> →
                                        <?= htmlspecialchars($request['swapper_name']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?= $request['formatted_swap_date'] ?> •
                                        <?= htmlspecialchars($request['requester_shift_name'] ?? 'Unknown') ?> to
                                        <?= htmlspecialchars($request['swapper_shift_name'] ?? 'Unknown') ?>
                                    </p>
                                    <?php if (!empty($request['reason'])): ?>
                                        <p class="text-xs text-gray-400 mt-1">Reason: <?= htmlspecialchars($request['reason']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if ($request['status'] == 'Pending'): ?>
                                    <form action="/approve-swap-request" method="POST" class="inline"
                                        onsubmit="return confirm('Approve this swap request?')">
                                        <input type="hidden" value="PATCH" name="__method">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit"
                                            class="w-7 h-7 bg-green-50 border border-green-200 text-green-600 rounded-lg hover:bg-green-100">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="/reject-swap-request" method="POST" class="inline"
                                        onsubmit="return confirm('Reject this swap request?')">
                                        <input type="hidden" value="PATCH" name="__method">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit"
                                            class="w-7 h-7 bg-red-50 border border-red-200 text-red-600 rounded-lg hover:bg-red-100">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <span class="text-xs <?= $request['status_class'] ?> px-2 py-1 rounded-full border">
                                    <?= $request['status'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-arrows-rotate text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">No shift swap requests found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Schedules by Date - Bundled Download -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Schedules by Date</h3>
                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full">
                    <?= $totalScheduleDates ?? 0 ?> total dates
                </span>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($bundledSchedules)): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-calendar-alt text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm text-gray-400">No schedules found</p>
                    <p class="text-xs text-gray-300 mt-1">Upload or create schedules to see them here</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($bundledSchedules as $bundle):
                        $statusClass = '';
                        $statusText = '';

                        if ($bundle['status'] == 'Today') {
                            $statusClass = 'bg-blue-50 text-blue-600 border-blue-200';
                            $statusText = 'Today';
                        } elseif ($bundle['status'] == 'Upcoming') {
                            $statusClass = 'bg-green-50 text-green-600 border-green-200';
                            $statusText = 'Upcoming';
                        } else {
                            $statusClass = 'bg-gray-50 text-gray-500 border-gray-200';
                            $statusText = 'Past';
                        }
                        ?>
                        <div
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100 hover:border-gray-300 transition-colors duration-200 group">
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Date Icon -->
                                <div
                                    class="w-12 h-12 <?= $bundle['status'] == 'Today' ? 'bg-blue-100' : ($bundle['status'] == 'Upcoming' ? 'bg-green-100' : 'bg-gray-100') ?> rounded-xl flex items-center justify-center">
                                    <i
                                        class="fas fa-calendar-day <?= $bundle['status'] == 'Today' ? 'text-blue-600' : ($bundle['status'] == 'Upcoming' ? 'text-green-600' : 'text-gray-500') ?> text-xl"></i>
                                </div>

                                <!-- Date Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-semibold text-gray-800">
                                            <?= $bundle['formatted_date'] ?>
                                        </h4>
                                        <span class="text-xs px-2 py-0.5 rounded-full border <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <?= $bundle['day_of_week'] ?>
                                        </span>
                                    </div>

                                    <!-- Stats Row -->
                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        <span class="text-xs bg-white px-2 py-1 rounded-md border border-gray-200">
                                            <i class="fas fa-users text-gray-500 mr-1"></i>
                                            <?= $bundle['employee_count'] ?>
                                            employee<?= $bundle['employee_count'] != 1 ? 's' : '' ?>
                                        </span>
                                        <span class="text-xs bg-white px-2 py-1 rounded-md border border-gray-200">
                                            <i class="fas fa-clock text-gray-500 mr-1"></i>
                                            <?= $bundle['total_shifts'] ?> shift<?= $bundle['total_shifts'] != 1 ? 's' : '' ?>
                                        </span>
                                        <?php if (!empty($bundle['shift_types']) && $bundle['shift_types'] != ''): ?>
                                            <span
                                                class="text-xs bg-white px-2 py-1 rounded-md border border-gray-200 max-w-xs truncate">
                                                <i class="fas fa-exchange-alt text-gray-500 mr-1"></i>
                                                <?= $bundle['shift_types'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Time Range (if available) -->
                                    <?php if (!empty($bundle['earliest_time_in']) && !empty($bundle['latest_time_out'])): ?>
                                        <p class="text-xs text-gray-400 mt-2">
                                            <i class="far fa-clock mr-1"></i>
                                            Shifts from <?= date('g:i A', strtotime($bundle['earliest_time_in'])) ?>
                                            to <?= date('g:i A', strtotime($bundle['latest_time_out'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Download Button -->
                            <a href="/download-upload?date=<?= $bundle['schedule_date'] ?>"
                                class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 flex items-center gap-2 ml-4 group-hover:shadow-sm"
                                title="Download all schedules for <?= $bundle['formatted_date'] ?>">
                                <i class="fas fa-file-excel text-green-600"></i>
                                <span class="font-medium">Download Day</span>
                                <i class="fas fa-download text-gray-400 group-hover:text-gray-600"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- View All Link -->
                <?php if ($totalScheduleDates > 10): ?>
                    <div class="mt-4 text-center">
                        <a href="/all-schedules-by-date"
                            class="text-xs text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            View all <?= $totalScheduleDates ?> dates
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // File upload functions
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const uploadForm = document.getElementById('uploadForm');

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop zone on drag over
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropZone.classList.add('border-gray-800', 'bg-gray-50');
    }

    function unhighlight() {
        dropZone.classList.remove('border-gray-800', 'bg-gray-50');
    }

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(input) {
        handleFiles(input.files);
    }

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];

            // Validate file type by extension
            const fileName_lower = file.name.toLowerCase();
            const validExtensions = ['.xlsx', '.xls'];
            const isValid = validExtensions.some(ext => fileName_lower.endsWith(ext));

            if (!isValid) {
                showNotification('Please upload a valid Excel file (.xlsx or .xls)', 'error');
                return;
            }

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showNotification('File size exceeds 10MB limit', 'error');
                return;
            }

            // Display file info
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            fileInfo.classList.remove('hidden');
            dropZone.classList.add('hidden');

            // Enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function clearFile() {
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        dropZone.classList.remove('hidden');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Handle form submission with AJAX
    uploadForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!fileInput.files.length) {
            showNotification('Please select a file to upload', 'error');
            return;
        }

        const formData = new FormData(this);

        // Disable submit button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Uploading ${Math.round(percentComplete)}%`;
            }
        });

        xhr.addEventListener('load', function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        showNotification(response.message, 'success');

                        // Clear the form
                        clearFile();

                        // Refresh the page to show new schedules
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification(response.message || 'Upload failed', 'error');
                    }
                } catch (error) {
                    console.error('Parse error:', error);
                    showNotification('Invalid server response', 'error');
                }
            } else {
                showNotification('Upload failed with status: ' + xhr.status, 'error');
            }

            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Upload and Process';
        });

        xhr.addEventListener('error', function () {
            showNotification('Upload failed. Please try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Upload and Process';
        });

        xhr.open('POST', uploadForm.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });

    // Notification function using your styled notification system
    function showNotification(message, type = 'success') {
        const notificationContainer = document.querySelector('[role="alert"]');

        if (notificationContainer) {
            const notification = document.createElement('div');
            notification.className = 'relative pointer-events-auto group animate-message-pop';
            notification.setAttribute('role', 'alert');
            notification.setAttribute('data-notification', 'upload-' + Date.now());

            const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-rose-500';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

            notification.innerHTML = `
                <div class="${bgColor} rounded-2xl shadow-lg border border-${type === 'success' ? 'emerald' : 'rose'}-600 p-4">
                    <div class="absolute right-4 -bottom-2 w-4 h-4 ${bgColor} border-b border-r border-${type === 'success' ? 'emerald' : 'rose'}-600 transform rotate-45"></div>
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas ${icon} text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white font-medium leading-relaxed break-words">${message}</p>
                        </div>
                        <button onclick="this.closest('[data-notification]').remove()"
                            class="shrink-0 w-8 h-8 rounded-lg text-white/60 hover:text-white hover:bg-white/10">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;

            notificationContainer.appendChild(notification);

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        } else {
            alert(message);
        }
    }

    // Add fade out animation if not already present
    if (!document.querySelector('#fadeOutStyle')) {
        const style = document.createElement('style');
        style.id = 'fadeOutStyle';
        style.textContent = `
            @keyframes fadeOut {
                to {
                    opacity: 0;
                    transform: scale(0.9) translateY(-10px);
                }
            }
        `;
        document.head.appendChild(style);
    }
</script>