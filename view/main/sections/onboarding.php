<!-- Main Onboarding Content -->
<div class="tab-content" id="onboarding-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">New Hire Onboarding</h2>
            <p class="text-gray-600 mt-1">Generate employee accounts and track onboarding progress</p>
        </div>
        <button class="btn-primary" onclick="openModal('generateAccountModal')">
            <i class="fas fa-user-plus mr-2"></i>Generate Employee Account
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-sm text-gray-600">Accounts Generated</p>
            <p class="text-2xl font-bold text-primary">
                <?= $stats['totalAccounts'] ?>
            </p>
            <p class="text-xs text-gray-500">Total employees</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Pending Onboarding</p>
            <p class="text-2xl font-bold text-yellow-600">
                <?= $stats['pending'] ?>
            </p>
            <p class="text-xs text-gray-500">Courses not started</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">In Progress</p>
            <p class="text-2xl font-bold text-blue-600">
                <?= $stats['inProgress'] ?>
            </p>
            <p class="text-xs text-gray-500">Completing courses</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Onboarded</p>
            <p class="text-2xl font-bold text-green-600">
                <?= $stats['onboarded'] ?>
            </p>
            <p class="text-xs text-gray-500">All courses completed</p>
        </div>
    </div>

    <!-- New Hires / Probationary Employees -->
    <div class="card p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">New Hires / Probationary Employees</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Employee ID</th>
                        <th class="text-left py-3">Name</th>
                        <th class="text-left py-3">Position</th>
                        <th class="text-left py-3">Hired Date</th>
                        <th class="text-left py-3">Onboarding Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paginatedNewHires)): ?>
                        <?php foreach ($paginatedNewHires as $employee): ?>
                            <?php
                            $onboardingStatus = $employee['onboarding_status'] ?? 'Onboarding';

                            // Set badge color based on status
                            $badgeClass = match ($onboardingStatus) {
                                'Onboarded' => 'bg-green-100 text-green-800',
                                'In Progress' => 'bg-blue-100 text-blue-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                            ?>
                            <tr class="border-b border-gray-100">
                                <td class="py-3 font-medium"><?= htmlspecialchars($employee['employee_number']) ?></td>
                                <td class="py-3 font-medium"><?= htmlspecialchars($employee['full_name']) ?></td>
                                <td class="py-3"><?= htmlspecialchars($employee['position']) ?></td>
                                <td class="py-3">
                                    <?= date('M d, Y', strtotime($employee['hired_date'] ?? $employee['start_date'])) ?>
                                </td>
                                <td class="py-3">
                                    <span class="status-badge <?= $badgeClass ?>"><?= $onboardingStatus ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">No new hires found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if ($totalNewHirePages > 1): ?>
                <div class="flex justify-end mt-4 space-x-2">
                    <?php for ($i = 1; $i <= $totalNewHirePages; $i++): ?>
                        <a href="?tab=onboarding&nh_page=<?= $i ?>"
                            class="px-3 py-1 border rounded <?= $i === $nhPage ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Accounts Generated -->
    <div class="card p-6 mb-6" id="recentAccount">
        <h3 class="text-lg font-semibold mb-4">Recent Employee Accounts</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Employee</th>
                        <th class="text-left py-3">Username</th>
                        <th class="text-left py-3">Position</th>
                        <th class="text-left py-3">Account Created</th>
                        <th class="text-left py-3">Status</th>
                        <th class="text-left py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($generatedAccounts)): ?>
                        <?php foreach ($generatedAccounts as $account): ?>
                            <tr class="border-b border-gray-100">
                                <td class="py-3 font-medium">
                                    <?= htmlspecialchars($account['full_name']) ?>
                                </td>

                                <td class="py-3">
                                    <?= htmlspecialchars($account['username']) ?>
                                </td>

                                <td class="py-3">
                                    <?= htmlspecialchars($account['position']) ?>
                                </td>

                                <td class="py-3">
                                    <?= date('M d, Y', strtotime($account['generated_date'])) ?>
                                </td>

                                <td class="py-3">
                                    <?php
                                    $status = $account['account_status'] ?? 'Active';
                                    $badgeClass = $status === 'Active'
                                        ? 'bg-green-100 text-green-800'
                                        : ($status === 'Inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800');
                                    ?>
                                    <span class="status-badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>


                                <td class="py-3">
                                    <button class="text-primary hover:text-primary-dark"
                                        onclick="openModal('onboardingProgressModal<?= $account['applicant_id'] ?>')">
                                        <i class="fas fa-eye mr-1"></i>View Progress
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                No employee accounts found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-end mt-4 space-x-2">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?tab=onboarding&page=<?= $i ?>#recentAccount"
                            class="px-3 py-1 border rounded <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>