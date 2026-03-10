<div class="tab-content" id="compensation-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Compensation Planning</h2>
            <p class="text-gray-500 text-sm mt-1">Manage salary structures and compensation reviews</p>
        </div>
        <button class="btn-primary" onclick="openModal('salaryReviewModal')">
            <i class="fas fa-plus"></i>
            New Salary Review
        </button>
    </div>

    <!-- Compensation Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Average Salary</p>
            <p class="text-2xl font-bold text-gray-800"><?= formatSalary($avgSalary) ?></p>
            <p class="text-xs text-gray-400 mt-1">Across all positions</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Salary Range</p>
            <p class="text-2xl font-bold text-gray-800"><?= formatSalary($minSalary) ?> -
                <?= formatSalary($maxSalary) ?>
            </p>
            <p class="text-xs text-gray-400 mt-1">Entry to Executive</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pending Reviews</p>
            <p class="text-2xl font-bold text-gray-800"><?= $pendingReviewsCount ?></p>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Budget Utilization</p>
            <p class="text-2xl font-bold text-gray-800"><?= $budgetUtilization ?>%</p>
            <p class="text-xs text-gray-400 mt-1"><?= formatSalary($usedBudget) ?> of <?= formatSalary($totalBudget) ?>
            </p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Salary Bands -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Salary Bands by Position</h3>
                <span
                    class="text-xs font-medium bg-white text-gray-600 px-2.5 py-1 rounded-full border border-gray-200">
                    <?= count($salaryBands) ?> positions
                </span>
            </div>

            <div class="p-6">
                <div class="space-y-5">
                    <?php if (!empty($salaryBands)): ?>
                        <?php foreach ($salaryBands as $band):
                            $midpoint = ($band['min_salary'] + $band['max_salary']) / 2;
                            $rangeWidth = $band['max_salary'] - $band['min_salary'];
                            $positionWidth = $rangeWidth > 0 ? (($band['avg_salary'] - $band['min_salary']) / $rangeWidth) * 100 : 50;
                            ?>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <div>
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($band['position']) ?></p>
                                        <p class="text-xs text-gray-400"><?= $band['employee_count'] ?> employees</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-800"><?= formatSalary($band['min_salary']) ?>
                                            - <?= formatSalary($band['max_salary']) ?></p>
                                        <p class="text-xs text-gray-400">Midpoint: <?= formatSalary($midpoint) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <p>No salary band data available</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Salary Band Footer -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total Compensation Budget</span>
                        <span class="font-semibold text-gray-800"><?= formatSalary($totalBudget) ?>/month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Reviews and Adjustments -->
        <div class="space-y-6">
            <!-- Upcoming Compensation Reviews with Filters -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-800">Upcoming Reviews</h3>

                        <!-- Filter Bar -->
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="compensation_status" onchange="applyCompensationFilter()"
                                class="px-2 py-1 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="">All Status</option>
                                <option value="draft" <?= $compensationStatusFilter == 'draft' ? 'selected' : '' ?>>Draft
                                </option>
                                <option value="pending_finance" <?= $compensationStatusFilter == 'pending_finance' ? 'selected' : '' ?>>Pending Finance</option>
                                <option value="approved" <?= $compensationStatusFilter == 'approved' ? 'selected' : '' ?>>
                                    Approved</option>
                                <option value="rejected" <?= $compensationStatusFilter == 'rejected' ? 'selected' : '' ?>>
                                    Rejected</option>
                            </select>

                            <select name="compensation_type" onchange="applyCompensationFilter()"
                                class="px-2 py-1 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="">All Types</option>
                                <option value="annual" <?= $compensationTypeFilter == 'annual' ? 'selected' : '' ?>>Annual
                                </option>
                                <option value="promotion" <?= $compensationTypeFilter == 'promotion' ? 'selected' : '' ?>>
                                    Promotion</option>
                                <option value="merit" <?= $compensationTypeFilter == 'merit' ? 'selected' : '' ?>>Merit
                                </option>
                                <option value="market" <?= $compensationTypeFilter == 'market' ? 'selected' : '' ?>>Market
                                </option>
                            </select>

                            <select name="compensation_department" onchange="applyCompensationFilter()"
                                class="px-2 py-1 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="">All Depts</option>
                                <?php foreach ($compensationDepartments as $dept): ?>
                                    <option value="<?= htmlspecialchars($dept['department']) ?>"
                                        <?= $compensationDepartmentFilter == $dept['department'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['department']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if (!empty($compensationStatusFilter) || !empty($compensationTypeFilter) || !empty($compensationDepartmentFilter)): ?>
                                <a href="?tab=compensation&compensation_page=1"
                                    class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded hover:bg-gray-200">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Results count -->
                    <div class="mt-2 text-xs text-gray-500">
                        Showing <?= count($upcomingReviews) ?> of <?= $totalCompensationReviews ?> reviews
                    </div>
                </div>

                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="space-y-3">
                        <?php if (!empty($upcomingReviews)): ?>
                            <?php foreach ($upcomingReviews as $review):
                                $currentMonthly = $review['hourly_rate'] * 8 * 22;
                                $proposedMonthly = $review['proposed_salary'];
                                $increaseAmount = $proposedMonthly - $currentMonthly;
                                $increasePercent = $currentMonthly > 0 ? round(($increaseAmount / $currentMonthly) * 100) : 0;

                                // Status badge color
                                $statusClass = '';
                                $statusText = ucfirst(str_replace('_', ' ', $review['status']));
                                if ($review['status'] == 'approved') {
                                    $statusClass = 'bg-green-50 text-green-700 border-green-200';
                                } elseif ($review['status'] == 'pending_finance') {
                                    $statusClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                } elseif ($review['status'] == 'rejected') {
                                    $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                } else {
                                    $statusClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                }

                                // Due date color
                                $dueClass = '';
                                $dueText = 'Due ' . date('M j', strtotime($review['effective_date']));
                                if ($review['days_until_effective'] < 0) {
                                    $dueClass = 'bg-red-50 text-red-700 border-red-200';
                                    $dueText = 'Overdue';
                                } elseif ($review['days_until_effective'] <= 7) {
                                    $dueClass = 'bg-orange-50 text-orange-700 border-orange-200';
                                } else {
                                    $dueClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                }
                                ?>
                                <div
                                    class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                                                <?= $review['initials'] ?>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    <?= htmlspecialchars($review['full_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-500"><?= htmlspecialchars($review['position']) ?> •
                                                    <?= $review['hired_date'] ? floor((time() - strtotime($review['hired_date'])) / (365 * 24 * 60 * 60)) : 0 ?>
                                                    yrs
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $dueClass ?>">
                                            <?= $dueText ?>
                                        </span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <p class="text-xs text-gray-400">Current Salary</p>
                                            <p class="font-medium text-gray-800"><?= formatSalary($currentMonthly) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Proposed</p>
                                            <p class="font-medium text-green-600"><?= formatSalary($proposedMonthly) ?>
                                                (+<?= $increasePercent ?>%)
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-2 flex items-center justify-between">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                        <div class="flex gap-2">
                                            <button onclick="viewReview(<?= $review['id'] ?>)" class="btn-primary">
                                                Review Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-calendar-alt text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm">No upcoming reviews found</p>
                                <?php if (!empty($compensationStatusFilter) || !empty($compensationTypeFilter) || !empty($compensationDepartmentFilter)): ?>
                                    <a href="?tab=compensation&compensation_page=1"
                                        class="mt-2 text-blue-600 hover:text-blue-800 text-xs">
                                        Clear filters
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalCompensationPages > 1): ?>
                        <div class="mt-4 flex items-center justify-center gap-2">
                            <?php if ($compensationPage > 1): ?>
                                <a href="?tab=compensation&compensation_page=<?= $compensationPage - 1 ?><?= !empty($compensationStatusFilter) ? '&compensation_status=' . urlencode($compensationStatusFilter) : '' ?><?= !empty($compensationTypeFilter) ? '&compensation_type=' . urlencode($compensationTypeFilter) : '' ?><?= !empty($compensationDepartmentFilter) ? '&compensation_department=' . urlencode($compensationDepartmentFilter) : '' ?>"
                                    class="w-7 h-7 flex items-center justify-center text-xs rounded bg-white border border-gray-200 text-gray-600 hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <span class="text-xs text-gray-600">
                                Page <?= $compensationPage ?> of <?= $totalCompensationPages ?>
                            </span>

                            <?php if ($compensationPage < $totalCompensationPages): ?>
                                <a href="?tab=compensation&compensation_page=<?= $compensationPage + 1 ?><?= !empty($compensationStatusFilter) ? '&compensation_status=' . urlencode($compensationStatusFilter) : '' ?><?= !empty($compensationTypeFilter) ? '&compensation_type=' . urlencode($compensationTypeFilter) : '' ?><?= !empty($compensationDepartmentFilter) ? '&compensation_department=' . urlencode($compensationDepartmentFilter) : '' ?>"
                                    class="w-7 h-7 flex items-center justify-center text-xs rounded bg-white border border-gray-200 text-gray-600 hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Adjustments -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Adjustments</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        <?php if (!empty($recentAdjustments)): ?>
                            <?php
                            $adjustmentCount = count($recentAdjustments);
                            $adjustmentIndex = 0;
                            ?>
                            <?php foreach ($recentAdjustments as $adj):
                                $adjustmentIndex++;
                                $currentMonthly = $adj['current_monthly'];
                                $increaseAmount = $adj['proposed_salary'] - $currentMonthly;
                                ?>
                                <div
                                    class="flex items-center justify-between py-2 <?= $adjustmentIndex < $adjustmentCount ? 'border-b border-gray-100' : '' ?>">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                            <?= $adj['initials'] ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                <?= htmlspecialchars($adj['full_name']) ?>
                                            </p>
                                            <p class="text-xs text-gray-400"><?= htmlspecialchars($adj['position']) ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-green-600">+<?= formatSalary($increaseAmount) ?></p>
                                        <p class="text-xs text-gray-400">
                                            <?= date('M j, Y', strtotime($adj['effective_date'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-500 text-sm">
                                No recent adjustments
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

        document.getElementById('ratePerHour').value = ((proposed / 8) / 22).toFixed(2);

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

    function rescheduleReview(id) {
        alert('Reschedule review #' + id);
    }

    function viewReview(id) {
        alert('View review #' + id);
    }
</script>