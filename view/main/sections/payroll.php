<!-- payroll.php -->
<div class="tab-content" id="payroll-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payroll Management</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Process payroll and manage employee compensation
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Status Summary Badges - Simplified -->
            <div
                class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700">
                <span class="inline-flex items-center gap-1.5 text-xs">
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400 font-medium">Approved:</span>
                    <span class="text-gray-900 dark:text-white font-bold"><?= $payrollApprovedCount ?? 0 ?></span>
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span class="inline-flex items-center gap-1.5 text-xs">
                    <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400 font-medium">Pending:</span>
                    <span class="text-gray-900 dark:text-white font-bold"><?= $payrollPendingCount ?? 0 ?></span>
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span class="inline-flex items-center gap-1.5 text-xs">
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400 font-medium">Total:</span>
                    <span class="text-gray-900 dark:text-white font-bold"><?= $payrollTotalEmployees ?? 0 ?></span>
                </span>
            </div>

            <!-- Process All Button -->
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
                ?>

                <button type="submit" <?= $buttonDisabled ? 'disabled' : '' ?>
                    class="<?= $buttonDisabled ? 'bg-gray-200 dark:bg-gray-700 cursor-not-allowed opacity-60' : 'bg-primary hover:bg-primary-hover text-white' ?> px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 whitespace-nowrap font-medium text-white"
                    onclick="return <?= $hasReadyData ? 'confirm(\'Process payroll for ' . $payrollReadyForProcessing . ' employees with approved data? This may take a moment.\')' : 'false' ?>">

                    <i class="fas fa-play-circle text-sm "></i>
                    Process All

                    <?php if ($payrollReadyForProcessing > 0): ?>
                            <span class="ml-1 px-1.5 py-0.5 bg-white/20 rounded-full text-xs font-semibold">
                                <?= $payrollReadyForProcessing ?>
                            </span>
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Payroll Period Info -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-gray-600 dark:text-gray-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current
                        Payroll Period</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5"><?= $payrollPeriodLabel ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/30 px-4 py-2 rounded-xl">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Payroll Date:</span>
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                    <i class="far fa-calendar-check mr-1.5 text-gray-400 dark:text-gray-500"></i>
                    <?= date('F j, Y', strtotime($payrollPayDate)) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Bar - Simplified -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Filter by:</span>
                <div class="relative">
                    <select name="payroll_status" onchange="applyPayrollFilter()"
                        class="text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none text-gray-900 dark:text-white">
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
                        class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 text-xs pointer-events-none"></i>
                </div>

                <div class="relative">
                    <select name="payroll_department" onchange="applyPayrollFilter()"
                        class="text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none text-gray-900 dark:text-white">
                        <option value="">All Departments</option>
                        <?php foreach ($payrollDepartments as $dept): ?>
                                <option value="<?= htmlspecialchars($dept['department']) ?>"
                                    <?= $payrollDepartmentFilter == $dept['department'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['department']) ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                    <i
                        class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 text-xs pointer-events-none"></i>
                </div>
            </div>

            <?php if (!empty($payrollStatusFilter) || !empty($payrollDepartmentFilter)): ?>
                    <a href="?tab=payroll&payroll_page=1"
                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors duration-200">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payroll Summary Stats - Simplified colors -->
    <div class="grid grid-cols-4 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Gross
                    Pay</p>
                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-gray-600 dark:text-gray-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= formatPayrollCurrency($payrollTotalGross) ?>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Before deductions</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Net Pay
                </p>
                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-gray-600 dark:text-gray-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">

                <?= formatPayrollCurrency($payrollTotalNet) ?>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Take-home pay</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Claims
                </p>
                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-gray-600 dark:text-gray-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                <?= formatPayrollCurrency($payrollTotalClaims) ?>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Additional compensation</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employees</p>
                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-gray-600 dark:text-gray-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $payrollTotalEmployees ?></p>
            <div class="flex items-center gap-3 mt-1">
                <span
                    class="text-xs bg-green-100 dark:bg-green-900/30 text-white dark:text-green-600 px-2 py-0.5 rounded-full font-medium"><?= $payrollProcessedCount ?>
                    processed</span>
                <span
                    class="text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-600 px-2 py-0.5 rounded-full font-medium"><?= $payrollPendingCount ?>
                    pending</span>
            </div>
        </div>
    </div>

    <!-- Payroll List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div
            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Payroll Summary</h3>
            <div class="flex items-center gap-4">
                <span
                    class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <i class="far fa-user mr-1.5 text-gray-400 dark:text-gray-500"></i>
                    <?= $payrollTotalEmployees ?> employees
                </span>
                <span
                    class="inline-flex items-center gap-1.5 text-xs bg-green-100 dark:bg-green-900/30 px-3 py-1.5 rounded-lg">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">Processed:
                        <?= $payrollProcessedCount ?></span>
                </span>
                <span
                    class="inline-flex items-center gap-1.5 text-xs bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1.5 rounded-lg">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">Pending:
                        <?= $payrollPendingCount ?></span>
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                            <th
                                class="text-left py-4 pl-4 pr-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Employee</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Regular Hours</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Overtime</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Claims</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Gross Pay</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Deductions</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Net Pay</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th
                                class="text-left py-4 pl-6 pr-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payrollEmployees)): ?>
                                <?php foreach ($payrollEmployees as $emp): ?>
                                        <tr
                                            class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150 group">
                                            <td class="py-4 pl-4 pr-6">
                                                <div class="flex items-center gap-3 min-w-45">
                                                    <div
                                                        class="w-10 h-10 bg-white shadow-md dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-700 dark:text-gray-300 text-sm font-bold shrink-0">
                                                        <?= $emp['initials'] ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                            <?= htmlspecialchars($emp['full_name']) ?>
                                                        </p>
                                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                                            <?= htmlspecialchars($emp['position']) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap"><?= round($emp['total_regular_hours']) ?>
                                                        hrs</span>
                                                    <?php if ($emp['total_regular_hours'] == 0 && $emp['attendance_summary_status'] == 'none'): ?>
                                                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">No
                                                                attendance</span>
                                                    <?php elseif ($emp['attendance_summary_status'] == 'pending'): ?>
                                                            <span
                                                                class="text-xs text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 px-2 py-0.5 rounded-full inline-block w-fit mt-1 whitespace-nowrap">Pending
                                                                approval</span>
                                                    <?php elseif ($emp['attendance_summary_status'] == 'rejected'): ?>
                                                            <span
                                                                class="text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full inline-block w-fit mt-1 whitespace-nowrap">Rejected</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap"><?= round($emp['total_overtime_hours']) ?>
                                                        hrs</span>
                                                    <?php if ($emp['total_overtime_hours'] == 0 && $emp['attendance_summary_status'] == 'approved'): ?>
                                                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">No
                                                                overtime</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6">
                                                <?php if ($emp['claims_count'] > 0): ?>
                                                        <div class="flex flex-col min-w-25">
                                                            <span
                                                                class="text-sm font-bold text-amber-600 dark:text-amber-400 whitespace-nowrap"><?= formatPayrollCurrency($emp['claims_amount']) ?></span>
                                                            <span
                                                                class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap"><?= $emp['claims_count'] ?>
                                                                claim<?= $emp['claims_count'] > 1 ? 's' : '' ?></span>
                                                        </div>
                                                <?php else: ?>
                                                        <div class="flex flex-col">
                                                            <span class="text-sm text-gray-300 dark:text-gray-600">—</span>
                                                            <span class="text-xs text-gray-300 dark:text-gray-600 whitespace-nowrap">No
                                                                claims</span>
                                                        </div>
                                                <?php endif; ?>
                                            </td>

                                            <td class="py-4 px-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-bold text-gray-900 dark:text-white whitespace-nowrap"><?= formatPayrollCurrency($emp['gross_pay']) ?></span>
                                                    <?php if ($emp['gross_pay'] == 0): ?>
                                                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">No
                                                                earnings</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6">
                                                <span class="text-sm font-medium text-red-600 dark:text-red-400 whitespace-nowrap">-
                                                    <?= formatPayrollCurrency($emp['total_deductions']) ?></span>
                                            </td>

                                            <td class="py-4 px-6">
                                                <div class="flex flex-col min-w-30">
                                                    <span
                                                        class="text-sm font-bold text-green-600 dark:text-green-400 whitespace-nowrap"><?= formatPayrollCurrency($emp['net_pay']) ?></span>
                                                    <?php if ($emp['claims_amount'] > 0): ?>
                                                            <span class="text-xs text-amber-600 dark:text-amber-400 whitespace-nowrap">(inc.
                                                                <?= formatPayrollCurrency($emp['claims_amount']) ?> claims)</span>
                                                    <?php elseif ($emp['net_pay'] == 0): ?>
                                                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">No net
                                                                pay</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="py-4 px-6">
                                                <?php
                                                $statusColors = [
                                                    'Processed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800',
                                                    'Processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                                    'Pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                                                    'Rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800',
                                                    'No Data' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-600'
                                                ];
                                                $statusClass = $statusColors[$emp['status']] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-600';
                                                ?>
                                                <div class="flex flex-col gap-1.5">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold border whitespace-nowrap <?= $statusClass ?>">
                                                        <?= $emp['status'] ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="py-4 pl-6 pr-4">
                                                <div class="flex items-center gap-2 min-w-40">
                                                    <!-- Review Button -->
                                                    <button onclick="openModal('payrollReviewModal<?= $emp['id'] ?>')"
                                                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 border border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                                        <i class="fas fa-eye text-xs"></i>
                                                        <span>Review</span>
                                                    </button>

                                                    <?php
                                                    $hasAttendanceData = ($emp['total_regular_hours'] > 0 || $emp['total_overtime_hours'] > 0);
                                                    $hasClaims = ($emp['claims_amount'] > 0);
                                                    $hasAnyData = $hasAttendanceData || $hasClaims;
                                                    $hasAttendanceSummary = ($emp['attendance_summary_status'] != 'none');
                                                    ?>

                                                    <?php if ($emp['status'] == 'Processing' || $emp['status'] == 'Pending'): ?>
                                                            <!-- Process/Update button -->
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
                                                                            class="text-sm text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 dark:border-gray-600 cursor-not-allowed whitespace-nowrap"
                                                                            title="Cannot process: No attendance summary or claims data">
                                                                            <i class="fas fa-ban text-xs"></i>
                                                                            <span>No Data</span>
                                                                        </button>
                                                                <?php elseif ($emp['attendance_summary_status'] == 'pending'): ?>
                                                                        <button type="button" disabled
                                                                            class="text-sm text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-yellow-200 dark:border-yellow-800 cursor-not-allowed whitespace-nowrap"
                                                                            title="Cannot process: Attendance still pending approval">
                                                                            <i class="fas fa-hourglass-half text-xs"></i>
                                                                            <span>Pending</span>
                                                                        </button>
                                                                <?php elseif ($emp['attendance_summary_status'] == 'rejected'): ?>
                                                                        <button type="button" disabled
                                                                            class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-red-200 dark:border-red-800 cursor-not-allowed whitespace-nowrap"
                                                                            title="Cannot process: Attendance was rejected">
                                                                            <i class="fas fa-times-circle text-xs"></i>
                                                                            <span>Rejected</span>
                                                                        </button>
                                                                <?php elseif ($hasAttendanceData || $hasClaims): ?>
                                                                        <button type="submit"
                                                                            class="text-sm text-primary bg-primary/10 hover:bg-primary/20 px-3 py-2 rounded-lg transition-all duration-200 flex items-center gap-1.5 border border-primary/20 whitespace-nowrap">
                                                                            <i class="fas fa-sync-alt text-xs"></i>
                                                                            <span><?= $emp['status'] == 'Processing' ? 'Update' : 'Process' ?></span>
                                                                        </button>
                                                                <?php else: ?>
                                                                        <button type="button" disabled
                                                                            class="text-sm text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 dark:border-gray-600 cursor-not-allowed whitespace-nowrap"
                                                                            title="No data to process">
                                                                            <i class="fas fa-ban text-xs"></i>
                                                                            <span>No Data</span>
                                                                        </button>
                                                                <?php endif; ?>
                                                            </form>
                                                    <?php elseif ($emp['status'] == 'Processed'): ?>
                                                            <button type="button" disabled
                                                                class="text-sm text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 dark:border-gray-600 cursor-not-allowed whitespace-nowrap"
                                                                title="Payroll already processed">
                                                                <i class="fas fa-lock text-xs"></i>
                                                                <span>Processed</span>
                                                            </button>
                                                    <?php else: ?>
                                                            <button disabled
                                                                class="text-sm text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg flex items-center gap-1.5 border border-gray-200 dark:border-gray-600 cursor-not-allowed whitespace-nowrap"
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
                                    <td colspan="9" class="py-16 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-calculator text-5xl mb-4 text-gray-300 dark:text-gray-600"></i>
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">No payroll data
                                                found</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Click "Process Payroll" to
                                                generate payroll for this period</p>
                                        </div>
                                    </td>
                                </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payroll Summary Footer -->
            <div class="mt-8 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Total Regular
                            Hours</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            <?= round($payrollPageRegularHours) ?> hrs
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Total Overtime
                            Hours</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            <?= round($payrollPageOvertimeHours) ?> hrs
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Total Claims
                        </p>
                        <p class="text-xl font-bold text-amber-600 dark:text-amber-400">
                            <?= formatPayrollCurrency($payrollPageClaimsTotal) ?>
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Average Net Pay
                        </p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            <?= formatPayrollCurrency($payrollPageAverageNet) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($payrollTotalPages > 1): ?>
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/30 px-4 py-2 rounded-lg">
                            Showing <span
                                class="font-semibold text-gray-900 dark:text-white"><?= min(1 + ($payrollPage - 1) * $payrollPerPage, $payrollTotalFiltered) ?>-<?= min($payrollPage * $payrollPerPage, $payrollTotalFiltered) ?></span>
                            of <span class="font-semibold text-gray-900 dark:text-white"><?= $payrollTotalFiltered ?></span>
                            employees
                        </p>
                        <div class="flex items-center gap-2">
                            <?php if ($payrollPage > 1): ?>
                                    <a href="?tab=payroll&payroll_page=<?= $payrollPage - 1 ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                        class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </a>
                            <?php else: ?>
                                    <button
                                        class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-300 dark:text-gray-500 cursor-not-allowed"
                                        disabled>
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </button>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= min(5, $payrollTotalPages); $i++): ?>
                                    <a href="?tab=payroll&payroll_page=<?= $i ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                        class="w-9 h-9 flex items-center justify-center text-sm rounded-xl transition-all duration-200 font-medium <?= $i == $payrollPage ? 'bg-primary text-white' : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' ?>">
                                        <?= $i ?>
                                    </a>
                            <?php endfor; ?>

                            <?php if ($payrollPage < $payrollTotalPages): ?>
                                    <a href="?tab=payroll&payroll_page=<?= $payrollPage + 1 ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                        class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                            <?php else: ?>
                                    <button
                                        class="w-9 h-9 flex items-center justify-center text-sm rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-300 dark:text-gray-500 cursor-not-allowed"
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
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden mt-8">
        <div
            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap -4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white shadow-sm dark:bg-gray-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-white dark:text-gray-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Payroll History</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= $payrollHistoryTotal ?> completed
                        payroll periods</p>
                </div>
            </div>
            <button onclick="exportCurrentPage()"
                class="text-sm text-white bg-primary px-4 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2  font-medium">
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
                            ?>
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:shadow-lg transition-all duration-200 <?= $isCurrentPeriod ? 'bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800' : 'bg-white dark:bg-gray-800' ?>">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white"><?= $periodLabel ?></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="far fa-calendar-alt"></i>
                                                <?= date('M j, Y', strtotime($history['last_generated'])) ?>
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                            <?= $history['employee_count'] ?> employees
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-4 md:grid-cols-2 gap-3 mb-4">
                                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 dark:text-gray-500">Gross Pay</p>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">
                                                <?= formatPayrollCurrency($history['total_gross']) ?>
                                            </p>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 dark:text-gray-500">Net Pay</p>
                                            <p class="text-sm font-bold text-green-600 dark:text-green-400 mt-1">
                                                <?= formatPayrollCurrency($history['total_net']) ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-file-invoice-dollar text-sm text-gray-400 dark:text-gray-500"></i>
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Claims:
                                                <?= formatPayrollCurrency($history['total_claims']) ?></span>
                                        </div>
                                        <button
                                            onclick="exportPayrollPeriod('<?= $history['period_start'] ?>', '<?= $history['period_end'] ?>')"
                                            class="text-xs text-white bg-primary hover:bg-primary-hover px-4 py-2 rounded-lg flex items-center gap-1.5 font-medium transition-all duration-200">
                                            <i class="fas fa-download"></i>
                                            Export
                                        </button>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- View All Link -->
                    <div class="mt-6 text-center">
                        <button onclick="openModal('allPayrollHistoryModal')"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50 px-5 py-2.5 rounded-xl inline-flex items-center gap-2 transition-all duration-200 border border-gray-200 dark:border-gray-600 font-medium">
                            View All History
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                    </div>
            <?php else: ?>
                    <div class="text-center py-12">
                        <div
                            class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-3xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No payroll history yet</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Process payroll to see history here</p>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View All Payroll History Modal -->
