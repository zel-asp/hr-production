<div class="tab-content" id="timesheet-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Timesheet Management</h2>
            <p class="text-gray-500 text-sm mt-1">Review and approve employee timesheets</p>
        </div>
    </div>

    <!-- Period Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Period</p>
                    <p class="text-lg font-semibold text-gray-800"><?= $filterLabel ?></p>
                    <?php if ($timesheetFilter !== 'all'): ?>
                        <p class="text-xs text-gray-400 mt-1"><?= date('M j', strtotime($dateRangeStart)) ?> -
                            <?= date('M j, Y', strtotime($dateRangeEnd)) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-xs text-gray-400 mt-1">All historical records</p>
                    <?php endif; ?>
                </div>
                <?php if ($filterApplied): ?>
                    <a href="?timesheet_filter=all&timesheet_page=1&tab=timesheet"
                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded-lg transition-colors duration-200 flex items-center gap-1">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Hours</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($totalHoursPeriod, 1) ?></p>
            <div class="flex gap-3 mt-1 text-xs text-gray-500">
                <span>Regular: <?= number_format($totalRegularPeriod, 1) ?></span>
                <span>|</span>
                <span>OT: <?= number_format($totalOvertimePeriod, 1) ?></span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Employees with Hours</p>
            <p class="text-2xl font-bold text-gray-800"><?= $employeesWithHours ?></p>
            <p class="text-xs text-gray-400 mt-1">Out of <?= $totalTimesheets ?> active employees</p>
        </div>
    </div>

    <!-- Main Timesheet Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Filter and Pagination Controls -->
        <div
            class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-500">Filter:</label>
                    <select
                        class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200"
                        onchange="window.location.href = '?timesheet_filter=' + this.value + '&timesheet_page=1&tab=timesheet'">
                        <option value="all" <?= $timesheetFilter == 'all' ? 'selected' : '' ?>>All Time</option>
                        <option value="this_week" <?= $timesheetFilter == 'this_week' ? 'selected' : '' ?>>This Week
                        </option>
                        <option value="last_week" <?= $timesheetFilter == 'last_week' ? 'selected' : '' ?>>Last Week
                        </option>
                        <option value="this_month" <?= $timesheetFilter == 'this_month' ? 'selected' : '' ?>>This Month
                        </option>
                    </select>
                </div>

                <?php if ($filterApplied): ?>
                    <a href="?timesheet_filter=all&timesheet_page=1&tab=timesheet"
                        class="text-xs text-gray-500 hover:text-gray-700 bg-white border border-gray-200 px-2 py-1 rounded-lg transition-colors duration-200 flex items-center gap-1">
                        <i class="fas fa-times-circle"></i>
                        Clear filter
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($totalTimesheetPages > 1): ?>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Page <?= $timesheetPage ?> of <?= $totalTimesheetPages ?></span>
                    <div class="flex gap-1">
                        <?php if ($timesheetPage > 1): ?>
                            <a href="?timesheet_filter=<?= $timesheetFilter ?>&timesheet_page=<?= $timesheetPage - 1 ?>"
                                class="w-7 h-7 flex items-center justify-center text-xs rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($timesheetPage < $totalTimesheetPages): ?>
                            <a href="?timesheet_filter=<?= $timesheetFilter ?>&timesheet_page=<?= $timesheetPage + 1 ?>&tab=timesheet"
                                class="w-7 h-7 flex items-center justify-center text-xs rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active Filter Indicator -->
        <?php if ($filterApplied): ?>
            <div
                class="mx-6 mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-filter text-gray-400"></i>
                    <span>Active filter: <span class="font-medium text-gray-800"><?= $filterLabel ?></span>
                        <span class="text-gray-400 text-xs ml-1">(<?= date('M j', strtotime($dateRangeStart)) ?> -
                            <?= date('M j, Y', strtotime($dateRangeEnd)) ?>)</span>
                    </span>
                </div>
                <a href="?timesheet_filter=all&timesheet_page=1&tab=timesheet"
                    class="text-xs bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1">
                    <i class="fas fa-times"></i>
                    Remove filter
                </a>
            </div>
        <?php endif; ?>

        <!-- Timesheets Table -->
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Week Ending</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Regular Hours</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Overtime</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Hours</th>
                            <th
                                class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($timesheets)): ?>
                            <?php foreach ($timesheets as $timesheet):
                                $totalHours = $timesheet['regular_hours'] + $timesheet['overtime_hours'];
                                $weekEnding = $timesheet['last_attendance_date'] ? date('M j, Y', strtotime($timesheet['last_attendance_date'])) : 'No records';

                                // Determine status
                                if ($totalHours == 0) {
                                    $status = 'No Hours';
                                    $statusClass = 'bg-gray-100 text-gray-600 border border-gray-200';
                                } elseif ($timesheet['timesheet_status'] == 'approved') {
                                    $status = 'Approved';
                                    $statusClass = 'bg-green-50 text-green-700 border border-green-200';
                                } elseif ($timesheet['timesheet_status'] == 'pending') {
                                    $status = 'pending';
                                    $statusClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                                } else {
                                    $status = 'pending';
                                    $statusClass = 'bg-red-50 text-red-700 border border-red-200';
                                }
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($timesheet['full_name']) ?>
                                        </div>
                                        <div class="text-xs text-gray-400"><?= htmlspecialchars($timesheet['position'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        <?= htmlspecialchars($timesheet['department'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= $weekEnding ?></td>
                                    <td class="py-3 px-4 text-right text-sm font-medium text-gray-800">
                                        <?= number_format($timesheet['regular_hours'], 1) ?>
                                    </td>
                                    <td
                                        class="py-3 px-4 text-right text-sm font-medium <?= $timesheet['overtime_hours'] > 0 ? 'text-gray-800' : 'text-gray-400' ?>">
                                        <?= number_format($timesheet['overtime_hours'], 1) ?>
                                    </td>
                                    <td class="py-3 px-4 text-right text-sm font-semibold text-gray-800">
                                        <?= number_format($totalHours, 1) ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex justify-center">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                                <?= $status ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- View Button - Always enabled with tooltip -->
                                            <button onclick="openModal('timesheetModal<?= $timesheet['employee_id'] ?>')"
                                                class="group relative w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-50 rounded-lg transition-all duration-200 border border-gray-200 shadow-sm hover:shadow"
                                                title="View timesheet details">
                                                <i class="fas fa-eye text-sm"></i>
                                                <span
                                                    class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                    View Details
                                                </span>
                                            </button>

                                            <?php
                                            // Get status (case-insensitive)
                                            $dbStatus = strtolower($timesheet['timesheet_status'] ?? '');
                                            $isApproved = $dbStatus === 'approved';
                                            $isRejected = $dbStatus === 'rejected';
                                            $isPending = !$isApproved && !$isRejected && $totalHours > 0;
                                            ?>

                                            <?php if ($isApproved): ?>
                                                <!-- APPROVED - Green success badge -->
                                                <div class="group relative">
                                                    <div
                                                        class="w-9 h-9 flex items-center justify-center text-green-600 bg-green-50 rounded-lg border border-green-200 cursor-default">
                                                        <i class="fas fa-check-circle text-sm"></i>
                                                    </div>
                                                    <span
                                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                        Approved
                                                    </span>
                                                </div>

                                            <?php elseif ($isRejected): ?>
                                                <!-- REJECTED - Red danger badge -->
                                                <div class="group relative">
                                                    <div
                                                        class="w-9 h-9 flex items-center justify-center text-red-600 bg-red-50 rounded-lg border border-red-200 cursor-default">
                                                        <i class="fas fa-times-circle text-sm"></i>
                                                    </div>
                                                    <span
                                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                        Rejected
                                                    </span>
                                                </div>

                                            <?php elseif ($isPending): ?>
                                                <!-- PENDING - Active approve button with loading state -->
                                                <form action="/approve-timesheet" method="POST" class="inline-block">
                                                    <input type="hidden" name="__method" value="PATCH">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                                    <?php if (!empty($timesheet['summary_id'])): ?>
                                                        <input type="hidden" name="summary_id" value="<?= $timesheet['summary_id'] ?>">
                                                    <?php else: ?>
                                                        <input type="hidden" name="employee_id"
                                                            value="<?= $timesheet['employee_id'] ?>">
                                                        <?php if (!empty($timesheet['summary_period_start']) && !empty($timesheet['summary_period_end'])): ?>
                                                            <input type="hidden" name="period_start"
                                                                value="<?= $timesheet['summary_period_start'] ?>">
                                                            <input type="hidden" name="period_end"
                                                                value="<?= $timesheet['summary_period_end'] ?>">
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <button type="submit"
                                                        class="group relative w-9 h-9 flex items-center justify-center text-green-600 hover:text-white bg-green-50 hover:bg-green-600 rounded-lg transition-all duration-200 border border-green-200 hover:border-green-600 shadow-sm hover:shadow-md"
                                                        onclick="return confirm('Approve timesheet for <?= htmlspecialchars($timesheet['full_name']) ?>?')"
                                                        title="Approve timesheet">
                                                        <i
                                                            class="fas fa-check text-sm group-hover:scale-110 transition-transform duration-200"></i>
                                                        <span
                                                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                            Approve
                                                        </span>
                                                    </button>
                                                </form>

                                            <?php else: ?>
                                                <!-- NO HOURS - Gray disabled badge -->
                                                <div class="group relative">
                                                    <div
                                                        class="w-9 h-9 flex items-center justify-center text-gray-400 bg-gray-100 rounded-lg border border-gray-200 cursor-default">
                                                        <i class="fas fa-clock text-sm"></i>
                                                    </div>
                                                    <span
                                                        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                        No Hours
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-clock text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-base font-medium text-gray-600">No timesheets found</p>
                                        <p class="text-sm text-gray-400 mt-1">No timesheet records available for the
                                            selected period</p>
                                        <?php if ($filterApplied): ?>
                                            <a href="?timesheet_filter=all&timesheet_page=1&tab=timesheet"
                                                class="mt-4 text-sm text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                                                <i class="fas fa-times"></i>
                                                Clear filter to see all records
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Pagination -->
            <?php if ($totalTimesheetPages > 1): ?>
                <div class="flex justify-center items-center gap-2 mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mr-2">Page <?= $timesheetPage ?> of <?= $totalTimesheetPages ?></p>
                    <?php for ($i = 1; $i <= $totalTimesheetPages; $i++): ?>
                        <a href="?timesheet_filter=<?= $timesheetFilter ?>&timesheet_page=<?= $i ?>&tab=timesheet"
                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors duration-200
                            <?= $i == $timesheetPage ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <!-- Summary Footer -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-sm">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="text-gray-600"><span class="font-medium text-gray-800">Total Regular:</span>
                            <?= number_format($totalRegularPeriod, 1) ?> hrs</span>
                        <span class="text-gray-600"><span class="font-medium text-gray-800">Total OT:</span>
                            <?= number_format($totalOvertimePeriod, 1) ?> hrs</span>
                        <span class="text-gray-600"><span class="font-medium text-gray-800">Grand Total:</span>
                            <?= number_format($totalHoursPeriod, 1) ?> hrs</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">Pending</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">Approved</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">No Hours</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timesheet Modals (one for each employee) -->
<?php if (!empty($timesheets)): ?>
    <?php foreach ($timesheets as $timesheet):
        $totalHours = $timesheet['regular_hours'] + $timesheet['overtime_hours'];
        $modalId = 'timesheetModal' . $timesheet['employee_id'];

        // Decode attendance records
        $attendanceRecords = [];
        if (!empty($timesheet['attendance_records'])) {
            $attendanceRecords = json_decode($timesheet['attendance_records'], true);
        }

        // Determine status
        if ($totalHours == 0) {
            $status = 'No Hours';
            $statusClass = 'bg-gray-100 text-gray-600 border border-gray-200';
        } elseif ($timesheet['timesheet_status'] == 'Approved') {
            $status = 'Approved';
            $statusClass = 'bg-green-50 text-green-700 border border-green-200';
        } else {
            $status = 'Pending';
            $statusClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
        }
        ?>
        <div id="<?= $modalId ?>" class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span
                                class="text-blue-600 font-semibold text-lg"><?= strtoupper(substr($timesheet['full_name'], 0, 1)) ?></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Timesheet Details</h3>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($timesheet['full_name']) ?> •
                                <?= htmlspecialchars($timesheet['position'] ?? '') ?>
                            </p>
                        </div>
                    </div>
                    <button onclick="closeModal('<?= $modalId ?>')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Period Summary -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 mb-6 border border-blue-100">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-blue-600 uppercase tracking-wider">Period</p>
                                <p class="text-base font-semibold text-gray-800"><?= $filterLabel ?></p>
                                <?php if ($timesheetFilter !== 'all'): ?>
                                    <p class="text-xs text-blue-500"><?= date('M j', strtotime($dateRangeStart)) ?> -
                                        <?= date('M j, Y', strtotime($dateRangeEnd)) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-xs text-blue-500">All time</p>
                                <?php endif; ?>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs text-gray-500">Regular Hours</p>
                                <p class="text-xl font-bold text-gray-800"><?= number_format($timesheet['regular_hours'], 1) ?>
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs text-gray-500">Overtime Hours</p>
                                <p class="text-xl font-bold text-gray-800"><?= number_format($timesheet['overtime_hours'], 1) ?>
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs text-gray-500">Total Hours</p>
                                <p class="text-xl font-bold text-gray-800"><?= number_format($totalHours, 1) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Info Card -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Department</p>
                            <p class="text-base font-medium text-gray-800">
                                <?= htmlspecialchars($timesheet['department'] ?? 'N/A') ?>
                            </p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Hourly Rate</p>
                            <p class="text-base font-medium text-gray-800">
                                ₱<?= number_format($timesheet['hourly_rate'] ?? 0, 2) ?></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Total Days Worked</p>
                            <p class="text-base font-medium text-gray-800">
                                <?= $timesheetFilter !== 'all' ? ($timesheet['period_attendance_days'] ?? 0) : ($timesheet['total_attendance_days'] ?? 0) ?>
                                days
                            </p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Status</p>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $statusClass ?>">
                                <?= $status ?>
                            </span>
                        </div>
                    </div>

                    <!-- Daily Attendance Records -->
                    <?php if (!empty($attendanceRecords)): ?>
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-blue-500"></i>
                                Daily Attendance Records
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full ml-2">
                                    <?= count($attendanceRecords) ?> entries
                                </span>
                            </h4>
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Date</th>
                                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Clock In</th>
                                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Clock Out</th>
                                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500">Regular</th>
                                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500">OT</th>
                                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500">Total</th>
                                            <th class="text-center py-3 px-4 text-xs font-medium text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $displayRecords = array_slice($attendanceRecords, 0, 15);
                                        foreach ($displayRecords as $day):
                                            $dayTotal = $day['regular_hours'] + $day['overtime_hours'];
                                            $lateClass = '';
                                            $lateText = '';
                                            if (($day['late_minutes'] ?? 0) > 0) {
                                                $lateClass = 'bg-yellow-50';
                                                $lateText = 'Late (' . $day['late_minutes'] . 'm)';
                                            }
                                            ?>
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 <?= $lateClass ?>">
                                                <td class="py-3 px-4">
                                                    <span class="font-medium"><?= date('M j, Y', strtotime($day['date'])) ?></span>
                                                    <span
                                                        class="text-xs text-gray-400 ml-2"><?= date('D', strtotime($day['date'])) ?></span>
                                                </td>
                                                <td class="py-3 px-4 text-gray-600"><?= $day['clock_in'] ?? '—' ?></td>
                                                <td class="py-3 px-4 text-gray-600"><?= $day['clock_out'] ?? '—' ?></td>
                                                <td class="py-3 px-4 text-right"><?= number_format($day['regular_hours'], 1) ?></td>
                                                <td class="py-3 px-4 text-right"><?= number_format($day['overtime_hours'], 1) ?></td>
                                                <td class="py-3 px-4 text-right font-medium"><?= number_format($dayTotal, 1) ?></td>
                                                <td class="py-3 px-4 text-center">
                                                    <?php if ($lateText): ?>
                                                        <span class="text-xs text-yellow-600"><?= $lateText ?></span>
                                                    <?php else: ?>
                                                        <span class="text-xs text-green-600">On Time</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($attendanceRecords) > 15): ?>
                                    <div class="p-3 text-center border-t border-gray-100">
                                        <p class="text-xs text-gray-400">Showing 15 of <?= count($attendanceRecords) ?> records</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mb-6 p-8 text-center bg-gray-50 rounded-lg border border-gray-200">
                            <i class="fas fa-calendar-times text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No attendance records found for this period</p>
                        </div>
                    <?php endif; ?>

                    <!-- Approval Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mt-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full 
                                <?= $status == 'Approved' ? 'bg-green-100' : ($status == 'Pending' ? 'bg-yellow-100' : 'bg-gray-100') ?> 
                                flex items-center justify-center">
                                    <i
                                        class="fas 
                                    <?= $status == 'Approved' ? 'fa-check-circle text-green-600' :
                                        ($status == 'Pending' ? 'fa-clock text-yellow-600' : 'fa-minus-circle text-gray-600') ?>">
                                    </i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Current Status</p>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                        <?= $status ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($status != 'Approved' && $totalHours > 0): ?>
                                <div class="flex gap-2">
                                    <button onclick="closeModal('<?= $modalId ?>')"
                                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        Cancel
                                    </button>
                                    <form action="/approve-timesheet" method="POST" class="inline-block">
                                        <input type="hidden" name="__method" value="PATCH">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                        <?php if (!empty($timesheet['summary_id'])): ?>
                                            <input type="hidden" name="summary_id" value="<?= $timesheet['summary_id'] ?>">
                                        <?php else: ?>
                                            <input type="hidden" name="employee_id" value="<?= $timesheet['employee_id'] ?>">
                                            <?php if (!empty($timesheet['summary_period_start']) && !empty($timesheet['summary_period_end'])): ?>
                                                <input type="hidden" name="period_start" value="<?= $timesheet['summary_period_start'] ?>">
                                                <input type="hidden" name="period_end" value="<?= $timesheet['summary_period_end'] ?>">
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <button
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center gap-2">
                                            <i class="fas fa-check"></i>
                                            Approve Timesheet
                                        </button>
                                    </form>


                                </div>
                            <?php else: ?>
                                <button onclick="closeModal('<?= $modalId ?>')"
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition-colors duration-200">
                                    Close
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>