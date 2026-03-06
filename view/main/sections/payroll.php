<div class="tab-content" id="payroll-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Payroll Management</h2>
            <p class="text-gray-500 text-sm mt-1">Process payroll and manage compensation</p>
        </div>
        <button onclick="processPayroll()" class="btn-primary">
            <i class="fas fa-calculator"></i>
            Process Payroll
        </button>
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
                    <option value="Pending" <?= $payrollStatusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
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
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Regular Hours</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Overtime</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Gross
                                Pay</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deductions</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Net
                                Pay</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payrollEmployees)): ?>
                            <?php foreach ($payrollEmployees as $emp): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                                <?= $emp['initials'] ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">
                                                    <?= htmlspecialchars($emp['full_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-400"><?= htmlspecialchars($emp['position']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= round($emp['total_regular_hours']) ?></td>
                                    <td class="py-3 text-sm text-gray-600"><?= round($emp['total_overtime_hours']) ?></td>
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        <?= formatPayrollCurrency($emp['gross_pay']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= formatPayrollCurrency($emp['total_deductions']) ?>
                                    </td>
                                    <td class="py-3 text-sm font-semibold text-gray-800">
                                        <?= formatPayrollCurrency($emp['net_pay']) ?>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <button onclick="Review(<?= $emp['id'] ?>)"
                                                class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-2.5 py-1 rounded-lg transition-colors duration-200 flex items-center gap-1">
                                                <i class="fas fa-receipt text-xs"></i>
                                                Review
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
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
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Total Regular Hours</p>
                        <p class="text-lg font-semibold text-gray-800"><?= round($payrollPageRegularHours) ?> hrs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Overtime Hours</p>
                        <p class="text-lg font-semibold text-gray-800"><?= round($payrollPageOvertimeHours) ?> hrs</p>
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

<script>
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