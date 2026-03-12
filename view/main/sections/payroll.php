<div class="tab-content" id="payroll-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Payroll Management</h2>
            <p class="text-gray-500 text-sm mt-1">Process payroll and manage employee compensation</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Status Summary Badges -->
            <div
                class="flex items-center gap-3 bg-linear-to-r from-gray-50 to-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                <span class="inline-flex items-center gap-1.5 text-xs">
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full ring-2 ring-green-100"></span>
                    <span class="text-gray-600 font-medium">Approved:</span>
                    <span class="text-gray-900 font-bold"><?= $payrollApprovedCount ?? 0 ?></span>
                </span>
                <span class="text-gray-300">|</span>
                <span class="inline-flex items-center gap-1.5 text-xs">
                    <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full ring-2 ring-yellow-100"></span>
                    <span class="text-gray-600 font-medium">Pending:</span>
                    <span class="text-gray-900 font-bold"><?= $payrollPendingCount ?? 0 ?></span>
                </span>
                <span class="text-gray-300">|</span>
                <span class="inline-flex items-center gap-1.5 text-xs">
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full ring-2 ring-blue-100"></span>
                    <span class="text-gray-600 font-medium">Total:</span>
                    <span class="text-gray-900 font-bold"><?= $payrollTotalEmployees ?? 0 ?></span>
                </span>
            </div>

            <!-- Process All Button with Status -->
            <form action="/process-all-payroll" method="POST" class="relative group">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="process_all" value="1">
                <input type="hidden" name="period_start" value="<?= $payrollPeriodStart ?>">
                <input type="hidden" name="period_end" value="<?= $payrollPeriodEnd ?>">

                <?php
                $payrollReadyForProcessing = 0;
                $payrollTotalApproved = 0;

                foreach ($payrollEmployees as $emp) {
                    if ($emp['attendance_summary_status'] == 'approved') {
                        $payrollTotalApproved++;
                        if ($emp['status'] != 'Processed' && $emp['status'] != 'Processing') {
                            $payrollReadyForProcessing++;
                        }
                    }
                }

                $hasReadyData = ($payrollReadyForProcessing > 0);
                $buttonDisabled = !$hasReadyData;
                $buttonTitle = $hasReadyData
                    ? 'Process payroll for ' . $payrollReadyForProcessing . ' employees with approved data'
                    : ($payrollTotalApproved > 0 ? 'All approved attendances have been processed' : 'No approved attendance to process');
                ?>

                <button type="submit" <?= $buttonDisabled ? 'disabled' : '' ?>
                    class="
                    <?= $buttonDisabled ? 'bg-gray-200 cursor-not-allowed opacity-60' : 'bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white shadow-lg shadow-blue-200' ?> 
                    px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 whitespace-nowrap font-medium" <?= $buttonDisabled ? 'disabled' : '' ?>
                    onclick="return <?= $hasReadyData ? 'confirm(\'Process payroll for ' . $payrollReadyForProcessing . ' employees with approved data? This may take a moment.\')' : 'false' ?>"
                    title="<?= $buttonTitle ?>">

                    <i class="fas fa-play-circle text-sm"></i>
                    Process All

                    <?php if ($payrollReadyForProcessing > 0): ?>
                        <span class="ml-1 px-1.5 py-0.5 bg-white/30 rounded-full text-xs font-semibold">
                            <?= $payrollReadyForProcessing ?>
                        </span>
                    <?php endif; ?>
                </button>

                <!-- Tooltip on hover (only shows when button is disabled) -->
                <?php if (!$hasReadyData): ?>
                    <div
                        class="absolute top-full mt-2 right-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2.5 px-3 whitespace-nowrap shadow-xl">
                            <div class="flex items-center gap-2">
                                <?php if ($payrollTotalApproved > 0): ?>
                                    <i class="fas fa-check-circle text-green-400 text-xs"></i>
                                    <span>All approved (<?= $payrollTotalApproved ?>) already processed</span>
                                <?php else: ?>
                                    <i class="fas fa-info-circle text-blue-400 text-xs"></i>
                                    <span>No approved attendance to process</span>
                                <?php endif; ?>
                            </div>
                            <!-- Tooltip arrow -->
                            <div class="absolute -top-1 right-6 w-2 h-2 bg-gray-900 transform rotate-45"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Payroll Period Info -->
    <div
        class="bg-white border border-gray-200 rounded-2xl p-5 mb-6 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-linear-to-br from-blue-50 to-indigo-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Current Payroll Period</p>
                    <p class="text-xl font-bold text-gray-900 mt-0.5"><?= $payrollPeriodLabel ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xl">
                <span class="text-xs font-medium text-gray-500">Payroll Date:</span>
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-gray-700 border border-gray-200 shadow-sm">
                    <i class="far fa-calendar-check mr-1.5 text-gray-400"></i>
                    <?= date('F j, Y', strtotime($payrollPayDate)) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-500">Filter by:</span>
                <div class="relative">
                    <select name="payroll_status" onchange="applyPayrollFilter()"
                        class="text-sm bg-white border border-gray-200 rounded-lg pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 appearance-none">
                        <option value="">All Status</option>
                        <option value="Processed" <?= $payrollStatusFilter == 'Processed' ? 'selected' : '' ?>>Processed
                        </option>
                        <option value="Processing" <?= $payrollStatusFilter == 'Processing' ? 'selected' : '' ?>>Processing
                        </option>
                        <option value="Pending" <?= $payrollStatusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Rejected" <?= $payrollStatusFilter == 'Rejected' ? 'selected' : '' ?>>Rejected
                        </option>
                    </select>
                    <i
                        class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>

                <div class="relative">
                    <select name="payroll_department" onchange="applyPayrollFilter()"
                        class="text-sm bg-white border border-gray-200 rounded-lg pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 appearance-none">
                        <option value="">All Departments</option>
                        <?php foreach ($payrollDepartments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department']) ?>"
                                <?= $payrollDepartmentFilter == $dept['department'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['department']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i
                        class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <?php if (!empty($payrollStatusFilter) || !empty($payrollDepartmentFilter)): ?>
                <a href="?tab=payroll&payroll_page=1"
                    class="text-sm text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors duration-200">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payroll Summary Stats -->
    <div class="grid grid-cols-4 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div
            class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Gross Pay</p>
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= formatPayrollCurrency($payrollTotalGross) ?></p>
            <p class="text-xs text-gray-400 mt-1">Before deductions</p>
        </div>

        <div
            class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Net Pay</p>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-green-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= formatPayrollCurrency($payrollTotalNet) ?></p>
            <p class="text-xs text-gray-400 mt-1">Take-home pay</p>
        </div>

        <div
            class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Claims</p>
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-amber-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600"><?= formatPayrollCurrency($payrollTotalClaims) ?></p>
            <p class="text-xs text-gray-400 mt-1">Additional compensation</p>
        </div>

        <div
            class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Employees</p>
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= $payrollTotalEmployees ?></p>
            <div class="flex items-center gap-3 mt-1">
                <span
                    class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"><?= $payrollProcessedCount ?>
                    processed</span>
                <span
                    class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium"><?= $payrollPendingCount ?>
                    pending</span>
            </div>
        </div>
    </div>

    <!-- Payroll List -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div
            class="px-6 py-5 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-gray-900">Payroll Summary</h3>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                    <i class="far fa-user mr-1.5 text-gray-400"></i>
                    <?= $payrollTotalEmployees ?> employees
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs bg-green-50 px-3 py-1.5 rounded-lg">
                    <span class="w-2 h-2 bg-green-500 rounded-full ring-2 ring-green-100"></span>
                    <span class="text-gray-600 font-medium">Processed: <?= $payrollProcessedCount ?></span>
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs bg-yellow-50 px-3 py-1.5 rounded-lg">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full ring-2 ring-yellow-100"></span>
                    <span class="text-gray-600 font-medium">Pending: <?= $payrollPendingCount ?></span>
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-100">
                            <th
                                class="text-left py-4 pl-4 pr-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Employee</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Regular Hours</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Overtime</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Claims</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Gross Pay</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Deductions</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Net Pay</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th
                                class="text-left py-4 pl-6 pr-4 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payrollEmployees)): ?>
                            <?php foreach ($payrollEmployees as $emp): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition-colors duration-150 group">
                                    <td class="py-4 pl-4 pr-6">
                                        <div class="flex items-center gap-3 min-w-45">
                                            <div
                                                class="w-10 h-10 bg-linear-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center text-gray-700 text-sm font-bold shrink-0 ring-2 ring-white shadow-sm">
                                                <?= $emp['initials'] ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">
                                                    <?= htmlspecialchars($emp['full_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-400 truncate">
                                                    <?= htmlspecialchars($emp['position']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Regular Hours Column with Indicator -->
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-semibold text-gray-900 whitespace-nowrap"><?= round($emp['total_regular_hours']) ?>
                                                hrs</span>
                                            <?php if ($emp['total_regular_hours'] == 0 && $emp['attendance_summary_status'] == 'none'): ?>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">No attendance summary</span>
                                            <?php elseif ($emp['attendance_summary_status'] == 'pending'): ?>
                                                <span
                                                    class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full inline-block w-fit mt-1 whitespace-nowrap">Pending
                                                    approval</span>
                                            <?php elseif ($emp['attendance_summary_status'] == 'rejected'): ?>
                                                <span
                                                    class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full inline-block w-fit mt-1 whitespace-nowrap">Rejected</span>
                                            <?php elseif ($emp['total_regular_hours'] == 0 && $emp['attendance_summary_status'] == 'approved'): ?>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">0 hrs approved</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Overtime Hours Column -->
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-semibold text-gray-900 whitespace-nowrap"><?= round($emp['total_overtime_hours']) ?>
                                                hrs</span>
                                            <?php if ($emp['total_overtime_hours'] == 0 && $emp['attendance_summary_status'] == 'approved'): ?>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">No overtime</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Claims Column -->
                                    <td class="py-4 px-6">
                                        <?php if ($emp['claims_count'] > 0): ?>
                                            <div class="flex flex-col min-w-25">
                                                <span
                                                    class="text-sm font-bold text-amber-600 whitespace-nowrap"><?= formatPayrollCurrency($emp['claims_amount']) ?></span>
                                                <span class="text-xs text-gray-400 whitespace-nowrap"><?= $emp['claims_count'] ?>
                                                    claim<?= $emp['claims_count'] > 1 ? 's' : '' ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-300">—</span>
                                                <span class="text-xs text-gray-300 whitespace-nowrap">No claims</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Gross Pay Column -->
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-900 whitespace-nowrap"><?= formatPayrollCurrency($emp['gross_pay']) ?></span>
                                            <?php if ($emp['gross_pay'] == 0): ?>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">No earnings</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Deductions Column -->
                                    <td class="py-4 px-6">
                                        <span class="text-sm font-medium text-red-600 whitespace-nowrap">-
                                            <?= formatPayrollCurrency($emp['total_deductions']) ?></span>
                                    </td>

                                    <!-- Net Pay Column -->
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col min-w-30">
                                            <span
                                                class="text-sm font-bold text-green-600 whitespace-nowrap"><?= formatPayrollCurrency($emp['net_pay']) ?></span>
                                            <?php if ($emp['claims_amount'] > 0): ?>
                                                <span class="text-xs text-amber-600 whitespace-nowrap">(inc.
                                                    <?= formatPayrollCurrency($emp['claims_amount']) ?> claims)</span>
                                            <?php elseif ($emp['net_pay'] == 0): ?>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">No net pay</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Status Column with Detailed Indicators -->
                                    <td class="py-4 px-6">
                                        <?php
                                        $statusBadgeClass = '';
                                        $statusIcon = '';

                                        if ($emp['status'] == 'Processed') {
                                            $statusBadgeClass = 'bg-green-100 text-green-700 border-green-200';
                                            $statusIcon = 'fa-check-circle';
                                        } elseif ($emp['status'] == 'Processing') {
                                            $statusBadgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                            $statusIcon = 'fa-clock';
                                        } elseif ($emp['status'] == 'Pending') {
                                            $statusBadgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                            $statusIcon = 'fa-hourglass-half';
                                        } else {
                                            $statusBadgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                            $statusIcon = 'fa-circle';
                                        }
                                        ?>
                                        <div class="flex flex-col gap-1.5">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold border shadow-sm whitespace-nowrap <?= $statusBadgeClass ?>">
                                                <i class="fas <?= $statusIcon ?> mr-1.5"></i>
                                                <?= $emp['status'] ?>
                                            </span>

                                            <!-- Additional Status Indicators -->
                                            <?php if ($emp['status'] == 'No Data'): ?>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">No attendance or claims</span>
                                            <?php elseif ($emp['status'] == 'Pending'): ?>
                                                <?php if ($emp['attendance_summary_status'] == 'pending'): ?>
                                                    <span
                                                        class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full w-fit whitespace-nowrap">Awaiting
                                                        approval</span>
                                                <?php elseif ($emp['attendance_summary_status'] == 'none' && $emp['claims_count'] > 0): ?>
                                                    <span
                                                        class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full w-fit whitespace-nowrap">Claims
                                                        only</span>
                                                <?php elseif ($emp['attendance_summary_status'] == 'approved'): ?>
                                                    <span
                                                        class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full w-fit whitespace-nowrap">Attendance
                                                        approved</span>
                                                <?php endif; ?>
                                            <?php elseif ($emp['status'] == 'Processed'): ?>
                                                <span
                                                    class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full w-fit whitespace-nowrap">Payroll
                                                    completed</span>
                                            <?php elseif ($emp['status'] == 'Processing'): ?>
                                                <span
                                                    class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full w-fit whitespace-nowrap">Being
                                                    processed</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Actions Column -->
                                    <td class="py-4 pl-6 pr-4">
                                        <div class="flex items-center gap-2 min-w-40">
                                            <!-- Review Button - Always enabled to view details -->
                                            <button onclick="openModal('payrollReviewModal<?= $emp['id'] ?>')"
                                                class="text-sm text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-50 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 border border-gray-200 shadow-sm whitespace-nowrap group-hover:border-gray-300">
                                                <i class="fas fa-eye text-xs"></i>
                                                <span>Review</span>
                                            </button>

                                            <?php
                                            $hasAttendanceData = ($emp['total_regular_hours'] > 0 || $emp['total_overtime_hours'] > 0);
                                            $hasClaims = ($emp['claims_amount'] > 0);
                                            $hasAnyData = $hasAttendanceData || $hasClaims;
                                            $hasAttendanceSummary = ($emp['attendance_summary_status'] != 'none');
                                            ?>

                                            <?php if ($emp['status'] == 'Processing'): ?>
                                                <!-- Update button - Only show for Processing status -->
                                                <form action="/payroll-summary" method="POST" class="inline-block">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="employeeId" value="<?= $emp['id'] ?>">
                                                    <input type="hidden" name="regularHours"
                                                        value="<?= round($emp['total_regular_hours']) ?>">
                                                    <input type="hidden" name="overtime"
                                                        value="<?= round($emp['total_overtime_hours']) ?>">
                                                    <input type="hidden" name="claims" value="<?= $emp['claims_amount'] ?>">
                                                    <input type="hidden" name="grossPay" value="<?= $emp['gross_pay'] ?>">
                                                    <input type="hidden" name="deduction" value="<?= $emp['total_deductions'] ?>">
                                                    <input type="hidden" name="netPay" value="<?= $emp['net_pay'] ?>">

                                                    <?php if (!$hasAttendanceSummary && !$hasClaims): ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-gray-400 bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                                            title="Cannot update: No attendance summary or claims data">
                                                            <i class="fas fa-ban text-xs"></i>
                                                            <span>No Data</span>
                                                        </button>
                                                    <?php elseif (!$hasAttendanceSummary && $hasClaims): ?>
                                                        <button type="submit"
                                                            class="text-sm text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 shadow-sm border border-blue-200 whitespace-nowrap"
                                                            title="Update payroll (claims only, no attendance summary)">
                                                            <i class="fas fa-sync-alt text-xs"></i>
                                                            <span>Update</span>
                                                        </button>
                                                    <?php elseif ($emp['attendance_summary_status'] == 'pending'): ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-yellow-600 bg-yellow-50 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-yellow-200 cursor-not-allowed whitespace-nowrap"
                                                            title="Cannot process: Attendance still pending approval">
                                                            <i class="fas fa-hourglass-half text-xs"></i>
                                                            <span>Pending</span>
                                                        </button>
                                                    <?php elseif ($emp['attendance_summary_status'] == 'rejected'): ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-red-200 cursor-not-allowed whitespace-nowrap"
                                                            title="Cannot process: Attendance was rejected">
                                                            <i class="fas fa-times-circle text-xs"></i>
                                                            <span>Rejected</span>
                                                        </button>
                                                    <?php elseif ($hasAttendanceData || $hasClaims): ?>
                                                        <button type="submit"
                                                            class="text-sm text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 shadow-sm border border-blue-200 whitespace-nowrap">
                                                            <i class="fas fa-sync-alt text-xs"></i>
                                                            <span>Update</span>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-gray-400 bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                                            title="No data to update">
                                                            <i class="fas fa-ban text-xs"></i>
                                                            <span>No Data</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </form>

                                            <?php elseif ($emp['status'] == 'Pending'): ?>
                                                <!-- Process button for new payroll -->
                                                <form action="/payroll-summary" method="POST" class="inline-block">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="employeeId" value="<?= $emp['id'] ?>">
                                                    <input type="hidden" name="regularHours"
                                                        value="<?= round($emp['total_regular_hours']) ?>">
                                                    <input type="hidden" name="overtime"
                                                        value="<?= round($emp['total_overtime_hours']) ?>">
                                                    <input type="hidden" name="claims" value="<?= $emp['claims_amount'] ?>">
                                                    <input type="hidden" name="grossPay" value="<?= $emp['gross_pay'] ?>">
                                                    <input type="hidden" name="deduction" value="<?= $emp['total_deductions'] ?>">
                                                    <input type="hidden" name="netPay" value="<?= $emp['net_pay'] ?>">

                                                    <?php if (!$hasAttendanceSummary && !$hasClaims): ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-gray-400 bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                                            title="Cannot process: No attendance summary or claims data">
                                                            <i class="fas fa-ban text-xs"></i>
                                                            <span>No Data</span>
                                                        </button>
                                                    <?php elseif (!$hasAttendanceSummary && $hasClaims): ?>
                                                        <button type="submit"
                                                            class="text-sm text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 shadow-sm border border-green-200 whitespace-nowrap"
                                                            title="Process payroll (claims only, no attendance summary)">
                                                            <i class="fas fa-check text-xs"></i>
                                                            <span>Process</span>
                                                        </button>
                                                    <?php elseif ($emp['attendance_summary_status'] == 'pending'): ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-yellow-600 bg-yellow-50 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-yellow-200 cursor-not-allowed whitespace-nowrap"
                                                            title="Cannot process: Attendance still pending approval">
                                                            <i class="fas fa-hourglass-half text-xs"></i>
                                                            <span>Pending</span>
                                                        </button>
                                                    <?php elseif ($emp['attendance_summary_status'] == 'rejected'): ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-red-200 cursor-not-allowed whitespace-nowrap"
                                                            title="Cannot process: Attendance was rejected">
                                                            <i class="fas fa-times-circle text-xs"></i>
                                                            <span>Rejected</span>
                                                        </button>
                                                    <?php elseif ($hasAttendanceData || $hasClaims): ?>
                                                        <button type="submit"
                                                            class="text-sm text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 shadow-sm border border-green-200 whitespace-nowrap">
                                                            <i class="fas fa-check text-xs"></i>
                                                            <span>Process</span>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" disabled
                                                            class="text-sm text-gray-400 bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                                            title="No data to process">
                                                            <i class="fas fa-ban text-xs"></i>
                                                            <span>No Data</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </form>

                                            <?php elseif ($emp['status'] == 'Processed'): ?>
                                                <!-- Show disabled button for Processed status -->
                                                <button type="button" disabled
                                                    class="text-sm text-gray-400 bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                                    title="Payroll already processed - cannot be updated">
                                                    <i class="fas fa-lock text-xs"></i>
                                                    <span>Processed</span>
                                                </button>

                                            <?php else: ?>
                                                <!-- Disabled button for No Data status -->
                                                <button disabled
                                                    class="text-sm text-gray-400 bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                                    title="No attendance or claims data available">
                                                    <i class="fas fa-ban text-xs"></i>
                                                    <span>No Data</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calculator text-5xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-semibold text-gray-700">No payroll data found</p>
                                        <p class="text-sm text-gray-400 mt-1">Click "Process Payroll" to generate payroll
                                            for this period</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payroll Summary Footer -->
            <div class="mt-8 pt-6 border-t-2 border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Total Regular Hours</p>
                        <p class="text-xl font-bold text-gray-900"><?= round($payrollPageRegularHours) ?> hrs</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Total Overtime Hours</p>
                        <p class="text-xl font-bold text-gray-900"><?= round($payrollPageOvertimeHours) ?> hrs</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Total Claims</p>
                        <p class="text-xl font-bold text-amber-600">
                            <?= formatPayrollCurrency($payrollPageClaimsTotal) ?>
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Average Net Pay</p>
                        <p class="text-xl font-bold text-gray-900"><?= formatPayrollCurrency($payrollPageAverageNet) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($payrollTotalPages > 1): ?>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-lg">
                        Showing <span
                            class="font-semibold text-gray-900"><?= min(1 + ($payrollPage - 1) * $payrollPerPage, $payrollTotalFiltered) ?>-<?= min($payrollPage * $payrollPerPage, $payrollTotalFiltered) ?></span>
                        of <span class="font-semibold text-gray-900"><?= $payrollTotalFiltered ?></span> employees
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($payrollPage > 1): ?>
                            <a href="?tab=payroll&payroll_page=<?= $payrollPage - 1 ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white border border-gray-200 text-gray-300 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= min(5, $payrollTotalPages); $i++): ?>
                            <a href="?tab=payroll&payroll_page=<?= $i ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                class="w-9 h-9 flex items-center justify-center text-sm rounded-xl transition-all duration-200 font-medium <?= $i == $payrollPage ? 'bg-linear-to-r from-gray-800 to-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($payrollPage < $payrollTotalPages): ?>
                            <a href="?tab=payroll&payroll_page=<?= $payrollPage + 1 ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white border border-gray-200 text-gray-300 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payroll History Section -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mt-8">
        <div
            class="px-6 py-5 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-linear-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Payroll History</h3>
                    <p class="text-xs text-gray-400 mt-0.5"><?= $payrollHistoryTotal ?> completed payroll periods</p>
                </div>
            </div>
            <button onclick="exportCurrentPage()"
                class="text-sm text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-4 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 border border-green-200 shadow-sm font-medium">
                <i class="fas fa-file-excel"></i>
                Export Current Period
            </button>
        </div>

        <div class="p-6">
            <?php if (!empty($payrollHistory)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php foreach ($payrollHistory as $history):
                        $periodLabel = date('M j', strtotime($history['period_start'])) . ' - ' . date('M j, Y', strtotime($history['period_end']));
                        $isCurrentPeriod = ($history['period_start'] == $payrollPeriodStart && $history['period_end'] == $payrollPeriodEnd);
                        $statusColor = 'gray';
                        $statusIcon = 'fa-circle';

                        if (strpos($history['statuses'], 'Processed') !== false) {
                            $statusColor = 'green';
                            $statusIcon = 'fa-check-circle';
                        } elseif (strpos($history['statuses'], 'Processing') !== false) {
                            $statusColor = 'blue';
                            $statusIcon = 'fa-clock';
                        }
                        ?>
                        <div
                            class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-200 <?= $isCurrentPeriod ? 'bg-blue-50/30 border-blue-300 ring-1 ring-blue-200' : 'bg-white' ?>">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= $periodLabel ?></p>
                                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                        <i class="far fa-calendar-alt"></i>
                                        <?= date('M j, Y', strtotime($history['last_generated'])) ?>
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-700 border border-<?= $statusColor ?>-200">
                                    <i class="fas <?= $statusIcon ?> mr-1.5"></i>
                                    <?= $history['employee_count'] ?> employees
                                </span>
                            </div>

                            <div class="grid grid-cols-4 md:grid-cols-2 gap-3 mb-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400">Gross Pay</p>
                                    <p class="text-sm font-bold text-gray-900 mt-1">
                                        <?= formatPayrollCurrency($history['total_gross']) ?>
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400">Net Pay</p>
                                    <p class="text-sm font-bold text-green-600 mt-1">
                                        <?= formatPayrollCurrency($history['total_net']) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-around pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-invoice-dollar text-sm text-gray-400"></i>
                                    <span class="text-xs font-medium text-gray-500">Claims:
                                        <?= formatPayrollCurrency($history['total_claims']) ?></span>
                                </div>
                                <button
                                    onclick="exportPayrollPeriod('<?= $history['period_start'] ?>', '<?= $history['period_end'] ?>')"
                                    class="text-xs text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-4 py-2 rounded-lg flex items-center gap-1.5 shadow-sm shadow-blue-200 font-medium transition-all duration-200">
                                    <i class="fas fa-download"></i>
                                    Export
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- View All Link - Now opens modal -->
                <div class="mt-6 text-center">
                    <button onclick="openModal('allPayrollHistoryModal')"
                        class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-5 py-2.5 rounded-xl inline-flex items-center gap-2 transition-all duration-200 border border-gray-200 font-medium">
                        View All History
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No payroll history yet</p>
                    <p class="text-xs text-gray-400 mt-1">Process payroll to see history here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View All Payroll History Modal -->
<div id="allPayrollHistoryModal"
    class="modal fixed inset-0 bg-gray-900/50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Complete Payroll History</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <?= $payrollHistoryTotal ?> completed payroll periods
                    </p>
                </div>
            </div>
            <button onclick="closeModal('allPayrollHistoryModal')"
                class="text-gray-400 hover:text-gray-600 transition-colors w-3 h-3 flex items-center justify-center rounded-lg hover:bg-gray-100 absolute right-5 top-5 md:inline">
                <i class="fas fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <?php if (!empty($allPayrollHistory)): ?>
                <!-- Export All Button -->
                <div class="mb-6 flex justify-end">
                    <button onclick="exportAllHistory()"
                        class="text-sm text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-4 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 border border-green-200 shadow-sm font-medium">
                        <i class="fas fa-file-excel"></i>
                        Export All History
                    </button>
                </div>

                <!-- History Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-100">
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Period</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Processed
                                    Date</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Employees
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Gross Pay
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Claims</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Net
                                    Pay
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allPayrollHistory as $history):
                                $periodLabel = date('M j', strtotime($history['period_start'])) . ' - ' . date('M j, Y', strtotime($history['period_end']));
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition-colors duration-150">
                                    <td class="py-4 px-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= $periodLabel ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="text-sm text-gray-600">
                                            <?= date('M j, Y', strtotime($history['last_generated'])) ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                                            <?= $history['employee_count'] ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= formatPayrollCurrency($history['total_gross']) ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="text-sm font-medium text-amber-600">
                                            <?= formatPayrollCurrency($history['total_claims']) ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="text-sm font-medium text-green-600">
                                            <?= formatPayrollCurrency($history['total_net']) ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-4">
                                        <button
                                            onclick="exportPayrollPeriod('<?= $history['period_start'] ?>', '<?= $history['period_end'] ?>')"
                                            class="text-xs text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm shadow-blue-200 font-medium transition-all duration-200">
                                            <i class="fas fa-download"></i>
                                            Export
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Footer -->
                <div class="mt-6 pt-4 border-t-2 border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Total Periods</p>
                            <p class="text-lg font-bold text-gray-900">
                                <?= $payrollHistoryTotal ?>
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Total Gross Pay</p>
                            <p class="text-lg font-bold text-gray-900">
                                <?= formatPayrollCurrency(array_sum(array_column($allPayrollHistory, 'total_gross'))) ?>
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Total Claims</p>
                            <p class="text-lg font-bold text-amber-600">
                                <?= formatPayrollCurrency(array_sum(array_column($allPayrollHistory, 'total_claims'))) ?>
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-400">Total Net Pay</p>
                            <p class="text-lg font-bold text-green-600">
                                <?= formatPayrollCurrency(array_sum(array_column($allPayrollHistory, 'total_net'))) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No payroll history yet</p>
                    <p class="text-xs text-gray-400 mt-1">Process payroll to see history here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Payroll Review Modals -->
<?php if (!empty($payrollEmployees)): ?>
    <?php foreach ($payrollEmployees as $emp): ?>
        <div id="payrollReviewModal<?= $emp['id'] ?>"
            class="modal fixed inset-0 bg-gray-900/50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-linear-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-invoice text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Payroll Review - <?= htmlspecialchars($emp['full_name']) ?>
                        </h3>
                    </div>
                    <button onclick="closeModal('payrollReviewModal<?= $emp['id'] ?>')"
                        class="text-gray-400 hover:text-gray-600 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Employee Info Card -->
                    <div class="bg-linear-to-r from-gray-50 to-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 bg-linear-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-lg shadow-blue-200">
                                <span class="text-lg font-bold text-white"><?= $emp['initials'] ?></span>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($emp['full_name']) ?></h4>
                                <p class="text-sm text-gray-500 mt-0.5"><?= htmlspecialchars($emp['position']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div
                            class="bg-linear-to-br from-blue-50 to-blue-100 rounded-xl p-5 text-center border border-blue-200 shadow-sm">
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-2">Regular Hours</p>
                            <p class="text-3xl font-bold text-blue-700"><?= round($emp['total_regular_hours']) ?></p>
                        </div>
                        <div
                            class="bg-linear-to-br from-amber-50 to-amber-100 rounded-xl p-5 text-center border border-amber-200 shadow-sm">
                            <p class="text-xs font-semibold text-amber-600 uppercase mb-2">Overtime Hours</p>
                            <p class="text-3xl font-bold text-amber-700"><?= round($emp['total_overtime_hours']) ?></p>
                        </div>
                        <div
                            class="bg-linear-to-br from-green-50 to-green-100 rounded-xl p-5 text-center border border-green-200 shadow-sm">
                            <p class="text-xs font-semibold text-green-600 uppercase mb-2">Net Pay</p>
                            <p class="text-3xl font-bold text-green-700"><?= formatPayrollCurrency($emp['net_pay']) ?></p>
                        </div>
                    </div>

                    <!-- Payroll Details Table -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-calculator text-gray-400"></i>
                            Payroll Breakdown
                        </h4>
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-5 text-sm text-gray-600">Gross Pay</td>
                                        <td class="py-3 px-5 text-sm font-bold text-gray-900 text-right">
                                            <?= formatPayrollCurrency($emp['gross_pay']) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-5 text-sm text-gray-600">Claim/s</td>
                                        <td class="py-3 px-5 text-sm font-bold text-amber-600 text-right">
                                            <?= formatPayrollCurrency($emp['claims_amount']) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-5 text-sm text-gray-600">Deductions</td>
                                        <td class="py-3 px-5 text-sm font-bold text-red-600 text-right">-
                                            <?= formatPayrollCurrency($emp['total_deductions']) ?>
                                        </td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="py-4 px-5 text-sm font-bold text-gray-900">Net Pay</td>
                                        <td class="py-4 px-5 text-sm font-bold text-green-600 text-right">
                                            <?= formatPayrollCurrency($emp['net_pay']) ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Deductions Breakdown -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-minus-circle text-gray-400"></i>
                            Deductions Breakdown
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <p class="text-xs text-gray-400 mb-1">SSS</p>
                                <p class="text-sm font-bold text-gray-900">
                                    <?= formatPayrollCurrency($emp['sss_deduction'] ?? $emp['total_deductions'] * 0.2) ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <p class="text-xs text-gray-400 mb-1">PhilHealth</p>
                                <p class="text-sm font-bold text-gray-900">
                                    <?= formatPayrollCurrency($emp['philhealth_deduction'] ?? $emp['total_deductions'] * 0.15) ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <p class="text-xs text-gray-400 mb-1">Pag-IBIG</p>
                                <p class="text-sm font-bold text-gray-900">
                                    <?= formatPayrollCurrency($emp['pagibig_deduction'] ?? $emp['total_deductions'] * 0.15) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end gap-3 pt-5 border-t-2 border-gray-100">
                        <button type="button" onclick="closeModal('payrollReviewModal<?= $emp['id'] ?>')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">
                            Close
                        </button>
                        <button type="button" onclick="printPayslip(<?= $emp['id'] ?>)"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-lg shadow-blue-200 flex items-center gap-2 transition-all duration-200">
                            <i class="fas fa-print"></i>
                            Print Payslip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function printPayslip(employeeId) {
        window.print();
    }

    function exportPayrollPeriod(periodStart, periodEnd) {
        window.location.href = '?tab=payroll&export_payroll=1&period_start=' + periodStart + '&period_end=' + periodEnd;
    }

    function exportCurrentPage() {
        const periodStart = '<?= $payrollPeriodStart ?>';
        const periodEnd = '<?= $payrollPeriodEnd ?>';
        exportPayrollPeriod(periodStart, periodEnd);
    }

    function exportAllHistory() {
        window.location.href = '?tab=payroll&export_all_history=1';
    }

    function applyPayrollFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'payroll');
        url.searchParams.set('payroll_page', '1');

        const status = document.querySelector('select[name="payroll_status"]')?.value;
        const dept = document.querySelector('select[name="payroll_department"]')?.value;

        if (status) url.searchParams.set('payroll_status', status);
        else url.searchParams.delete('payroll_status');

        if (dept) url.searchParams.set('payroll_department', dept);
        else url.searchParams.delete('payroll_department');

        window.location.href = url.toString();
    }
</script>