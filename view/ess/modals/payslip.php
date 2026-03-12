<!-- Payslip Modal -->
<div id="payslipModal" class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-md w-full mx-4 p-6 shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-file-lines mr-2 text-primary"></i>My Payslips
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="payslipModal">
                <i class="fa-solid fa-circle-xmark fa-xl"></i>
            </button>
        </div>

        <?php if (!empty($payslips)): ?>
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <p class="text-xs text-blue-600 font-medium">Latest Net Pay</p>
                    <p class="text-lg font-bold text-gray-800">₱<?= number_format($latestPayslip['net_pay'] ?? 0, 2) ?></p>
                    <p class="text-xs text-gray-500"><?= $latestPayslip['status'] ?? 'N/A' ?></p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <p class="text-xs text-green-600 font-medium">YTD Earnings</p>
                    <p class="text-lg font-bold text-gray-800">₱<?= number_format($ytdEarnings['total_net'] ?? 0, 2) ?></p>
                    <p class="text-xs text-gray-500"><?= $ytdEarnings['pay_periods'] ?? 0 ?> periods</p>
                </div>
            </div>

            <!-- Next Pay Date -->
            <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <i class="fa-solid fa-calendar-check text-amber-600 mr-2"></i>
                    <div>
                        <p class="text-xs text-amber-600 font-medium">Next Pay Date</p>
                        <p class="text-sm font-semibold text-gray-800"><?= date('F j, Y', strtotime($nextPayDate)) ?></p>
                        <p class="text-xs text-gray-500"><?= $nextPayPeriod ?></p>
                    </div>
                </div>
            </div>

            <p class="text-gray-500 text-sm mb-3">Download Payslips</p>
            <ul class="divide-y">
                <?php foreach ($payslips as $payslip): ?>
                    <li class="py-3 flex justify-around items-center">
                        <div>
                            <span class="font-medium block"><?= htmlspecialchars($payslip['month_year'] ?? '') ?></span>
                            <span class="text-xs text-gray-500">
                                <?= date('M j', strtotime($payslip['period_start'])) ?> -
                                <?= date('M j, Y', strtotime($payslip['period_end'])) ?>
                            </span>
                            <?php
                            $statusClass = '';
                            switch ($payslip['status']) {
                                case 'Pending':
                                    $statusClass = 'bg-amber-100 text-amber-700';
                                    break;
                                case 'Processing':
                                    $statusClass = 'bg-blue-100 text-blue-700';
                                    break;
                                case 'Approved':
                                    $statusClass = 'bg-green-100 text-green-700';
                                    break;
                                case 'Paid':
                                    $statusClass = 'bg-emerald-100 text-emerald-700';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-700';
                            }
                            ?>
                            <span class="text-xs <?= $statusClass ?> px-2 py-0.5 rounded-full ml-1 inline-block">
                                <?= htmlspecialchars($payslip['status'] ?? '') ?>
                            </span>
                        </div>
                        <span
                            class="text-primary text-sm bg-[#ecf3fa] px-3 py-1 rounded-md cursor-pointer hover:bg-[#d9e2ed] download-payslip"
                            data-id="<?= $payslip['id'] ?>"
                            data-period="<?= date('Y-m', strtotime($payslip['period_start'])) ?>"
                            data-employee="<?= urlencode($payslip['full_name']) ?>">
                            <i class="fa-solid fa-circle-down mr-1"></i>Download
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <!-- No payslips yet -->
            <div class="text-center py-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-file-invoice text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-gray-700 font-medium mb-1">No Payslips Yet</h4>
                <p class="text-sm text-gray-500 mb-4">Your payslips will appear here once payroll is processed.</p>

                <!-- Show sample/placeholder for first month employees -->
                <?php if (isset($employeeInfo['start_date']) && strtotime($employeeInfo['start_date']) > strtotime('-1 month')): ?>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-left">
                        <p class="text-xs text-blue-600 font-medium mb-1">💡 Just started?</p>
                        <p class="text-xs text-gray-600">Your first payslip will be available after the next payroll cut-off.
                            Please check back after <?= date('F j', strtotime($nextPayDate)) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mt-5 flex justify-end">
            <button
                class="close-modal bg-[#ecf3fa] text-primary px-4 py-2 rounded-md text-sm font-medium hover:bg-[#d9e2ed]"
                data-modal="payslipModal">
                Close
            </button>
        </div>
    </div>
</div>