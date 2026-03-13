<div class="tab-content" id="hmo-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">HMO & Benefits Administration</h2>
            <p class="text-gray-500 text-sm mt-1">Manage employee health insurance and benefits</p>
        </div>
        <div class="flex gap-2">
            <button class="btn-secondary" onclick="openModal('addProviderModal')">
                <i class="fas fa-plus"></i>
                Add Provider
            </button>
            <button class="btn-primary" onclick="openModal('enrollBenefitModal')">
                <i class="fas fa-plus"></i>
                Enroll Employee
            </button>
        </div>
    </div>

    <!-- Benefits Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Enrolled</p>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-bold text-gray-800"><?= $hmoEnrolledCount ?></p>
                <span class="text-sm text-gray-500">/<?= $hmoTotalEmployees ?></span>
            </div>
            <p class="text-xs text-gray-400 mt-1"><?= $hmoCoverageRate ?>% coverage rate</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pending Enrollment</p>
            <p class="text-2xl font-bold text-gray-800"><?= $hmoPendingCount ?></p>
            <p class="text-xs text-gray-400 mt-1">Awaiting requirements</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Claims This Month</p>
            <p class="text-2xl font-bold text-gray-800"><?= $hmoClaimsCount ?></p>
            <p class="text-xs text-gray-400 mt-1"><?= formatHmoCurrency($hmoClaimsTotal) ?> total value</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Premium Due</p>
            <p class="text-2xl font-bold text-gray-800"><?= formatHmoCurrency($hmoPremiumDue) ?></p>
            <p class="text-xs text-gray-400 mt-1">Due Apr 30, 2024</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter by:</span>
                <select name="hmo_provider" onchange="applyHmoFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Providers</option>
                    <?php foreach ($hmoProviders as $provider): ?>
                        <option value="<?= $provider['id'] ?>" <?= $hmoProviderFilter == $provider['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($provider['provider_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="hmo_status" onchange="applyHmoFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Status</option>
                    <option value="active" <?= $hmoStatusFilter == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="expiring" <?= $hmoStatusFilter == 'expiring' ? 'selected' : '' ?>>Expiring Soon</option>
                    <option value="expired" <?= $hmoStatusFilter == 'expired' ? 'selected' : '' ?>>Expired</option>
                </select>
            </div>
            <?php if (!empty($hmoProviderFilter) || !empty($hmoStatusFilter)): ?>
                <a href="?tab=hmo&hmo_page=1" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Coverage Summary -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Coverage Summary</h3>
                <span
                    class="text-xs font-medium bg-white text-gray-600 px-2.5 py-1 rounded-full border border-gray-200">
                    Active Plans
                </span>
            </div>

            <div class="p-6">
                <div class="space-y-3">
                    <?php if (!empty($hmoCoveragePlans)): ?>
                        <?php foreach ($hmoCoveragePlans as $plan): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-shield-alt text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">
                                                <?= htmlspecialchars($plan['provider_name']) ?>
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1">Principal + dependents</p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="text-xs text-gray-400">Coverage:
                                                    <?= formatHmoCurrency($plan['avg_coverage'] ?? 200000) ?></span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-400"><?= $plan['enrolled_count'] ?>
                                                    enrolled</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        Active
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shield-alt text-4xl mb-3 text-gray-300"></i>
                            <p>No active coverage plans</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Coverage Footer -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total Monthly Premium</span>
                        <span class="font-medium text-gray-800"><?= formatHmoCurrency($hmoTotalMonthlyPremium) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <span class="text-gray-500">Company Share</span>
                        <span class="font-medium text-gray-800"><?= formatHmoCurrency($hmoCompanyShare) ?> (70%)</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <span class="text-gray-500">Employee Share</span>
                        <span class="font-medium text-gray-800"><?= formatHmoCurrency($hmoEmployeeShare) ?> (30%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiring Soon & Recent Enrollments -->
        <div class="space-y-6">
            <!-- Expiring Soon Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Expiring Soon</h3>
                    <span
                        class="text-xs font-medium bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-full border border-yellow-200">
                        Next 30 days
                    </span>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        <?php if (!empty($hmoExpiringSoon)): ?>
                            <?php foreach ($hmoExpiringSoon as $expiring): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-yellow-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($expiring['full_name']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?= htmlspecialchars($expiring['provider_name']) ?> ends
                                                <?= date('M j, Y', strtotime($expiring['expiry_date'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-check-circle text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">No expiring benefits</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Enrollments</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        <?php if (!empty($hmoRecentEnrollments)): ?>
                            <?php foreach ($hmoRecentEnrollments as $enrollment): ?>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                        <?= $enrollment['initials'] ?>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">
                                            <?= htmlspecialchars($enrollment['full_name']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Enrolled in <?= htmlspecialchars($enrollment['provider_name']) ?> •
                                            <?= $enrollment['days_ago'] ?> days ago
                                        </p>
                                    </div>
                                    <span
                                        class="text-xs <?= $enrollment['enrollment_status'] == 'Processed' ? 'text-gray-500' : 'text-yellow-600' ?>">
                                        <?= $enrollment['enrollment_status'] ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-user-plus text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">No recent enrollments</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits List Table -->
    <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">All Benefit Enrollments</h3>
            <span class="text-xs font-medium bg-white text-gray-600 px-2.5 py-1 rounded-full border border-gray-200">
                Showing <?= count($hmoBenefitsList) ?> of <?= $hmoTotalBenefits ?>
            </span>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provider</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Benefit Type</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Effective Date</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry
                                Date</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monthly Premium</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($hmoBenefitsList)): ?>
                            <?php foreach ($hmoBenefitsList as $benefit): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                                <?= $benefit['initials'] ?>
                                            </div>
                                            <div>
                                                <span
                                                    class="text-sm font-medium text-gray-800"><?= htmlspecialchars($benefit['full_name']) ?></span>
                                                <p class="text-xs text-gray-400">
                                                    <?= htmlspecialchars($benefit['position'] ?? '') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= htmlspecialchars($benefit['provider_name']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($benefit['benefit_type'] ?? 'HMO') ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= $benefit['formatted_effective'] ?></td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= $benefit['expiry_date'] ? $benefit['formatted_expiry'] : 'No Expiry' ?>
                                    </td>
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        <?= formatHmoCurrency($benefit['monthly_premium'] ?? 0) ?>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $benefit['status_class'] ?>">
                                            <?= $benefit['status_text'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <button onclick="openModal('viewBenefitModal<?= $benefit['id'] ?>')"
                                                class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-2.5 py-1 rounded-lg transition-colors duration-200 flex items-center gap-1">
                                                <i class="fas fa-eye text-xs"></i>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <i class="fas fa-shield-alt text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">No benefit enrollments found</p>
                                    <p class="text-sm">Click "Enroll Employee" to add a new enrollment</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($hmoTotalPages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Showing <span
                            class="font-medium"><?= min(1 + ($hmoPage - 1) * $hmoPerPage, $hmoTotalBenefits) ?>-<?= min($hmoPage * $hmoPerPage, $hmoTotalBenefits) ?></span>
                        of <span class="font-medium"><?= $hmoTotalBenefits ?></span> enrollments
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($hmoPage > 1): ?>
                            <a href="?tab=hmo&hmo_page=<?= $hmoPage - 1 ?>&hmo_provider=<?= urlencode($hmoProviderFilter) ?>&hmo_status=<?= urlencode($hmoStatusFilter) ?>"
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

                        <?php for ($i = 1; $i <= min(5, $hmoTotalPages); $i++): ?>
                            <a href="?tab=hmo&hmo_page=<?= $i ?>&hmo_provider=<?= urlencode($hmoProviderFilter) ?>&hmo_status=<?= urlencode($hmoStatusFilter) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg <?= $i == $hmoPage ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?> transition-colors">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($hmoPage < $hmoTotalPages): ?>
                            <a href="?tab=hmo&hmo_page=<?= $hmoPage + 1 ?>&hmo_provider=<?= urlencode($hmoProviderFilter) ?>&hmo_status=<?= urlencode($hmoStatusFilter) ?>"
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
        </div>
    </div>
</div>

<!-- HMO Enrollment Modals -->
<?php if (!empty($hmoBenefitsList)): ?>
    <?php foreach ($hmoBenefitsList as $benefit): ?>
        <div id="viewBenefitModal<?= $benefit['id'] ?>"
            class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Benefit Enrollment Details</h3>
                        <button onclick="closeModal('viewBenefitModal<?= $benefit['id'] ?>')"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Employee Information -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Employee Information</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-500">Name</p>
                                    <p class="font-medium text-gray-800">
                                        <?= htmlspecialchars($benefit['full_name']) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Position</p>
                                    <p class="font-medium text-gray-800">
                                        <?= htmlspecialchars($benefit['position'] ?? 'N/A') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Benefit Details -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Benefit Details</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Provider</span>
                                    <span class="text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($benefit['provider_name']) ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Benefit Type</span>
                                    <span class="text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($benefit['benefit_type'] ?? 'HMO') ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Coverage Amount</span>
                                    <span class="text-sm font-medium text-gray-800">
                                        <?= formatHmoCurrency($benefit['coverage_amount'] ?? 200000) ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Monthly Premium</span>
                                    <span class="text-sm font-medium text-gray-800">
                                        <?= formatHmoCurrency($benefit['monthly_premium'] ?? 0) ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $benefit['status_class'] ?>">
                                        <?= $benefit['status_text'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-blue-50 rounded-lg p-3">
                                <p class="text-xs text-blue-600 mb-1">Effective Date</p>
                                <p class="text-sm font-medium text-blue-800">
                                    <?= $benefit['formatted_effective'] ?>
                                </p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-3">
                                <p class="text-xs text-orange-600 mb-1">Expiry Date</p>
                                <p class="text-sm font-medium text-orange-800">
                                    <?= $benefit['formatted_expiry'] ?? 'No Expiry' ?>
                                </p>
                            </div>
                        </div>
                        <!-- Actions -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeModal('viewBenefitModal<?= $benefit['id'] ?>')"
                                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


<!-- Add Provider Modal -->
<div id="addProviderModal" class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <form id="addProviderForm" method="POST" action="/addProvider">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Add New Benefit Provider</h3>
                    <button type="button" onclick="closeModal('addProviderModal')"
                        class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Provider Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provider Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="provider_name" required placeholder="e.g., Maxicare, Medicard, etc."
                            class="w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Information</label>
                        <input type="text" name="contact_info" placeholder="Phone, email, or address"
                            class="w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Additional information about the provider"
                            class="w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal('addProviderModal')"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" name="add_provider"
                            class="px-4 py-2 text-sm text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                            <i class="fas fa-save text-xs"></i>
                            Add Provider
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function applyHmoFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'hmo');
        url.searchParams.set('hmo_page', '1');

        const provider = document.querySelector('select[name="hmo_provider"]')?.value;
        const status = document.querySelector('select[name="hmo_status"]')?.value;

        if (provider) url.searchParams.set('hmo_provider', provider);
        else url.searchParams.delete('hmo_provider');

        if (status) url.searchParams.set('hmo_status', status);
        else url.searchParams.delete('hmo_status');

        window.location.href = url.toString();
    }

</script>