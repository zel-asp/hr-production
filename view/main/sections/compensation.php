<div class="tab-content" id="compensation-content">
    <!-- Header Section - Minimalist -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-xl font-medium text-gray-900">Compensation planning</h2>
            <p class="text-gray-500 text-sm mt-1">Manage salary structures and reviews</p>
        </div>
        <button onclick="openModal('salaryReviewModal')" class="btn-primary">
            <i class="fas fa-plus text-sm"></i>
            New review
        </button>
    </div>

    <!-- Stats Cards - Clean & Minimal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 border border-gray-100">
            <p class="text-xs text-gray-500 mb-1">Average salary</p>
            <p class="text-xl font-bold text-gray-900"><?= formatSalary($avgSalary) ?></p>
            <span class="text-xs text-gray-400">Across all positions</span>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-100">
            <p class="text-xs text-gray-500 mb-1">Salary range</p>
            <p class="text-xl font-bold text-gray-900"><?= formatSalary($minSalary) ?> –
                <?= formatSalary($maxSalary) ?>
            </p>
            <span class="text-xs text-gray-400">Entry to executive</span>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-100">
            <p class="text-xs text-gray-500 mb-1">Pending reviews</p>
            <p class="text-xl font-bold text-gray-900"><?= $pendingReviewsCount ?></p>
            <span class="text-xs text-gray-400">Awaiting approval</span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Salary Bands - Left Column -->
        <div class="lg:col-span-1 bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-900">Salary bands</h3>
                    <span class="text-xs text-gray-500"><?= count($salaryBands) ?> positions</span>
                </div>
            </div>
            <div class="p-5 max-h-[480px] overflow-y-auto">
                <div class="space-y-5">
                    <?php if (!empty($salaryBands)): ?>
                        <?php foreach ($salaryBands as $band):
                            $midpoint = ($band['min_salary'] + $band['max_salary']) / 2;
                            ?>
                            <div class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($band['position']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400"><?= $band['employee_count'] ?> employees</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900"><?= formatSalary($band['min_salary']) ?> –
                                            <?= formatSalary($band['max_salary']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400">Mid: <?= formatSalary($midpoint) ?></p>
                                    </div>
                                </div>
                                <!-- Mini range indicator -->
                                <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                                    <?php
                                    $rangeWidth = $band['max_salary'] - $band['min_salary'];
                                    $avgPosition = $rangeWidth > 0 ? (($band['avg_salary'] - $band['min_salary']) / $rangeWidth) * 100 : 50;
                                    ?>
                                    <div class="h-full bg-gray-900 rounded-full" style="width: <?= $avgPosition ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-400 text-center py-4">No salary bands available</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/30">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Projected monthly payroll</span>
                    <span class="font-medium text-gray-900"><?= formatSalary($totalBudget) ?></span>
                </div>
            </div>
        </div>

        <!-- Right Column - Reviews and Adjustments -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Reviews Section -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <!-- Header with filters -->
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="text-sm font-medium text-gray-900">Upcoming reviews</h3>

                        <!-- Filter Bar - Minimal -->
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="compensation_status" onchange="applyCompensationFilter()"
                                class="px-2 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <option value="">Status</option>
                                <option value="draft" <?= $compensationStatusFilter == 'draft' ? 'selected' : '' ?>>Draft
                                </option>
                                <option value="pending_finance" <?= $compensationStatusFilter == 'pending_finance' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $compensationStatusFilter == 'approved' ? 'selected' : '' ?>>
                                    Approved</option>
                                <option value="rejected" <?= $compensationStatusFilter == 'rejected' ? 'selected' : '' ?>>
                                    Rejected</option>
                            </select>

                            <select name="compensation_type" onchange="applyCompensationFilter()"
                                class="px-2 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <option value="">Type</option>
                                <option value="annual" <?= $compensationTypeFilter == 'annual' ? 'selected' : '' ?>>Annual
                                </option>
                                <option value="promotion" <?= $compensationTypeFilter == 'promotion' ? 'selected' : '' ?>>
                                    Promotion</option>
                                <option value="merit" <?= $compensationTypeFilter == 'merit' ? 'selected' : '' ?>>Merit
                                </option>
                            </select>

                            <select name="compensation_department" onchange="applyCompensationFilter()"
                                class="px-2 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <option value="">Department</option>
                                <?php foreach ($compensationDepartments as $dept): ?>
                                    <option value="<?= htmlspecialchars($dept['department']) ?>"
                                        <?= $compensationDepartmentFilter == $dept['department'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['department']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if (!empty($compensationStatusFilter) || !empty($compensationTypeFilter) || !empty($compensationDepartmentFilter)): ?>
                                <a href="?tab=compensation&compensation_page=1"
                                    class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-900">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2"><?= count($upcomingReviews) ?> of
                        <?= $totalCompensationReviews ?> reviews
                    </p>
                </div>

                <!-- Reviews List -->
                <div class="p-5 max-h-[380px] overflow-y-auto">
                    <div class="space-y-3">
                        <?php if (!empty($upcomingReviews)): ?>
                            <?php foreach ($upcomingReviews as $review):
                                $currentMonthly = $review['hourly_rate'] * 8 * 22;
                                $proposedMonthly = $review['proposed_salary'];
                                $increaseAmount = $proposedMonthly - $currentMonthly;
                                $increasePercent = $currentMonthly > 0 ? round(($increaseAmount / $currentMonthly) * 100) : 0;

                                // Status badge
                                $statusStyles = [
                                    'approved' => 'bg-green-50 text-green-700',
                                    'pending_finance' => 'bg-yellow-50 text-yellow-700',
                                    'rejected' => 'bg-red-50 text-red-700',
                                    'draft' => 'bg-gray-50 text-gray-700'
                                ];
                                $statusClass = $statusStyles[$review['status']] ?? 'bg-gray-50 text-gray-700';
                                $statusText = ucfirst(str_replace('_', ' ', $review['status']));

                                // Due badge
                                $dueClass = $review['days_until_effective'] < 0 ? 'bg-red-50 text-red-700' : ($review['days_until_effective'] <= 7 ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700');
                                $dueText = $review['days_until_effective'] < 0 ? 'Overdue' : 'Due ' . date('M j', strtotime($review['effective_date']));
                                ?>
                                <div class="p-4 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs font-medium text-gray-600">
                                                <?= $review['initials'] ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($review['full_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-400"><?= htmlspecialchars($review['position']) ?> •
                                                    <?= $review['hired_date'] ? floor((time() - strtotime($review['hired_date'])) / (365 * 24 * 60 * 60)) : 0 ?>
                                                    yrs
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-md <?= $dueClass ?>"><?= $dueText ?></span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <p class="text-xs text-gray-400">Current</p>
                                            <p class="text-sm font-medium text-gray-900"><?= formatSalary($currentMonthly) ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Proposed</p>
                                            <p class="text-sm font-medium text-green-600"><?= formatSalary($proposedMonthly) ?>
                                                <span class="text-xs text-gray-400">(+<?= $increasePercent ?>%)</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-around lg:justify-between">
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-md <?= $statusClass ?>"><?= $statusText ?></span>
                                        <div class="flex items-center gap-2">
                                            <?php if ($review['status'] == 'pending_finance' || $review['status'] == 'draft'): ?>
                                                <!-- Delete button - only show for pending/draft reviews -->
                                                <button
                                                    onclick="openDeleteModal(<?= $review['id'] ?>, '<?= htmlspecialchars($review['full_name']) ?>')"
                                                    class="text-xs text-red-500 hover:text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="Delete review">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="openModal('reviewModal<?= $review['id'] ?>')"
                                                class="text-xs text-gray-500 hover:text-gray-900 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                                                Review <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-400 text-center py-6">No reviews found</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalCompensationPages > 1): ?>
                    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-center gap-2">
                        <?php if ($compensationPage > 1): ?>
                            <a href="?tab=compensation&compensation_page=<?= $compensationPage - 1 ?><?= !empty($compensationStatusFilter) ? '&compensation_status=' . urlencode($compensationStatusFilter) : '' ?><?= !empty($compensationTypeFilter) ? '&compensation_type=' . urlencode($compensationTypeFilter) : '' ?><?= !empty($compensationDepartmentFilter) ? '&compensation_department=' . urlencode($compensationDepartmentFilter) : '' ?>"
                                class="w-7 h-7 flex items-center justify-center text-xs rounded border border-gray-200 text-gray-500 hover:border-gray-900 hover:text-gray-900 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400 mx-2">Page <?= $compensationPage ?> of
                            <?= $totalCompensationPages ?></span>
                        <?php if ($compensationPage < $totalCompensationPages): ?>
                            <a href="?tab=compensation&compensation_page=<?= $compensationPage + 1 ?><?= !empty($compensationStatusFilter) ? '&compensation_status=' . urlencode($compensationStatusFilter) : '' ?><?= !empty($compensationTypeFilter) ? '&compensation_type=' . urlencode($compensationTypeFilter) : '' ?><?= !empty($compensationDepartmentFilter) ? '&compensation_department=' . urlencode($compensationDepartmentFilter) : '' ?>"
                                class="w-7 h-7 flex items-center justify-center text-xs rounded border border-gray-200 text-gray-500 hover:border-gray-900 hover:text-gray-900 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Adjustments - Minimal -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-medium text-gray-900">Recent adjustments</h3>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <?php if (!empty($recentAdjustments)): ?>
                            <?php foreach ($recentAdjustments as $adj):
                                $increaseAmount = $adj['proposed_salary'] - $adj['current_monthly'];
                                ?>
                                <div
                                    class="flex items-center justify-between py-2 <?= $adjustmentIndex < count($recentAdjustments) ? 'border-b border-gray-100' : '' ?>">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-xs font-medium text-gray-600">
                                            <?= $adj['initials'] ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($adj['full_name']) ?>
                                            </p>
                                            <p class="text-xs text-gray-400"><?= htmlspecialchars($adj['position']) ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-green-600">+<?= formatSalary($increaseAmount) ?></p>
                                        <p class="text-xs text-gray-400"><?= date('M j', strtotime($adj['effective_date'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-400 text-center py-4">No recent adjustments</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal - Redesigned -->
<?php if (!empty($upcomingReviews)): ?>
    <?php foreach ($upcomingReviews as $review):
        $currentMonthly = $review['hourly_rate'] * 8 * 22;
        $proposedMonthly = $review['proposed_salary'];
        $increaseAmount = $proposedMonthly - $currentMonthly;
        $increasePercent = $currentMonthly > 0 ? round(($increaseAmount / $currentMonthly) * 100) : 0;
        ?>
        <div id="reviewModal<?= $review['id'] ?>"
            class="modal fixed inset-0 bg-gray-900/20 flex items-center justify-center hidden modal-enter z-50 backdrop-blur-sm">
            <div class="bg-white rounded-xl max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-medium text-gray-900">Review details</h3>
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-md 
                            <?= $review['status'] == 'approved' ? 'bg-green-50 text-green-700' : ($review['status'] == 'pending_finance' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-700') ?>">
                            <?= ucfirst(str_replace('_', ' ', $review['status'])) ?>
                        </span>
                    </div>
                    <button onclick="closeModal('reviewModal<?= $review['id'] ?>')" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <form method="POST" action="/add-compensation" class="space-y-5">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                        <input type="hidden" name="employee_id" value="<?= $review['employee_id'] ?>">
                        <!--Employee Info - Minimal -->
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                            <div
                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-sm font-medium text-gray-600">
                                <?= $review['initials'] ?>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($review['full_name']) ?></p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars($review['position']) ?> •
                                    <?= htmlspecialchars($review['department'] ?? 'N/A') ?>
                                </p>
                            </div>
                        </div>

                        <!-- Review Fields -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Type</label>
                                <?php if ($review['status'] != 'approved' && $review['status'] != 'rejected'): ?>
                                    <select name="review_type"
                                        class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900">
                                        <option value="annual" <?= $review['review_type'] == 'annual' ? 'selected' : '' ?>>Annual
                                        </option>
                                        <option value="promotion" <?= $review['review_type'] == 'promotion' ? 'selected' : '' ?>>
                                            Promotion</option>
                                        <option value="merit" <?= $review['review_type'] == 'merit' ? 'selected' : '' ?>>Merit</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" value="<?= ucfirst($review['review_type']) ?>"
                                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg" readonly>
                                    <input type="hidden" name="review_type" value="<?= $review['review_type'] ?>">
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Effective</label>
                                <?php if ($review['status'] != 'approved' && $review['status'] != 'rejected'): ?>
                                    <input type="date" name="effective_date" value="<?= $review['effective_date'] ?>"
                                        class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <?php else: ?>
                                    <input type="text" value="<?= date('M j, Y', strtotime($review['effective_date'])) ?>"
                                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg" readonly>
                                    <input type="hidden" name="effective_date" value="<?= $review['effective_date'] ?>">
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Salary Comparison - Clean -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 mb-3">Salary adjustment</p>

                            <?php if ($review['status'] != 'approved' && $review['status'] != 'rejected'): ?>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Current monthly</label>
                                        <input type="number" name="current_salary" step="0.01"
                                            value="<?= $review['current_salary'] ?>" readonly
                                            class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-200 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Proposed monthly</label>
                                        <input type="number" name="proposed_salary" step="0.01"
                                            value="<?= $review['proposed_salary'] ?>"
                                            class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900"
                                            id="edit_proposed_salary_<?= $review['id'] ?>"
                                            oninput="calculateEditIncrease(<?= $review['id'] ?>)">
                                    </div>
                                </div>
                                <input type="hidden" name="proposed_hourly_rate" id="edit_proposed_hourly_rate_<?= $review['id'] ?>"
                                    value="<?= $review['proposed_hourly_rate'] ?? '' ?>">
                            <?php endif; ?>

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-400">Current</p>
                                    <p class="text-base font-medium text-gray-900"><?= formatSalary($currentMonthly) ?></p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-300"></i>
                                <div>
                                    <p class="text-xs text-gray-400">Proposed</p>
                                    <p class="text-base font-medium text-green-600"
                                        id="edit_proposed_display_<?= $review['id'] ?>">
                                        <?= formatSalary($proposedMonthly) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400">Increase</p>
                                    <p class="text-sm font-medium text-green-600"
                                        id="edit_increase_display_<?= $review['id'] ?>">
                                        +<?= formatSalary($increaseAmount) ?> (+<?= $increasePercent ?>%)
                                    </p>
                                </div>
                            </div>
                        </div>


                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Notes</label>
                            <textarea name="finance_notes" rows="2"
                                class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-900"
                                placeholder="Add notes..." <?= ($review['status'] == 'approved' || $review['status'] == 'rejected') ? 'readonly' : '' ?>><?= htmlspecialchars($review['finance_notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" onclick="closeModal('reviewModal<?= $review['id'] ?>')"
                                class="px-4 py-2 text-xs text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200">
                                Cancel
                            </button>
                            <?php if ($review['status'] != 'approved' && $review['status'] != 'rejected'): ?>
                                <button type="submit" name="update" value="save" class="btn-primary">
                                    Update changes
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>


    function applyCompensationFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'compensation');
        url.searchParams.set('compensation_page', '1');

        const status = document.querySelector('select[name="compensation_status"]').value;
        const type = document.querySelector('select[name="compensation_type"]').value;
        const dept = document.querySelector('select[name="compensation_department"]').value;

        if (status) url.searchParams.set('compensation_status', status);
        else url.searchParams.delete('compensation_status');

        if (type) url.searchParams.set('compensation_type', type);
        else url.searchParams.delete('compensation_type');

        if (dept) url.searchParams.set('compensation_department', dept);
        else url.searchParams.delete('compensation_department');

        window.location.href = url.toString();
    }

    function loadEmployeeSalary() {
        const select = document.getElementById('comp_employee');
        const selected = select.options[select.selectedIndex];
        const hourlyRate = selected.dataset.hourly || 0;
        const monthlySalary = hourlyRate * 8 * 22;


        document.getElementById('current_salary_display').value = '₱' + monthlySalary.toLocaleString();
        document.getElementById('current_salary').value = monthlySalary;
        document.getElementById('proposed_salary').value = '';
        document.getElementById('increase_amount').value = '';
        document.getElementById('increase_percentage').value = '';
        document.getElementById('ratePerHour').value = ratePerHour;
    }

    function calculateIncrease() {
        const current = parseFloat(document.getElementById('current_salary').value) || 0;
        const proposed = parseFloat(document.getElementById('proposed_salary').value) || 0;

        // Calculate hourly rate (monthly salary / (8 hours * 22 days))
        const hourlyRate = ((proposed / 8) / 22).toFixed(2);

        // Set the hidden field value
        document.getElementById('proposed_hourly_rate').value = hourlyRate;

        // Also update display if you have one
        const hourlyDisplay = document.getElementById('hourly_rate_display');
        if (hourlyDisplay) {
            hourlyDisplay.value = '₱' + hourlyRate;
        }

        if (current > 0 && proposed > 0) {
            const increase = proposed - current;
            const percentage = ((increase / current) * 100).toFixed(2);

            document.getElementById('increase_amount').value = '₱' + increase.toLocaleString();
            document.getElementById('increase_percentage').value = percentage + '%';
        } else {
            document.getElementById('increase_amount').value = '';
            document.getElementById('increase_percentage').value = '';
        }
    }

    function calculateEditIncrease(reviewId) {
        const current = parseFloat(document.querySelector(`#edit_proposed_salary_${reviewId}`).closest('form').querySelector('input[name="current_salary"]').value) || 0;
        const proposed = parseFloat(document.getElementById(`edit_proposed_salary_${reviewId}`).value) || 0;

        // Calculate hourly rate (monthly salary / (8 hours * 22 days))
        const hourlyRate = ((proposed / 8) / 22).toFixed(2);

        // Update the hidden field
        const hourlyField = document.getElementById(`edit_proposed_hourly_rate_${reviewId}`);
        if (hourlyField) {
            hourlyField.value = hourlyRate;
        }

        if (current > 0 && proposed > 0) {
            const increase = proposed - current;
            const percentage = ((increase / current) * 100).toFixed(1);

            document.getElementById(`edit_proposed_display_${reviewId}`).textContent = '₱' + proposed.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById(`edit_increase_display_${reviewId}`).innerHTML = '+' +
                '₱' + increase.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                ' (+' + percentage + '%)';
        }
    }
</script>