<div id="allPayrollHistoryModal"
    class="modal fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center hidden z-50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div
            class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Complete Payroll History</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= $payrollHistoryTotal ?> completed
                        payroll periods</p>
                </div>
            </div>
            <button onclick="closeModal('allPayrollHistoryModal')"
                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <?php if (!empty($allPayrollHistory)): ?>
                    <!-- Export All Button -->
                    <div class="mb-6 flex justify-end">
                        <button onclick="exportAllHistory()"
                            class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 px-4 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 border border-green-200 dark:border-green-800 font-medium">
                            <i class="fas fa-file-excel"></i>
                            Export All History
                        </button>
                    </div>

                    <!-- History Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Period</th>
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Processed Date</th>
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Employees</th>
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Gross Pay</th>
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Claims</th>
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Net Pay</th>
                                    <th
                                        class="text-left py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allPayrollHistory as $history):
                                    $periodLabel = date('M j', strtotime($history['period_start'])) . ' - ' . date('M j, Y', strtotime($history['period_end']));
                                    ?>
                                        <tr
                                            class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                            <td class="py-4 px-4">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $periodLabel ?></p>
                                            </td>
                                            <td class="py-4 px-4">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    <?= date('M j, Y', strtotime($history['last_generated'])) ?>
                                                </p>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span
                                                    class="px-2.5 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold"><?= $history['employee_count'] ?></span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?= formatPayrollCurrency($history['total_gross']) ?>
                                                </p>
                                            </td>
                                            <td class="py-4 px-4">
                                                <p class="text-sm font-medium text-amber-600 dark:text-amber-400">
                                                    <?= formatPayrollCurrency($history['total_claims']) ?>
                                                </p>
                                            </td>
                                            <td class="py-4 px-4">
                                                <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                                    <?= formatPayrollCurrency($history['total_net']) ?>
                                                </p>
                                            </td>
                                            <td class="py-4 px-4">
                                                <button
                                                    onclick="exportPayrollPeriod('<?= $history['period_start'] ?>', '<?= $history['period_end'] ?>')"
                                                    class="text-xs text-white bg-primary hover:bg-primary-hover px-3 py-1.5 rounded-lg flex items-center gap-1.5 font-medium transition-all duration-200">
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
                    <div class="mt-6 pt-4 border-t-2 border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Total Periods</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white"><?= $payrollHistoryTotal ?></p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Total Gross Pay</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    <?= formatPayrollCurrency(array_sum(array_column($allPayrollHistory, 'total_gross'))) ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Total Claims</p>
                                <p class="text-lg font-bold text-amber-600 dark:text-amber-400">
                                    <?= formatPayrollCurrency(array_sum(array_column($allPayrollHistory, 'total_claims'))) ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Total Net Pay</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                    <?= formatPayrollCurrency(array_sum(array_column($allPayrollHistory, 'total_net'))) ?>
                                </p>
                            </div>
                        </div>
                    </div>
            <?php else: ?>
                    <div class="text-center py-12">
                        <div
                            class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-3xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No payroll history yet</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Process payroll to see history here</p>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Payroll Review Modals -->
