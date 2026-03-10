<!-- Generate a unique modal for each employee -->
<?php foreach ($hcmEmployees as $employee): ?>
        <div id="employeeModal<?= $employee['id'] ?>"
            class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50"
            data-employee-id="<?= $employee['id'] ?>" data-applicant-id="<?= $employee['applicant_id'] ?? '' ?>"
            data-employee-number="<?= htmlspecialchars($employee['employee_number'] ?? '') ?>">
            <div class="bg-white rounded-xl max-w-4xl w-full mx-4 shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
                <!-- Modal Header with Admin Badge -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fa-solid fa-user mr-2 text-blue-600"></i>
                            Employee Profile: <?= htmlspecialchars($employee['full_name'] ?? '') ?>
                        </h3>
                        <span
                            class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full border border-purple-200">
                            <i class="fa-solid fa-shield mr-1"></i>Admin View
                        </span>
                    </div>
                    <button onclick="closeModal('employeeModal<?= $employee['id'] ?>')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-circle-xmark fa-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form id="employeeForm<?= $employee['id'] ?>" class="space-y-6" method="POST" action="/update-employee">
                        <input type="hidden" name="__method" value="PATCH">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">

                        <!-- Admin Status Summary Cards with Quick Actions -->
                        <div class="grid grid-cols-5 gap-4 mb-6">
                            <!-- Employee Status -->
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 relative group">
                                <span class="text-blue-600 text-xs font-medium uppercase">Employee Status</span>
                                <p class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($employee['status'] ?? 'Probationary') ?>
                                </p>
                                <button type="button"
                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity text-blue-600 hover:text-blue-800">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            </div>

                            <!-- Onboarding -->
                            <div class="bg-green-50 p-3 rounded-lg border border-green-100 relative group">
                                <span class="text-green-600 text-xs font-medium uppercase">Onboarding</span>
                                <p class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($employee['onboarding_status'] ?? 'In Progress') ?>
                                </p>
                                <button type="button"
                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity text-green-600 hover:text-green-800">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            </div>

                            <!-- Role -->
                            <div class="bg-purple-50 p-3 rounded-lg border border-purple-100 relative group">
                                <span class="text-purple-600 text-xs font-medium uppercase">Role</span>
                                <p class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($employee['role'] ?? 'employee') ?>
                                </p>
                                <button type="button"
                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity text-purple-600 hover:text-purple-800">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            </div>

                            <!-- Evaluation -->
                            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 relative group">
                                <span class="text-yellow-600 text-xs font-medium uppercase">Evaluation</span>
                                <p class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($employee['evaluation_status'] ?? 'Pending') ?>
                                </p>
                                <button type="button"
                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity text-yellow-600 hover:text-yellow-800">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            </div>

                            <!-- Employee ID -->
                            <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                <span class="text-red-600 text-xs font-medium uppercase">Employee ID</span>
                                <p class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($employee['employee_number'] ?? '') ?>
                                </p>
                            </div>

                        </div>

                        <!-- Employee Basic Information Section -->
                        <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fa-solid fa-id-card text-blue-600"></i>
                                Basic Information
                            </h4>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Employee Number</label>
                                <input type="text" name="employee_number"
                                    value="<?= htmlspecialchars($employee['employee_number'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Full Name</label>
                                <input type="text" name="full_name"
                                    value="<?= htmlspecialchars($employee['full_name'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($employee['email'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Phone</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($employee['phone'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Position</label>
                                <input type="text" name="position"
                                    value="<?= htmlspecialchars($employee['position'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Department</label>
                                <select name="department"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Management" <?= ($employee['department'] ?? '') == 'Management' ? 'selected' : '' ?>>Management</option>
                                    <option value="Restaurant" <?= ($employee['department'] ?? '') == 'Restaurant' ? 'selected' : '' ?>>Restaurant</option>
                                    <option value="Hotel" <?= ($employee['department'] ?? '') == 'Hotel' ? 'selected' : '' ?>>Hotel</option>
                                    <option value="HR" <?= ($employee['department'] ?? '') == 'HR' ? 'selected' : '' ?>>HR</option>
                                    <option value="Logistic" <?= ($employee['department'] ?? '') == 'Logistic' ? 'selected' : '' ?>>Logistic</option>
                                    <option value="Finance" <?= ($employee['department'] ?? '') == 'Finance' ? 'selected' : '' ?>>Finance</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Start Date</label>
                                <input type="date" name="start_date"
                                    value="<?= htmlspecialchars($employee['start_date'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Hired Date</label>
                                <input type="date" name="hired_date"
                                    value="<?= htmlspecialchars($employee['hired_date'] ?? '') ?>"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Hourly Rate</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                    <input type="number" name="hourly_rate" step="0.01"
                                        value="<?= htmlspecialchars($employee['hourly_rate'] ?? '0.00') ?>"
                                        class="w-full pl-8 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" read>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Employee Status</label>
                                <select name="status"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Fired" <?= ($employee['status'] ?? '') == 'Fired' ? 'selected' : '' ?>>Fired</option>
                                    <option value="Probationary" <?= ($employee['status'] ?? '') == 'Probationary' ? 'selected' : '' ?>>Probationary</option>
                                    <option value="Regular" <?= ($employee['status'] ?? '') == 'Regular' ? 'selected' : '' ?>>Regular</option>
                                    <option value="Contract" <?= ($employee['status'] ?? '') == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                    <option value="Resigned" <?= ($employee['status'] ?? '') == 'Resigned' ? 'selected' : '' ?>>Resigned</option>
                                    <option value="Terminated" <?= ($employee['status'] ?? '') == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Onboarding Status</label>
                                <select name="onboarding_status"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Onboarding" <?= ($employee['onboarding_status'] ?? '') == 'Onboarding' ? 'selected' : '' ?>>Onboarding</option>
                                    <option value="In Progress" <?= ($employee['onboarding_status'] ?? '') == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Onboarded" <?= ($employee['onboarding_status'] ?? '') == 'Onboarded' ? 'selected' : '' ?>>Onboarded</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Role</label>
                                <select name="role"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="employee" <?= ($employee['role'] ?? '') == 'employee' ? 'selected' : '' ?>>Employee</option>
                                    <option value="mentor" <?= ($employee['role'] ?? '') == 'mentor' ? 'selected' : '' ?>>Mentor</option>
                                    <option value="evaluator" <?= ($employee['role'] ?? '') == 'evaluator' ? 'selected' : '' ?>>Evaluator</option>
                                    <option value="admin" <?= ($employee['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                        </div>
                        </div>

                        <!-- Employee Documents Section -->
                        <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fa-solid fa-file text-purple-600"></i>
                                Employee Documents
                            </h4>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <!-- Resume -->
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-file-pdf text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-700">Resume / CV</p>
                                            <?php if (!empty($employee['resume_path']) || !empty($employee['resume'])): ?>
                                                    <span class="text-xs text-green-600"><i class="fa-solid fa-check-circle mr-1"></i>Uploaded</span>
                                            <?php else: ?>
                                                    <span class="text-xs text-gray-400">Not uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($employee['resume_path']) || !empty($employee['resume'])): ?>
                                            <a href="<?= htmlspecialchars($employee['resume_path'] ?? $employee['resume'] ?? '') ?>" 
                                                target="_blank" 
                                                class="text-xs text-blue-600 hover:text-blue-800 bg-white px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition">
                                                <i class="fa-solid fa-eye mr-1"></i> View
                                            </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- NBI Clearance -->
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-fingerprint text-red-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-700">NBI Clearance</p>
                                            <?php if (!empty($employee['nbi_clearance'])): ?>
                                                    <span class="text-xs text-green-600"><i class="fa-solid fa-check-circle mr-1"></i>Uploaded</span>
                                            <?php else: ?>
                                                    <span class="text-xs text-gray-400">Not uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($employee['nbi_clearance'])): ?>
                                            <a href="<?= htmlspecialchars($employee['nbi_clearance']) ?>" 
                                                target="_blank" 
                                                class="text-xs text-blue-600 hover:text-blue-800 bg-white px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition">
                                                <i class="fa-solid fa-eye mr-1"></i> View
                                            </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Medical Result -->
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-notes-medical text-teal-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-700">Medical Result</p>
                                            <?php if (!empty($employee['medical_result'])): ?>
                                                    <span class="text-xs text-green-600"><i class="fa-solid fa-check-circle mr-1"></i>Uploaded</span>
                                            <?php else: ?>
                                                    <span class="text-xs text-gray-400">Not uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($employee['medical_result'])): ?>
                                            <a href="<?= htmlspecialchars($employee['medical_result']) ?>" 
                                                target="_blank" 
                                                class="text-xs text-blue-600 hover:text-blue-800 bg-white px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition">
                                                <i class="fa-solid fa-eye mr-1"></i> View
                                            </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Birth Certificate -->
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-id-card text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-700">Birth Certificate</p>
                                            <?php if (!empty($employee['birth_certificate'])): ?>
                                                    <span class="text-xs text-green-600"><i class="fa-solid fa-check-circle mr-1"></i>Uploaded</span>
                                            <?php else: ?>
                                                    <span class="text-xs text-gray-400">Not uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($employee['birth_certificate'])): ?>
                                            <a href="<?= htmlspecialchars($employee['birth_certificate']) ?>" 
                                                target="_blank" 
                                                class="text-xs text-blue-600 hover:text-blue-800 bg-white px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition">
                                                <i class="fa-solid fa-eye mr-1"></i> View
                                            </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Applicant Source Information (if available) -->
                        <?php if (!empty($employee['applicant_experience']) || !empty($employee['applicant_education'])): ?>
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <i class="fa-solid fa-file-lines text-green-600"></i>
                                            Application Information
                                        </h4>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Experience</label>
                                            <textarea readonly rows="2" name="experience"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($employee['applicant_experience'] ?? '') ?></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Education</label>
                                            <textarea readonly rows="2" name="education"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($employee['applicant_education'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs text-gray-500 mb-1">Skills</label>
                                            <textarea readonly rows="2" name="skills"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($employee['applicant_skills'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs text-gray-500 mb-1">Cover Note</label>
                                            <textarea readonly rows="3" name="cover_note"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="No cover note provided"><?= htmlspecialchars($employee['applicant_cover_note'] ?? '') ?></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Applied Date</label>
                                            <input type="date" name="applied_date"
                                                value="<?= htmlspecialchars($employee['applicant_created_at'] ?? '') ?>"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                </div>
                        <?php endif; ?>

                        <!-- Account Information (if available) -->
                        <?php if (!empty($employee['username'])): ?>
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <i class="fa-solid fa-laptop text-purple-600"></i>
                                            Account Details
                                        </h4>
                                        <button type="button" class="text-xs text-blue-600 hover:text-blue-800">
                                            <i class="fa-solid fa-key mr-1"></i>Reset Password
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Username</label>
                                            <input type="text" name="username"
                                                value="<?= htmlspecialchars($employee['username'] ?? '') ?>"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Password</label>
                                            <div class="relative">
                                                <input type="password" name="password" value="********"
                                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <button type="button" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Account Status</label>
                                            <select name="account_status"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="Active" <?= ($employee['account_status'] ?? '') == 'Active' ? 'selected' : '' ?>>Active</option>
                                                <option value="Inactive" <?= ($employee['account_status'] ?? '') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                <option value="Suspended" <?= ($employee['account_status'] ?? '') == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                                <option value="Locked" <?= ($employee['account_status'] ?? '') == 'Locked' ? 'selected' : '' ?>>Locked</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Last Login</label>
                                            <input type="text" value="<?= htmlspecialchars($employee['last_login'] ?? 'Never') ?>"
                                                class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Session Token</label>
                                            <input type="text" value="<?= substr($employee['session_token'] ?? '', 0, 10) ?>..."
                                                class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm" readonly>
                                        </div>
                                    </div>
                                </div>
                        <?php endif; ?>

                        <!-- Attendance Summary -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-clock text-amber-600"></i>
                                    Attendance Summary
                                </h4>
                            </div>
                            <div class="bg-amber-50 p-3 rounded-lg border border-amber-200 mb-3">
                                <span class="text-xs text-amber-800">Current Period: <?= htmlspecialchars($cutoffStart ?? '') ?>
                                    - <?= htmlspecialchars($cutoffEnd ?? '') ?></span>
                            </div>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Cutoff Hours</label>
                                    <input type="number" step="0.01" name="cutoff_hours" min="0"
                                        value="<?= htmlspecialchars($employee['cutoff_hours'] ?? '0.00') ?>"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Upcoming Trainings</label>
                                    <input type="number" value="<?= htmlspecialchars($employee['upcoming_trainings'] ?? '0') ?>"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Pending Leaves</label>
                                    <input type="number" value="<?= htmlspecialchars($employee['pending_leaves'] ?? '0') ?>"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Pending Tasks</label>
                                    <input type="number" value="<?= htmlspecialchars($employee['pending_tasks'] ?? '0') ?>"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Notes Section -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-note-sticky text-gray-600"></i>
                                Admin Notes
                            </h4>
                            <textarea rows="3" name="admin_notes"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Add private notes about this employee (visible only to admins)"></textarea>
                        </div>

                        <!-- System Information (Read-only) -->
                        <div class="mb-6 p-3 bg-gray-100 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-600 mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info"></i>
                                System Information
                            </h4>
                            <div class="grid grid-cols-3 gap-2 text-xs text-gray-500">
                                <div>Created:
                                    <?= htmlspecialchars(date('M j, Y H:i', strtotime($employee['created_at'] ?? ''))) ?>
                                </div>
                                <div>Last Updated:
                                    <?= htmlspecialchars(date('M j, Y H:i', strtotime($employee['updated_at'] ?? ''))) ?>
                                </div>
                                <div>Record ID: <?= htmlspecialchars($employee['id'] ?? '') ?></div>
                            </div>
                        </div>

                        <!-- Admin Form Actions -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 sticky bottom-0 bg-white py-3">
                            <div class="flex gap-2">
                                <button type="button" onclick="closeModal('employeeModal<?= $employee['id'] ?>')"
                                    class="px-5 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php endforeach; ?>