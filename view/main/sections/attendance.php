<div class="tab-content" id="time-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Time and Attendance</h2>
            <p class="text-gray-500 text-sm mt-1">Real-time employee attendance tracking</p>
        </div>
        <!-- <button class="btn-primary" onclick="openModal('manualTimeModal')">
            <i class="fas fa-plus"></i>
            Manual Entry
        </button> -->
    </div>

    <!-- Payroll Cutoff Period Banner -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-gray-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Current Payroll Cutoff Period</p>
                    <p class="text-lg font-semibold text-gray-800">
                        <?= date('M j', strtotime($cutoffStart)) ?> - <?= date('M j, Y', strtotime($cutoffEnd)) ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">Cutoff Type:</span>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                    <?= $cutoffType ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Cutoff Summary Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-gray-500"></i>
                Cutoff Period Summary
            </h3>
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                <i class="fas fa-calendar-check mr-1"></i>
                Cutoff Date: <?= date('F j, Y', strtotime($cutoffEnd)) ?>
            </span>
        </div>

        <!-- Total Hours All Employees -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Hours (All Employees)</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($totalCutoffHours, 1) ?></p>
                <div class="flex gap-3 mt-1 text-xs text-gray-500">
                    <span>Regular: <?= number_format($totalRegularHours, 1) ?></span>
                    <span>|</span>
                    <span>OT: <?= number_format($totalOvertimeHours, 1) ?></span>
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Average Hours per Employee</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($averageHoursPerEmployee, 1) ?></p>
                <p class="text-xs text-gray-500 mt-1">Based on <?= $employeesWithAttendance ?> employees with attendance
                </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Projected Total Payout</p>
                <p class="text-2xl font-bold text-gray-800">₱<?= number_format($projectedPayout, 2) ?></p>
                <p class="text-xs text-gray-500 mt-1">Including overtime premiums</p>
            </div>
        </div>

        <!-- Department Breakdown -->
        <?php if (!empty($departmentHours)): ?>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php foreach ($departmentHours as $dept): ?>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 hover:shadow-sm transition-shadow duration-200">
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($dept['department']) ?></p>
                        <p class="text-lg font-semibold text-gray-800"><?= number_format($dept['total_hours'], 1) ?> <span
                                class="text-xs font-normal text-gray-500">hrs</span></p>
                        <p class="text-xs text-gray-400"><?= $dept['employee_count'] ?> employees</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Today's Attendance Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Present Today</p>
            <p class="text-2xl font-bold text-gray-800"><?= $presentToday ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">On Leave</p>
            <p class="text-2xl font-bold text-gray-800"><?= $onLeaveToday ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Late</p>
            <p class="text-2xl font-bold text-gray-800"><?= $lateToday ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Absent</p>
            <p class="text-2xl font-bold text-gray-800"><?= $absentToday ?></p>
        </div>
    </div>

    <!-- Attendance List with Running Hours -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Today's Attendance</h3>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <?php if ($totalAttendancePages > 1): ?>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500">Page <?= $attendancePage ?> of
                            <?= $totalAttendancePages ?></span>
                        <div class="flex gap-1">
                            <?php if ($attendancePage > 1): ?>
                                <a href="?attendance_page=<?= $attendancePage - 1 ?>"
                                    class="w-7 h-7 flex items-center justify-center text-xs rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($attendancePage < $totalAttendancePages): ?>
                                <a href="?attendance_page=<?= $attendancePage + 1 ?>"
                                    class="w-7 h-7 flex items-center justify-center text-xs rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="flex items-center gap-1 text-xs text-gray-400">
                    <i class="fas fa-info-circle"></i>
                    <span>Cutoff running hours shown</span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Shift
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Time
                                In</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Time
                                Out</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Today's Hours</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Cutoff
                                Total</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($todaysAttendance)): ?>
                            <?php foreach ($todaysAttendance as $attendance): ?>
                                <?php
                                $statusClass = '';
                                $statusText = 'Present';
                                $statusBadgeClass = 'bg-green-50 text-green-700 border border-green-200';

                                if ($attendance['late_status'] == 'late') {
                                    $statusBadgeClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                                    $statusText = 'Late (' . $attendance['late_minutes'] . ' min)';
                                } elseif ($attendance['status'] == 'clocked_out') {
                                    $statusText = 'Completed';
                                }
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-2 h-2 rounded-full <?= $attendance['late_status'] == 'late' ? 'bg-yellow-500' : 'bg-green-500' ?>"></span>
                                            <span
                                                class="text-sm font-medium text-gray-800"><?= htmlspecialchars($attendance['full_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($attendance['department'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($attendance['shift_name']): ?>
                                            <div class="text-sm text-gray-800"><?= htmlspecialchars($attendance['shift_name']) ?>
                                            </div>
                                            <span class="text-xs text-gray-400">
                                                <?= date('h:i A', strtotime($attendance['shift_start'])) ?> -
                                                <?= date('h:i A', strtotime($attendance['shift_end'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">No shift</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= $attendance['formatted_clock_in'] ?? '—' ?></td>
                                    <td class="py-3 text-sm text-gray-600"><?= $attendance['formatted_clock_out'] ?? '—' ?></td>
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        <?= $attendance['todays_hours'] ? number_format($attendance['todays_hours'], 1) . ' hrs' : '—' ?>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            <?= number_format($attendance['cutoff_total_hours'], 1) ?> hrs
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $statusBadgeClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-clock text-3xl text-gray-300"></i>
                                        <p class="text-sm">No attendance records for today</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for attendance -->
            <?php if ($totalAttendancePages > 1): ?>
                <div class="flex justify-center items-center gap-2 mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mr-2">Page <?= $attendancePage ?> of <?= $totalAttendancePages ?></p>
                    <?php for ($i = 1; $i <= $totalAttendancePages; $i++): ?>
                        <a href="?attendance_page=<?= $i ?>"
                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors duration-200
                            <?= $i == $attendancePage ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <!-- Cutoff Legend -->
            <div class="mt-4 flex items-center justify-end gap-4 text-xs text-gray-400">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                    <span>Cutoff running total (<?= date('M j', strtotime($cutoffStart)) ?> -
                        <?= date('M j', strtotime($cutoffEnd)) ?>)</span>
                </div>
                <div class="flex items-center gap-1">
                    <i class="fas fa-clock"></i>
                    <span>Updated in real-time</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Cutoff Schedule Reminder -->
    <div
        class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <i class="fas fa-info-circle text-gray-400"></i>
            <span>
                <span class="font-medium">Next cutoff:</span>
                <?php
                if ($cutoffType == '1st Cutoff') {
                    $nextCutoffStart = date('Y-m-06');
                    $nextCutoffEnd = date('Y-m-20');
                    echo date('M j', strtotime($nextCutoffStart)) . ' - ' . date('M j, Y', strtotime($nextCutoffEnd)) . ' (2nd Cutoff)';
                } else {
                    if ($currentDay <= 20) {
                        $nextCutoffStart = date('Y-m-21');
                        $nextCutoffEnd = date('Y-m-05', strtotime('+1 month'));
                    } else {
                        $nextCutoffStart = date('Y-m-21');
                        $nextCutoffEnd = date('Y-m-05', strtotime('+1 month'));
                    }
                    echo date('M j', strtotime($nextCutoffStart)) . ' - ' . date('M j, Y', strtotime($nextCutoffEnd)) . ' (1st Cutoff)';
                }
                ?>
            </span>
        </div>
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
            Current: <?= $cutoffType ?>
        </span>
    </div>
</div>