<?php if (!empty($payrollEmployees)): ?>
        <?php foreach ($payrollEmployees as $emp): ?>
                <div id="payrollReviewModal<?= $emp['id'] ?>"
                    class="modal fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div
                            class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-gray-600 dark:text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Payroll Review -
                                    <?= htmlspecialchars($emp['full_name']) ?>
                                </h3>
                            </div>
                            <button onclick="closeModal('payrollReviewModal<?= $emp['id'] ?>')"
                                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6">
                            <!-- Employee Info Card -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-primary rounded-full flex items-center justify-center">
                                        <span class="text-lg font-bold text-white"><?= $emp['initials'] ?></span>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($emp['full_name']) ?>
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                            <?= htmlspecialchars($emp['position']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payroll Summary Cards - Simplified -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div
                                    class="bg-gray-100 dark:bg-gray-700 rounded-xl p-5 text-center border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Regular Hours</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                        <?= round($emp['total_regular_hours']) ?>
                                    </p>
                                </div>
                                <div
                                    class="bg-gray-100 dark:bg-gray-700 rounded-xl p-5 text-center border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Overtime Hours</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                        <?= round($emp['total_overtime_hours']) ?>
                                    </p>
                                </div>
                                <div
                                    class="bg-gray-100 dark:bg-gray-700 rounded-xl p-5 text-center border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Net Pay</p>
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                        <?= formatPayrollCurrency($emp['net_pay']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Payroll Details Table -->
                            <div class="mb-6">
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                    <i class="fas fa-calculator text-gray-400 dark:text-gray-500"></i>
                                    Payroll Breakdown
                                </h4>
                                <div
                                    class="bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                    <table class="w-full">
                                        <tbody>
                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                <td class="py-3 px-5 text-sm text-gray-600 dark:text-gray-400">Gross Pay</td>
                                                <td class="py-3 px-5 text-sm font-bold text-gray-900 dark:text-white text-right">
                                                    <?= formatPayrollCurrency($emp['gross_pay']) ?>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                <td class="py-3 px-5 text-sm text-gray-600 dark:text-gray-400">Claim/s</td>
                                                <td class="py-3 px-5 text-sm font-bold text-amber-600 dark:text-amber-400 text-right">
                                                    <?= formatPayrollCurrency($emp['claims_amount']) ?>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                <td class="py-3 px-5 text-sm text-gray-600 dark:text-gray-400">Deductions</td>
                                                <td class="py-3 px-5 text-sm font-bold text-red-600 dark:text-red-400 text-right">-
                                                    <?= formatPayrollCurrency($emp['total_deductions']) ?>
                                                </td>
                                            </tr>
                                            <tr class="bg-white dark:bg-gray-800">
                                                <td class="py-4 px-5 text-sm font-bold text-gray-900 dark:text-white">Net Pay</td>
                                                <td class="py-4 px-5 text-sm font-bold text-green-600 dark:text-green-400 text-right">
                                                    <?= formatPayrollCurrency($emp['net_pay']) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Deductions Breakdown -->
                            <div class="mb-6">
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                    <i class="fas fa-minus-circle text-gray-400 dark:text-gray-500"></i>
                                    Deductions Breakdown
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div
                                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">SSS</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            <?= formatPayrollCurrency($emp['sss_deduction'] ?? $emp['total_deductions'] * 0.2) ?>
                                        </p>
                                    </div>
                                    <div
                                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">PhilHealth</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            <?= formatPayrollCurrency($emp['philhealth_deduction'] ?? $emp['total_deductions'] * 0.15) ?>
                                        </p>
                                    </div>
                                    <div
                                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Pag-IBIG</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            <?= formatPayrollCurrency($emp['pagibig_deduction'] ?? $emp['total_deductions'] * 0.15) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Actions -->
                            <div class="flex justify-end gap-3 pt-5 border-t-2 border-gray-200 dark:border-gray-700">
                                <button type="button" onclick="closeModal('payrollReviewModal<?= $emp['id'] ?>')"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-all duration-200">
                                    Close
                                </button>
                                <button type="button" onclick="printPayslip(<?= $emp['id'] ?>)"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-hover rounded-xl flex items-center gap-2 transition-all duration-200">
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