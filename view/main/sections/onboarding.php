<!-- Main Onboarding Content -->
<div class="tab-content" id="onboarding-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">New Hire Onboarding</h2>
            <p class="text-gray-500 text-sm mt-1">Generate employee accounts and track onboarding progress</p>
        </div>
        <button class="btn-primary" onclick="openModal('generateAccountModal')">
            <i class="fas fa-user-plus"></i>
            Generate Employee Account
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Accounts Generated</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['totalAccounts'] ?></p>
            <p class="text-xs text-gray-400 mt-1">Total employees</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pending Onboarding</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['pending'] ?></p>
            <p class="text-xs text-gray-400 mt-1">Courses not started</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">In Progress</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['inProgress'] ?></p>
            <p class="text-xs text-gray-400 mt-1">Completing courses</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Onboarded</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['onboarded'] ?></p>
            <p class="text-xs text-gray-400 mt-1">All courses completed</p>
        </div>
    </div>

    <!-- New Hires / Probationary Employees -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">New Hires / Probationary Employees</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee ID</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Position</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Hired
                                Date</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Onboarding Status</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($paginatedNewHires)): ?>
                            <?php foreach ($paginatedNewHires as $employee): ?>
                                <?php
                                $onboardingStatus = $employee['onboarding_status'] ?? 'Onboarding';
                                $badgeClass = match ($onboardingStatus) {
                                    'Onboarded' => 'bg-green-50 text-green-700 border border-green-200',
                                    'In Progress' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    default => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                };
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($employee['employee_number']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-800"><?= htmlspecialchars($employee['full_name']) ?></td>
                                    <td class="py-3 text-sm text-gray-600"><?= htmlspecialchars($employee['position']) ?></td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= date('M d, Y', strtotime($employee['hired_date'] ?? $employee['start_date'])) ?>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClass ?>">
                                            <?= $onboardingStatus ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php
                                        $completeness = getEmployeeCompleteness($employee);
                                        ?>
                                        <?php if ($completeness['is_complete']): ?>
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                                Complete
                                            </span>
                                        <?php else: ?>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 cursor-help"
                                                    title="<?= htmlspecialchars(implode(', ', $completeness['missing_items'])) ?>">
                                                    <i class="fas fa-exclamation-circle text-red-600 text-xs"></i>
                                                    <?= $completeness['missing_count'] ?> Missing
                                                </span>

                                                <!-- Notify Button -->
                                                <form action="/send-requirement-notification" method="POST" class="inline">
                                                    <input type="hidden" name="csrf_token"
                                                        value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                                                    <input type="hidden" name="missing_count"
                                                        value="<?= $completeness['missing_count'] ?>">
                                                    <input type="hidden" name="missing_items"
                                                        value="<?= htmlspecialchars(implode(', ', $completeness['missing_items'])) ?>">
                                                    <button type="submit"
                                                        class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg border border-blue-200 transition flex items-center gap-1"
                                                        onclick="return confirm('Send notification to employee about missing requirements?')">
                                                        <i class="fas fa-bell"></i>
                                                        Notify
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-users text-gray-300 text-2xl"></i>
                                        <p>No new hires found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalNewHirePages > 1): ?>
                    <div class="flex justify-end items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mr-2">Page <?= $nhPage ?> of <?= $totalNewHirePages ?></p>
                        <?php for ($i = 1; $i <= $totalNewHirePages; $i++): ?>
                            <a href="?tab=onboarding&nh_page=<?= $i ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors duration-200
                                <?= $i === $nhPage ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Accounts Generated -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" id="recentAccount">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">Recent Employee Accounts</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Username</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Position</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Created</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($generatedAccounts)): ?>
                            <?php foreach ($generatedAccounts as $account): ?>
                                <?php
                                $status = $account['account_status'] ?? 'Active';
                                $badgeClass = $status === 'Active'
                                    ? 'bg-green-50 text-green-700 border border-green-200'
                                    : ($status === 'Inactive' ? 'bg-gray-50 text-gray-700 border border-gray-200' : 'bg-red-50 text-red-700 border border-red-200');
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($account['full_name']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($account['username']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($account['position']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= date('M d, Y', strtotime($account['generated_date'])) ?>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClass ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <button
                                            class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1"
                                            onclick="openModal('onboardingProgressModal<?= $account['applicant_id'] ?>')">
                                            <i class="fas fa-eye text-xs"></i>
                                            View account
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-user-slash text-gray-300 text-2xl"></i>
                                        <p>No employee accounts found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-end items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mr-2">Page <?= $page ?> of <?= $totalPages ?></p>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?tab=onboarding&page=<?= $i ?>#recentAccount"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors duration-200
                                <?= $i == $page ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>