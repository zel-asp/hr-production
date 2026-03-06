<div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Claims & Reimbursement</h2>


    <!-- Tabs: Submit New Claim | My Claims History -->
    <div class="border-b border-gray-200 mb-6">
        <div class="flex space-x-4">
            <a href="?tab=claims&panel=new"
                class="tab-btn py-2 px-1 border-b-2 <?= $claimsActiveTab == 'new' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?> font-medium text-sm transition">
                <i class="fa-solid fa-plus mr-1"></i>Submit New Claim
            </a>
            <a href="?tab=claims&panel=history&claims_page=1"
                class="tab-btn py-2 px-1 border-b-2 <?= $claimsActiveTab == 'history' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?> font-medium text-sm transition">
                <i class="fa-solid fa-clock-rotate-left mr-1"></i>My Claims History
            </a>
        </div>
    </div>

    <!-- Tab 1: Submit New Claim Form -->
    <div id="newClaimPanel" class="tab-panel <?= $claimsActiveTab == 'new' ? '' : 'hidden' ?>">
        <form method="POST" action="/submit-claims" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="receipt_url" id="receipt_url">
            <input type="hidden" name="employeeId" value="<?= $employeeInfo['id'] ?>">
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Expense Date -->
                <div>
                    <label for="expense_date" class="block text-xs font-medium text-gray-500 mb-1">
                        Date of Expense <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="expense_date" name="expense_date" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-xs font-medium text-gray-500 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                        <option value="">Select Category</option>
                        <option value="Travel">Travel</option>
                        <option value="Meals">Meals / Client Dinner</option>
                        <option value="Transportation">Transportation / Gas</option>
                        <option value="Office Supplies">Office Supplies</option>
                        <option value="Training">Training / Seminar</option>
                        <option value="Equipment">Equipment / Gadgets</option>
                        <option value="Communication"> Communication / Internet</option>
                        <option value="Other"> Other</option>
                    </select>
                </div>

                <!-- Merchant/Vendor -->
                <div class="md:col-span-2">
                    <label for="merchant" class="block text-xs font-medium text-gray-500 mb-1">
                        Merchant / Vendor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="merchant" name="merchant" required
                        placeholder="e.g., Philippine Airlines, National Bookstore, Shell"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-xs font-medium text-gray-500 mb-1">
                        Amount (₱) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₱</span>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"
                            placeholder="0.00">
                    </div>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-medium text-gray-500 mb-1">
                        Description / Purpose
                    </label>
                    <textarea id="description" name="description" rows="2"
                        placeholder="Briefly describe the expense and its business purpose..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                </div>

                <!-- Receipt Upload -->
                <div class="md:col-span-2">
                    <label for="receipt" class="block text-xs font-medium text-gray-500 mb-1">
                        Upload Receipt <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-primary transition cursor-pointer"
                        onclick="document.getElementById('receipt').click()">
                        <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                        <i class="fa-solid fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                    <div id="filePreview" class="hidden mt-2 p-2 bg-gray-50 rounded-md">
                        <p class="text-sm text-gray-600"><i class="fa-solid fa-file mr-1"></i> <span
                                id="fileName"></span></p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="reset" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                    Clear
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Submit Claim
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 2: My Claims History -->
    <div id="claimsHistoryPanel" class="tab-panel <?= $claimsActiveTab == 'history' ? '' : 'hidden' ?>">
        <!-- Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div class="flex gap-2">
                <select name="claims_status" onchange="applyClaimsFilter()"
                    class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="all" <?= $claimsStatusFilter == 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="Pending" <?= $claimsStatusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $claimsStatusFilter == 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Paid" <?= $claimsStatusFilter == 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Rejected" <?= $claimsStatusFilter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <select name="claims_period" onchange="applyClaimsFilter()"
                    class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="3" <?= $claimsPeriodFilter == '3' ? 'selected' : '' ?>>Last 3 months</option>
                    <option value="6" <?= $claimsPeriodFilter == '6' ? 'selected' : '' ?>>Last 6 months</option>
                    <option value="12" <?= $claimsPeriodFilter == '12' ? 'selected' : '' ?>>This year</option>
                    <option value="all" <?= $claimsPeriodFilter == 'all' ? 'selected' : '' ?>>All time</option>
                </select>

                <?php if ($claimsStatusFilter != 'all' || $claimsPeriodFilter != '3'): ?>
                    <a href="?tab=claims&panel=history&claims_page=1"
                        class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 px-2">
                        <i class="fa-solid fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Results count -->
        <div class="mb-3 text-xs text-gray-500">
            Showing <?= count($claimsList) ?> of <?= $totalClaims ?> claims
        </div>

        <!-- Claims List -->
        <div class="space-y-3">
            <?php if (!empty($claimsList)): ?>
                <?php foreach ($claimsList as $claim):
                    $icon = getClaimIcon($claim['category']);
                    $iconBgClass = getIconBgClass($claim['category']);
                    $statusText = ucfirst(strtolower($claim['status']));
                    ?>
                    <!-- Claim Item -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-start gap-3">
                            <div class="<?= $iconBgClass ?> p-2 rounded-lg">
                                <i class="fa-solid <?= $icon ?>"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-medium text-gray-800"><?= htmlspecialchars($claim['category']) ?></h4>
                                    <span class="text-xs <?= $claim['status_class'] ?> px-2 py-0.5 rounded-full">
                                        <?= $statusText ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($claim['merchant']) ?> · <?= $claim['formatted_date'] ?>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-xs text-gray-400"><?= $claim['claim_number'] ?></p>
                                    <?php if (!empty($claim['employee_name'])): ?>
                                        <span class="text-xs text-gray-400">•
                                            <?= htmlspecialchars($claim['employee_name']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($claim['status'] == 'Rejected' && !empty($claim['rejection_reason'])): ?>
                                    <p class="text-xs text-amber-600 mt-1">
                                        <i class="fa-solid fa-comment mr-1"></i><?= htmlspecialchars($claim['rejection_reason']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($claim['description'])): ?>
                                    <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($claim['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-receipt text-4xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-medium">No claims found</p>
                    <p class="text-sm">Try adjusting your filters or submit a new claim</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalClaimsPages > 1): ?>
            <div class="mt-6 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    Showing <span
                        class="font-medium"><?= min(1 + ($claimsPage - 1) * $claimsPerPage, $totalClaims) ?>-<?= min($claimsPage * $claimsPerPage, $totalClaims) ?></span>
                    of <span class="font-medium"><?= $totalClaims ?></span> claims
                </p>
                <div class="flex items-center gap-2">
                    <?php if ($claimsPage > 1): ?>
                        <a href="?tab=claims&panel=history&claims_page=<?= $claimsPage - 1 ?>&claims_status=<?= urlencode($claimsStatusFilter) ?>&claims_period=<?= urlencode($claimsPeriodFilter) ?>"
                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                    <?php else: ?>
                        <button
                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                            disabled>
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $claimsPage - 2);
                    $endPage = min($totalClaimsPages, $claimsPage + 2);

                    if ($startPage > 1) {
                        echo '<a href="?tab=claims&panel=history&claims_page=1&claims_status=' . urlencode($claimsStatusFilter) . '&claims_period=' . urlencode($claimsPeriodFilter) . '" 
                       class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">1</a>';
                        if ($startPage > 2) {
                            echo '<span class="text-gray-400">...</span>';
                        }
                    }

                    for ($i = $startPage; $i <= $endPage; $i++) {
                        if ($i == $claimsPage) {
                            echo '<button class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-gray-800 text-white">' . $i . '</button>';
                        } else {
                            echo '<a href="?tab=claims&panel=history&claims_page=' . $i . '&claims_status=' . urlencode($claimsStatusFilter) . '&claims_period=' . urlencode($claimsPeriodFilter) . '" 
                           class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">' . $i . '</a>';
                        }
                    }

                    if ($endPage < $totalClaimsPages) {
                        if ($endPage < $totalClaimsPages - 1) {
                            echo '<span class="text-gray-400">...</span>';
                        }
                        echo '<a href="?tab=claims&panel=history&claims_page=' . $totalClaimsPages . '&claims_status=' . urlencode($claimsStatusFilter) . '&claims_period=' . urlencode($claimsPeriodFilter) . '" 
                       class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">' . $totalClaimsPages . '</a>';
                    }
                    ?>

                    <?php if ($claimsPage < $totalClaimsPages): ?>
                        <a href="?tab=claims&panel=history&claims_page=<?= $claimsPage + 1 ?>&claims_status=<?= urlencode($claimsStatusFilter) ?>&claims_period=<?= urlencode($claimsPeriodFilter) ?>"
                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    <?php else: ?>
                        <button
                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                            disabled>
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for File Upload Preview (only) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // File upload preview
        const receiptInput = document.getElementById('receipt');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');

        if (receiptInput) {
            receiptInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    fileName.textContent = this.files[0].name;
                    filePreview.classList.remove('hidden');
                } else {
                    filePreview.classList.add('hidden');
                }
            });
        }
    });

    // Claims filter function
    function applyClaimsFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'claims');
        url.searchParams.set('panel', 'history');
        url.searchParams.set('claims_page', '1');

        const status = document.querySelector('select[name="claims_status"]')?.value;
        const period = document.querySelector('select[name="claims_period"]')?.value;

        if (status) url.searchParams.set('claims_status', status);
        else url.searchParams.delete('claims_status');

        if (period) url.searchParams.set('claims_period', period);
        else url.searchParams.delete('claims_period');

        window.location.href = url.toString();
    }

    function exportClaimsData() {
        const status = document.querySelector('select[name="claims_status"]')?.value;
        const period = document.querySelector('select[name="claims_period"]')?.value;
        window.location.href = `/export-claims?status=${status}&period=${period}`;
    }
</script>