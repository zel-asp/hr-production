<!-- Generate Account Modal -->
<div id="generateAccountModal" class="modal">
    <div class="modal-content max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Generate Employee Account</h3>
            <button onclick="closeModal('generateAccountModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="generateAccountForm" method="POST" action="/generate-employee-account">
            <!-- Select Employee -->
            <div class="space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg mb-2">
                    <h4 class="font-semibold text-blue-800 mb-3">Select Employee</h4>
                    <div>
                        <label class="block text-sm font-medium mb-2">Choose Employee</label>
                        <select name="applicant_id" id="employeeSelect" class="profile-input w-full p-2 border rounded"
                            onchange="updateEmployeeDetails(this)" required>

                            <option value="">-- Select an employee --</option>

                            <?php foreach ($availableEmployees as $employee): ?>
                                <option value="<?= $employee['id'] ?>"
                                    data-employee-Id="EMP-<?= str_pad($employee['id'], 3, '0', STR_PAD_LEFT) ?>"
                                    data-name="<?= htmlspecialchars($employee['full_name']) ?>"
                                    data-email="<?= htmlspecialchars($employee['email']) ?>"
                                    data-dept="<?= htmlspecialchars($employee['department']) ?>"
                                    data-position="<?= htmlspecialchars($employee['position']) ?>"
                                    data-start="<?= date('M d, Y', strtotime($employee['start_date'] ?? $employee['hired_date'])) ?>">

                                    <?= htmlspecialchars($employee['full_name']) ?> -
                                    <?= htmlspecialchars($employee['position']) ?> (Probationary)
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>

                <!-- Employee Details -->
                <div class="bg-gray-50 p-4 rounded-lg mb-2">
                    <h4 class="font-semibold mb-3">Employee Details</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Employee ID</label>
                            <input type="text" id="empId" name="employee_id"
                                class="profile-input w-full p-2 border rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Full Name</label>
                            <input type="text" id="empName" name="full_name"
                                class="profile-input w-full p-2 border rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Position</label>
                            <input type="text" id="empPosition" name="position"
                                class="profile-input w-full p-2 border rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" id="empEmail" name="email"
                                class="profile-input w-full p-2 border rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Department</label>
                            <input type="text" id="dept" name="department"
                                class="profile-input w-full p-2 border rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Start Date</label>
                            <input type="text" id="empStartDate"
                                class="profile-input w-full p-2 border rounded bg-gray-100" readonly>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                        onclick="closeModal('generateAccountModal')">Cancel</button>
                    <button type="submit" class="btn-primary px-4 py-2">
                        <i class="fas fa-user-plus mr-2"></i>Generate Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Account Generated Success Modal -->
<div id="accountSuccessModal" class="modal">
    <div class="modal-content max-w-md">
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold">Account Generated Successfully!</h3>
            <p class="text-gray-600 mt-1">Employee account has been created</p>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg mb-4">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium">Employee:</span>
                    <span>Grace Lee</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Username:</span>
                    <span>glee</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Temporary Password:</span>
                    <span>Welcome2026!</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Email:</span>
                    <span>grace.lee@company.com</span>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-3">
            <button class="btn-primary px-4 py-2" onclick="closeModal('accountSuccessModal')">
                <i class="fas fa-check mr-2"></i>Done
            </button>
        </div>
    </div>
</div>

<!-- Main Onboarding Content -->
<div class="tab-content" id="onboarding-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">New Hire Onboarding</h2>
            <p class="text-gray-600 mt-1">Generate accounts for probationary employees</p>
        </div>
        <button class="btn-primary" onclick="openModal('generateAccountModal')">
            <i class="fas fa-user-plus mr-2"></i>Generate Employee Account
        </button>
    </div>


    <!-- Generated Accounts -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Generated Accounts</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Employee</th>
                        <th class="text-left py-3">Username</th>
                        <th class="text-left py-3">Generated Date</th>
                        <th class="text-left py-3">Status</th>
                        <th class="text-left py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-3 font-medium">Sarah Chen</td>
                        <td class="py-3">schen</td>
                        <td class="py-3">Feb 10, 2026</td>
                        <td class="py-3"><span class="status-badge bg-green-100 text-green-800">Active</span></td>
                        <td class="py-3">
                            <button class="text-primary hover:text-primary-dark">
                                <i class="fas fa-key mr-1"></i>Reset
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Account Generated Success Modal -->
<div id="accountSuccessModal" class="modal">
    <div class="modal-content max-w-md">
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold">Account Generated Successfully!</h3>
            <p class="text-gray-600 mt-1">Employee account has been created</p>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg mb-4">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium">Employee:</span>
                    <span id="successName">John Doe</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Username:</span>
                    <span id="successUsername">jdoe</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Temporary Password:</span>
                    <span id="successPassword">Welcome2026!</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Email:</span>
                    <span id="successEmail">john.doe@company.com</span>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-3">
            <button class="px-4 py-2 bg-gray-200 rounded-lg" onclick="printCredentials()">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button class="btn-primary px-4 py-2"
                onclick="closeModal('accountSuccessModal'); openModal('generateAccountModal')">
                <i class="fas fa-plus mr-2"></i>Add Another
            </button>
        </div>
    </div>
</div>

<!-- Onboarding Progress Modal -->
<?php foreach ($generatedAccounts as $account): ?>
    <div id="onboardingProgressModal<?= $account['applicant_id'] ?>" class="modal">
        <div class="modal-content max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">
                    Onboarding Progress -
                    <?= htmlspecialchars($account['full_name']) ?>
                </h3>
                <button onclick="closeModal('onboardingProgressModal-<?= $account['applicant_id'] ?>')"
                    class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Account Info -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Username</p>
                        <p class="font-medium">
                            <?= htmlspecialchars($account['username']) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-medium">
                            <?= htmlspecialchars($account['email'] ?? '') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Account Created</p>
                        <p class="font-medium">
                            <?= date('M d, Y', strtotime($account['generated_date'])) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Login</p>
                        <p class="font-medium">
                            <?= date('M d, Y', strtotime($account['last_login'] ?? $account['generated_date'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="border-t pt-4">
                <div class="flex justify-end gap-2">
                    <button class="px-4 py-2 bg-red-100 text-red-700 rounded-lg"
                        onclick="deactivateAccount(<?= $account['applicant_id'] ?>)">
                        <i class="fas fa-ban mr-2"></i>Deactivate
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>