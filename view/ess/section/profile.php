<div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">My Profile</h2>

    <?php if ($employeeInfo): ?>
        <!-- Profile Header with primary color accent -->
        <div class="flex items-start gap-4 pb-6 mb-6 border-b border-gray-200">
            <div
                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center text-primary text-2xl font-semibold">
                <?= strtoupper(substr($employeeInfo['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($employeeInfo['full_name'] ?? 'N/A') ?>
                </h3>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($employeeInfo['position'] ?? 'N/A') ?></p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-medium rounded-md">
                        <?= htmlspecialchars($employeeInfo['employee_number'] ?? 'N/A') ?>
                    </span>
                    <span class="text-xs text-gray-400">
                        <i class="fa-regular fa-calendar mr-1"></i>
                        Since
                        <?= $employeeInfo['start_date'] ? date('M Y', strtotime($employeeInfo['start_date'])) : 'N/A' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Update Form -->
        <form method="POST" action="/profile/update" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="__method" value="PATCH">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Personal Information -->
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-700 border-b border-gray-200 pb-2">Personal Information</h4>

                    <div>
                        <label for="full_name" class="block text-xs font-medium text-gray-500 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                            value="<?= htmlspecialchars($employeeInfo['full_name'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-500 mb-1">Email Address</label>
                        <input type="email" id="email" name="email"
                            value="<?= htmlspecialchars($employeeInfo['email'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium text-gray-500 mb-1">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?= htmlspecialchars($employeeInfo['phone'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <!-- Right Column - Password Change -->
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-700 border-b border-gray-200 pb-2">Change Password</h4>

                    <div>
                        <label for="current_password" class="block text-xs font-medium text-gray-500 mb-1">Current
                            Password</label>
                        <input type="password" id="current_password" name="current_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="new_password" class="block text-xs font-medium text-gray-500 mb-1">New Password</label>
                        <input type="password" id="new_password" name="new_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-xs font-medium text-gray-500 mb-1">Confirm
                            Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>

                    <p class="text-xs text-gray-400 mt-2">Leave blank to keep current password</p>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                    <i class="fa-solid fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>

        <!-- Employment Details Grid -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Employment Details</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs text-gray-500 block">Department</span>
                    <span
                        class="text-sm font-medium text-gray-900"><?= htmlspecialchars($employeeInfo['department'] ?? 'N/A') ?></span>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs text-gray-500 block">Employment Type</span>
                    <span
                        class="text-sm font-medium text-gray-900"><?= htmlspecialchars($employeeInfo['status'] ?? 'N/A') ?></span>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs text-gray-500 block">Manager</span>
                    <span class="text-sm font-medium text-gray-900">Sarah V.</span>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs text-gray-500 block">Location</span>
                    <span class="text-sm font-medium text-gray-900">Main Office</span>
                </div>
            </div>
        </div>

        <!-- Status Badges with primary color -->
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Current Status</h4>
            <div class="flex flex-wrap gap-2">
                <!-- Onboarding Status -->
                <span class="px-3 py-1.5 text-xs font-medium rounded-md border 
                    <?php
                    $status = $employeeInfo['onboarding_status'] ?? 'Onboarding';
                    if ($status == 'Onboarded') {
                        echo 'border-green-200 bg-green-50 text-green-700';
                    } elseif ($status == 'In Progress') {
                        echo 'border-blue-200 bg-blue-50 text-blue-700';
                    } else {
                        echo 'border-primary/20 bg-primary/5 text-primary';
                    }
                    ?>">
                    <i class="fas fa-circle-check mr-1"></i>
                    Onboarding: <?= htmlspecialchars($status) ?>
                </span>

                <!-- Evaluation Status -->
                <span class="px-3 py-1.5 text-xs font-medium rounded-md border
                    <?php
                    $evalStatus = $employeeInfo['evaluation_status'] ?? 'Pending';
                    if ($evalStatus == 'Completed') {
                        echo 'border-green-200 bg-green-50 text-green-700';
                    } else {
                        echo 'border-primary/20 bg-primary/5 text-primary';
                    }
                    ?>">
                    <i class="fa-solid fa-chart-line mr-1"></i>
                    Evaluation: <?= htmlspecialchars($evalStatus) ?>
                </span>

                <!-- Account Status -->
                <?php if ($employeeAccount): ?>
                    <span class="px-3 py-1.5 text-xs font-medium rounded-md border
                        <?= ($employeeAccount['account_status'] ?? '') == 'Active'
                            ? 'border-green-200 bg-green-50 text-green-700'
                            : 'border-red-200 bg-red-50 text-red-700' ?>">
                        <i class="fa-solid fa-shield mr-1"></i>
                        Account: <?= htmlspecialchars($employeeAccount['account_status'] ?? 'N/A') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Two Column Grid for Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Application History -->
            <?php if (!empty($employeeInfo['applicant_name'])): ?>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Application History</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Applied Position</span>
                            <span
                                class="font-medium text-gray-900"><?= htmlspecialchars($employeeInfo['applied_position'] ?? 'N/A') ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Hired Date</span>
                            <span class="font-medium text-gray-900">
                                <?= $employeeInfo['hired_date'] ? date('M d, Y', strtotime($employeeInfo['hired_date'])) : 'N/A' ?>
                            </span>
                        </div>
                        <?php if (!empty($employeeInfo['skills'])): ?>
                            <div class="text-sm">
                                <span class="text-gray-600 block mb-1">Skills</span>
                                <span class="font-medium text-gray-900"><?= htmlspecialchars($employeeInfo['skills']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Account Activity -->
            <?php if ($employeeAccount): ?>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Account Activity</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Username</span>
                            <span
                                class="font-medium text-gray-900"><?= htmlspecialchars($employeeAccount['username'] ?? 'N/A') ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Last Login</span>
                            <span class="font-medium text-gray-900">
                                <?php
                                if ($employeeAccount['last_login']) {
                                    $timestamp = strtotime($employeeAccount['last_login']);
                                    echo date('M d, Y · g:ia', $timestamp);
                                } else {
                                    echo 'Never';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Member Since</span>
                            <span class="font-medium text-gray-900">
                                <?= date('M d, Y', strtotime($employeeAccount['generated_date'] ?? 'now')) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Benefits Section -->
        <?php if (!empty($employeeBenefits)): ?>
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Benefits</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($employeeBenefits as $benefit): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span
                                        class="text-sm font-medium text-gray-900"><?= htmlspecialchars($benefit['benefit_type'] ?? 'N/A') ?></span>
                                    <p class="text-xs text-gray-500 mt-0.5">Provider:
                                        <?= htmlspecialchars($benefit['provider_name'] ?? 'N/A') ?>
                                    </p>
                                </div>
                                <?php if (!empty($benefit['coverage_amount'])): ?>
                                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                                        ₱<?= number_format($benefit['coverage_amount'], 0) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($benefit['effective_date'])): ?>
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="fa-regular fa-calendar mr-1"></i>
                                    <?= date('M d, Y', strtotime($benefit['effective_date'])) ?>
                                    <?php if (!empty($benefit['expiry_date'])): ?>
                                        - <?= date('M d, Y', strtotime($benefit['expiry_date'])) ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Error State -->
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-exclamation text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-600 font-medium">Employee information not found</p>
            <p class="text-sm text-gray-400 mt-1">Please try again later</p>
        </div>
    <?php endif; ?>
</div>