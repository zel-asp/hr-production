<div class="tab-content" id="payroll-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Payroll Management</h2>
            <p class="text-gray-500 text-sm mt-1">Process payroll and manage compensation</p>
        </div>
        <form action="/process-all-payroll" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="process_all" value="1">
            <button type="submit" class="btn-primary"
                onclick="return confirm('Process payroll for all employees? This may take a moment.')">
                <i class="fas fa-calculator"></i>
                Process All Payroll
            </button>
        </form>
    </div>

    <!-- Payroll Period Info -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-gray-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Current Payroll Period</p>
                    <p class="text-lg font-semibold text-gray-800"><?= $payrollPeriodLabel ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">Payroll Date:</span>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                    <?= date('F j, Y', strtotime($payrollPayDate)) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter by:</span>
                <select name="payroll_status" onchange="applyPayrollFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Status</option>
                    <option value="Processed" <?= $payrollStatusFilter == 'Processed' ? 'selected' : '' ?>>Processed
                    </option>
                    <option value="Processing" <?= $payrollStatusFilter == 'Processing' ? 'selected' : '' ?>>Processing
                    </option>
                    <option value="Pending" <?= $payrollStatusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Rejected" <?= $payrollStatusFilter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>

                <select name="payroll_department" onchange="applyPayrollFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Departments</option>
                    <?php foreach ($payrollDepartments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept['department']) ?>"
                            <?= $payrollDepartmentFilter == $dept['department'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['department']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($payrollStatusFilter) || !empty($payrollDepartmentFilter)): ?>
                <a href="?tab=payroll&payroll_page=1"
                    class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Payroll Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols- gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Gross Pay</p>
            <p class="text-2xl font-bold text-gray-800"><?= formatPayrollCurrency($payrollTotalGross) ?></p>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs text-gray-400">Before deductions</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Net Pay</p>
            <p class="text-2xl font-bold text-gray-800"><?= formatPayrollCurrency($payrollTotalNet) ?></p>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs text-gray-400">Take-home pay</span>
            </div>
        </div>
    </div>

    <!-- Payroll List -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Payroll Summary</h3>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500"><?= $payrollTotalEmployees ?> employees</span>
                <span class="inline-flex items-center gap-1 text-xs">
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                    <span class="text-gray-500">Processed: <?= $payrollProcessedCount ?></span>
                </span>
                <span class="inline-flex items-center gap-1 text-xs">
                    <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                    <span class="text-gray-500">Pending: <?= $payrollPendingCount ?></span>
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="text-left py-3 pl-4 pr-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Employee
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Regular Hours
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Overtime
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Claims
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Gross Pay
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Deductions
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Net Pay
                            </th>
                            <th
                                class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Status
                            </th>
                            <th
                                class="text-left py-3 pl-6 pr-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payrollEmployees)): ?>
                            <?php foreach ($payrollEmployees as $emp): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 pl-4 pr-6">
                                        <div class="flex items-center gap-3 min-w-45">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium shrink-0">
                                                <?= $emp['initials'] ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                    <?= htmlspecialchars($emp['full_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-400 truncate">
                                                    <?= htmlspecialchars($emp['position']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-sm text-gray-600 font-medium whitespace-nowrap">
                                            <?= round($emp['total_regular_hours']) ?> hrs
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-sm text-gray-600 font-medium whitespace-nowrap">
                                            <?= round($emp['total_overtime_hours']) ?> hrs
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <?php if ($emp['claims_count'] > 0): ?>
                                            <div class="flex flex-col min-w-25">
                                                <span class="text-sm font-medium text-green-600 whitespace-nowrap">
                                                    <?= formatPayrollCurrency($emp['claims_amount']) ?>
                                                </span>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">
                                                    <?= $emp['claims_count'] ?> claim<?= $emp['claims_count'] > 1 ? 's' : '' ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-sm font-medium text-gray-800 whitespace-nowrap">
                                            <?= formatPayrollCurrency($emp['gross_pay']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-sm text-gray-600 whitespace-nowrap">
                                            <?= formatPayrollCurrency($emp['total_deductions']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <div class="flex flex-col min-w-30">
                                            <span class="text-sm font-semibold text-gray-800 whitespace-nowrap">
                                                <?= formatPayrollCurrency($emp['net_pay']) ?>
                                            </span>
                                            <?php if ($emp['claims_amount'] > 0): ?>
                                                <span class="text-xs text-green-600 whitespace-nowrap">
                                                    (inc. <?= formatPayrollCurrency($emp['claims_amount']) ?> claims)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6">
                                        <?php
                                        // Determine status badge color
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
                                        <div class="flex flex-col">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border whitespace-nowrap <?= $statusBadgeClass ?>">
                                                <i class="fas <?= $statusIcon ?> mr-1.5"></i>
                                                <?= $emp['status'] ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 pl-6 pr-4">
                                        <div class="flex items-center gap-2 min-w-[160px]">
                                            <button onclick="openModal('payrollReviewModal<?= $emp['id'] ?>')"
                                                class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1.5 shadow-sm border border-gray-200 whitespace-nowrap">
                                                <i class="fas fa-eye text-xs"></i>
                                                <span>Review</span>
                                            </button>

                                            <?php if ($emp['status'] == 'Processed' || $emp['status'] == 'Processing'): ?>
                                                <!-- Update button for existing payroll -->
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
                                                    <button type="submit"
                                                        class="text-sm text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1.5 shadow-sm border border-blue-200 whitespace-nowrap">
                                                        <i class="fas fa-sync-alt text-xs"></i>
                                                        <span>Update</span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
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
                                                    <button type="submit"
                                                        class="text-sm text-green-500 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1.5 shadow-sm border border-green-200 whitespace-nowrap">
                                                        <i class="fas fa-check text-xs"></i>
                                                        <span>Process</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="py-12 text-center text-gray-500">
                                    <i class="fas fa-calculator text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">No payroll data found</p>
                                    <p class="text-sm">Click "Process Payroll" to generate payroll for this period</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payroll Summary Footer -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Total Regular Hours</p>
                        <p class="text-lg font-semibold text-gray-800"><?= round($payrollPageRegularHours) ?> hrs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Overtime Hours</p>
                        <p class="text-lg font-semibold text-gray-800"><?= round($payrollPageOvertimeHours) ?> hrs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Claims</p>
                        <p class="text-lg font-semibold text-green-600">
                            <?= formatPayrollCurrency($payrollPageClaimsTotal) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Average Net Pay</p>
                        <p class="text-lg font-semibold text-gray-800">
                            <?= formatPayrollCurrency($payrollPageAverageNet) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($payrollTotalPages > 1): ?>
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        Showing <span
                            class="font-medium"><?= min(1 + ($payrollPage - 1) * $payrollPerPage, $payrollTotalFiltered) ?>-<?= min($payrollPage * $payrollPerPage, $payrollTotalFiltered) ?></span>
                        of <span class="font-medium"><?= $payrollTotalFiltered ?></span> employees
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($payrollPage > 1): ?>
                            <a href="?tab=payroll&payroll_page=<?= $payrollPage - 1 ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= min(5, $payrollTotalPages); $i++): ?>
                            <a href="?tab=payroll&payroll_page=<?= $i ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg <?= $i == $payrollPage ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?> transition-colors duration-200">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($payrollPage < $payrollTotalPages): ?>
                            <a href="?tab=payroll&payroll_page=<?= $payrollPage + 1 ?>&payroll_status=<?= urlencode($payrollStatusFilter) ?>&payroll_department=<?= urlencode($payrollDepartmentFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Payroll Review Modals -->
<?php if (!empty($payrollEmployees)): ?>
    <?php foreach ($payrollEmployees as $emp): ?>
        <div id="payrollReviewModal<?= $emp['id'] ?>"
            class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-file-invoice text-gray-600"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Payroll Review -
                            <?= htmlspecialchars($emp['full_name']) ?>
                        </h3>
                    </div>
                    <button onclick="closeModal('payrollReviewModal<?= $emp['id'] ?>')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Employee Info Card -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-blue-700">
                                    <?= $emp['initials'] ?>
                                </span>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($emp['full_name']) ?>
                                </h4>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($emp['position']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 uppercase mb-1">Regular Hours</p>
                            <p class="text-2xl font-bold text-blue-700">
                                <?= round($emp['total_regular_hours']) ?>
                            </p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 uppercase mb-1">Overtime Hours</p>
                            <p class="text-2xl font-bold text-amber-700">
                                <?= round($emp['total_overtime_hours']) ?>
                            </p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 uppercase mb-1">Net Pay</p>
                            <p class="text-2xl font-bold text-green-700">
                                <?= formatPayrollCurrency($emp['net_pay']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Payroll Details Table -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-calculator text-gray-400"></i>
                            Payroll Breakdown
                        </h4>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-4 text-sm text-gray-600">Gross Pay</td>
                                        <td class="py-3 px-4 text-sm font-medium text-gray-800 text-right">
                                            <?= formatPayrollCurrency($emp['gross_pay']) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-4 text-sm text-gray-600">Claim/s</td>
                                        <td class="py-3 px-4 text-sm font-medium text-gray-800 text-right">
                                            <?= formatPayrollCurrency($emp['claims_amount']) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-4 text-sm text-gray-600">Deductions</td>
                                        <td class="py-3 px-4 text-sm font-medium text-red-600 text-right">-
                                            <?= formatPayrollCurrency($emp['total_deductions']) ?>
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <td class="py-3 px-4 text-sm font-semibold text-gray-700">Net Pay</td>
                                        <td class="py-3 px-4 text-sm font-bold text-green-700 text-right">
                                            <?= formatPayrollCurrency($emp['net_pay']) ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Deductions Breakdown -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-minus-circle text-gray-400"></i>
                            Deductions Breakdown
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">SSS</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <?= formatPayrollCurrency($emp['sss_deduction'] ?? $emp['total_deductions'] * 0.2) ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">PhilHealth</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <?= formatPayrollCurrency($emp['philhealth_deduction'] ?? $emp['total_deductions'] * 0.15) ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">Pag-IBIG</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <?= formatPayrollCurrency($emp['pagibig_deduction'] ?? $emp['total_deductions'] * 0.15) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal('payrollReviewModal<?= $emp['id'] ?>')"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Close
                        </button>
                        <button type="button" onclick="printPayslip(<?= $emp['id'] ?>)" class="btn-primary">
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