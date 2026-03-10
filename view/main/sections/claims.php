<div class="tab-content" id="claims-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Claims & Reimbursement</h2>
            <p class="text-gray-500 text-sm mt-1">Manage employee expense claims and reimbursements</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pending Claims</p>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-bold text-gray-800"><?= $claimsPendingCount ?></p>
                <span class="text-sm text-gray-500">₱<?= number_format($claimsPendingTotal) ?> total</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Awaiting review</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Approved</p>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-bold text-gray-800"><?= $claimsApprovedCount ?></p>
                <span class="text-sm text-gray-500">₱<?= number_format($claimsApprovedTotal) ?> total</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Ready for processing</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Processed This Month</p>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-bold text-gray-800"><?= $claimsProcessedCount ?></p>
                <span class="text-sm text-gray-500">₱<?= number_format($claimsProcessedTotal) ?> total</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Successfully completed</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter by:</span>
                <select name="claims_status" onchange="applyClaimsFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="all" <?= $claimsStatusFilter == 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="Pending" <?= $claimsStatusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $claimsStatusFilter == 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Paid" <?= $claimsStatusFilter == 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Rejected" <?= $claimsStatusFilter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <select name="claims_date" onchange="applyClaimsFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="30" <?= $claimsDateFilter == '30' ? 'selected' : '' ?>>Last 30 days</option>
                    <option value="90" <?= $claimsDateFilter == '90' ? 'selected' : '' ?>>Last 3 months</option>
                    <option value="180" <?= $claimsDateFilter == '180' ? 'selected' : '' ?>>Last 6 months</option>
                    <option value="365" <?= $claimsDateFilter == '365' ? 'selected' : '' ?>>This year</option>
                </select>
            </div>
            <?php if ($claimsStatusFilter != 'all' || $claimsDateFilter != '30'): ?>
                <a href="?tab=claims&claims_page=1" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Claims Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Recent Claims</h3>
            <span class="text-xs font-medium bg-white text-gray-600 px-2.5 py-1 rounded-full border border-gray-200">
                Showing <?= count($claimsList) ?> of <?= $claimsTotal ?> claims
            </span>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($claimsList)): ?>
                            <?php foreach ($claimsList as $claim): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                                <?= $claim['initials'] ?>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-800"><?= htmlspecialchars($claim['employee_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= htmlspecialchars($claim['category']) ?></td>
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        ₱<?= number_format($claim['amount'], 2) ?></td>
                                    <td class="py-3 text-sm text-gray-600"><?= $claim['formatted_date'] ?></td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= getClaimStatusClass($claim['status']) ?>">
                                            <?= $claim['status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <?php if ($claim['receipt_path']): ?>
                                                <a href="<?= $claim['receipt_path'] ?>" target="_blank" class="btn-primary">
                                                    <i class="fas fa-receipt text-xs"></i>
                                                    Receipt
                                                </a>
                                            <?php else: ?>
                                                <span class="text-xs">No receipt</span>
                                            <?php endif; ?>
                                            <?php if ($claim['status'] == 'Pending'): ?>
                                                <button onclick="approveClaim(<?= $claim['id'] ?>)"
                                                    class="text-sm text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-2.5 py-1 rounded-lg transition-colors duration-200 border border-green-200 flex items-center gap-1">
                                                    <i class="fas fa-check text-xs"></i>
                                                    Approve
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">No claims found</p>
                                    <p class="text-sm">Try adjusting your filters</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($claimsTotalPages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Showing <span
                            class="font-medium"><?= min(1 + ($claimsPage - 1) * $claimsPerPage, $claimsTotal) ?>-<?= min($claimsPage * $claimsPerPage, $claimsTotal) ?></span>
                        of <span class="font-medium"><?= $claimsTotal ?></span> claims
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($claimsPage > 1): ?>
                            <a href="?tab=claims&claims_page=<?= $claimsPage - 1 ?>&claims_status=<?= urlencode($claimsStatusFilter) ?>&claims_date=<?= urlencode($claimsDateFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= min(5, $claimsTotalPages); $i++): ?>
                            <a href="?tab=claims&claims_page=<?= $i ?>&claims_status=<?= urlencode($claimsStatusFilter) ?>&claims_date=<?= urlencode($claimsDateFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg <?= $i == $claimsPage ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?> transition-colors">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($claimsPage < $claimsTotalPages): ?>
                            <a href="?tab=claims&claims_page=<?= $claimsPage + 1 ?>&claims_status=<?= urlencode($claimsStatusFilter) ?>&claims_date=<?= urlencode($claimsDateFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
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

            <!-- Table Footer with Summary -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-sm">
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600">
                            <span class="font-medium text-gray-800">Total Pending:</span>
                            ₱<?= number_format($claimsTotalPendingAmount, 2) ?>
                        </span>
                        <span class="text-gray-600">
                            <span class="font-medium text-gray-800">Total Approved:</span>
                            ₱<?= number_format($claimsTotalApprovedAmount, 2) ?>
                        </span>
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
                            <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">Paid</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">Rejected</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Footer -->
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center justify-between">
            <span class="text-xs text-gray-600">Average Claim Amount</span>
            <span class="text-sm font-medium text-gray-800">₱<?= number_format($claimsAverageAmount, 2) ?></span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center justify-between">
            <span class="text-xs text-gray-600">Most Common Type</span>
            <span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($claimsMostCommonType) ?></span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center justify-between">
            <span class="text-xs text-gray-600">This Month Total</span>
            <span class="text-sm font-medium text-gray-800">₱<?= number_format($claimsMonthTotal, 2) ?></span>
        </div>
    </div>
</div>

<script>
    function applyClaimsFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'claims');
        url.searchParams.set('claims_page', '1');

        const status = document.querySelector('select[name="claims_status"]')?.value;
        const date = document.querySelector('select[name="claims_date"]')?.value;

        if (status) url.searchParams.set('claims_status', status);
        else url.searchParams.delete('claims_status');

        if (date) url.searchParams.set('claims_date', date);
        else url.searchParams.delete('claims_date');

        window.location.href = url.toString();
    }

</script>