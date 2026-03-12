<div class="tab-content" id="hcm-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Core Human Capital Management</h2>
            <p class="text-gray-500 text-sm mt-1">Central employee records and organizational data</p>
        </div>

        <!-- Export Button in Form -->
        <form action="/export/employees-csv" method="GET" class="flex justify-end items-center">
            <input type="hidden" name="search" value="<?= htmlspecialchars($hcmSearchTerm) ?>">
            <input type="hidden" name="status" value="<?= htmlspecialchars($hcmStatusFilter) ?>">
            <input type="hidden" name="department" value="<?= htmlspecialchars($hcmDepartmentFilter) ?>">
            <input type="hidden" name="role" value="<?= htmlspecialchars($hcmRoleFilter) ?>">
            <button type="submit"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                Export to Excel
            </button>
        </form>
    </div>

    <!-- HCM Lifecycle Stats -->
    <div class="grid grid-cols-4 sm:grid-cols-3 lg:grid-cols-3 gap-3 mb-6">
        <!-- Attract & Recruit -->
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass text-purple-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-purple-600 uppercase tracking-wider">Attract & Recruit</span>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($jobPostings ? count($jobPostings) : 0) ?></p>
            <p class="text-xs text-gray-500 mt-1">Open positions</p>
            <div class="mt-2 text-xs text-gray-400">
                <span
                    class="text-green-600 font-medium"><?= number_format($recentApplicants ? count($recentApplicants) : 0) ?></span>
                new applicants
            </div>
        </div>

        <!-- Hire & Onboard -->
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-blue-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-blue-600 uppercase tracking-wider">Hire & Onboard</span>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalHired']) ?></p>
            <p class="text-xs text-gray-500 mt-1">Total hired</p>
            <div class="mt-2 text-xs text-gray-400">
                <span class="text-amber-600 font-medium"><?= number_format($totalPending['count'] ?? 0) ?></span>
                onboarding
            </div>
        </div>

        <!-- Train & Develop -->
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-green-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-green-600 uppercase tracking-wider">Train & Develop</span>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($trainingStats['active_trainings'] ?? 0) ?>
            </p>
            <p class="text-xs text-gray-500 mt-1">Active trainings</p>
            <div class="mt-2 text-xs text-gray-400">
                <span class="text-green-600 font-medium"><?= number_format($analyticsTrainingsCompleted ?? 0) ?></span>
                completed
            </div>
        </div>

        <!-- Manage & Evaluate -->
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple text-yellow-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-yellow-600 uppercase tracking-wider">Manage & Evaluate</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">
                <?= number_format($recentEvaluations ? count($recentEvaluations) : 0) ?>
            </p>
            <p class="text-xs text-gray-500 mt-1">Recent evaluations</p>
            <div class="mt-2 text-xs text-gray-400">
                <span class="text-yellow-600 font-medium"><?= number_format($pendingCount['count'] ?? 0) ?></span>
                pending
            </div>
        </div>

        <!-- Compensate & Recognize -->
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-red-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-red-600 uppercase tracking-wider">Compensate & Recognize</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">
                <?= number_format($recentRecognitions ? count($recentRecognitions) : 0) ?>
            </p>
            <p class="text-xs text-gray-500 mt-1">Recent recognitions</p>
            <div class="mt-2 text-xs text-gray-400">
                <span class="text-red-600 font-medium">₱<?= number_format($payrollTotalNet ?? 0, 0) ?></span> payroll
            </div>
        </div>

    </div>

    <!-- Employee Stats -->
    <div class="grid grid-cols-4 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Employees</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($totalEmployees) ?></p>
            <p class="text-xs text-gray-400 mt-1">Full organization</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Active</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($activeEmployees) ?></p>
            <p class="text-xs text-gray-400 mt-1"><?= $activePercentage ?>% of workforce</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">On Leave</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($onLeaveCount) ?></p>
            <p class="text-xs text-gray-400 mt-1">Currently absent</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Probationary</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($probationaryCount) ?></p>
            <p class="text-xs text-gray-400 mt-1">Under review</p>
        </div>
    </div>

    <!-- Employee Directory -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800" id="employeeList">Employee Directory</h3>

            <!-- Filter Bar -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Search -->
                <form method="GET" class="relative w-full sm:w-64">
                    <input type="hidden" name="tab" value="hcm">
                    <input type="text" name="hcm_search" placeholder="Search employees..."
                        value="<?= htmlspecialchars($hcmSearchTerm) ?>"
                        class="w-full pl-10 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <?php if (!empty($hcmSearchTerm)): ?>
                        <a href="?tab=hcm&hcm_page=1#employeeList"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Status Filter -->
                <select name="hcm_status" onchange="applyHcmFilter()"
                    class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Statuses</option>
                    <?php foreach ($hcmStatuses as $status): ?>
                        <option value="<?= htmlspecialchars($status['status']) ?>" <?= $hcmStatusFilter == $status['status'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status['status']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Department Filter -->
                <select name="hcm_department" onchange="applyHcmFilter()"
                    class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Departments</option>
                    <?php foreach ($hcmDepartments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept['department']) ?>"
                            <?= $hcmDepartmentFilter == $dept['department'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['department']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Role Filter -->
                <select name="hcm_role" onchange="applyHcmFilter()"
                    class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">All Roles</option>
                    <?php foreach ($hcmRoles as $role): ?>
                        <option value="<?= htmlspecialchars($role['role']) ?>" <?= $hcmRoleFilter == $role['role'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($role['role'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Clear Filters Button (shown only when filters are active) -->
                <?php if (!empty($hcmStatusFilter) || !empty($hcmDepartmentFilter) || !empty($hcmRoleFilter) || !empty($hcmSearchTerm)): ?>
                    <a href="?tab=hcm&hcm_page=1#employeeList"
                        class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-1">
                        <i class="fas fa-times-circle"></i>
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full" id="employeeTable">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee ID</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Position</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Hire
                                Date</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($hcmEmployees)): ?>
                            <?php foreach ($hcmEmployees as $employee):
                                $initials = getInitials($employee['full_name']);

                                // Determine status badge color
                                $statusClass = '';
                                $statusText = $employee['status'] ?? 'New';
                                if ($statusText == 'Regular' || $statusText == 'Active') {
                                    $statusClass = 'bg-green-50 text-green-700 border-green-200';
                                } elseif ($statusText == 'Probationary') {
                                    $statusClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                } elseif ($statusText == 'Contract') {
                                    $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                } elseif ($statusText == 'Resigned' || $statusText == 'Terminated') {
                                    $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                } elseif ($statusText == 'Fired' || $statusText == 'Fired') {
                                    $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                } else {
                                    $statusClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                }
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 text-sm font-mono text-gray-500">
                                        <?= htmlspecialchars($employee['employee_number'] ?? '') ?>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                                <?= $initials ?>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-800"><?= htmlspecialchars($employee['full_name'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= htmlspecialchars($employee['position'] ?? '') ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($employee['department'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= $employee['hired_date'] ? date('M j, Y', strtotime($employee['hired_date'])) : 'N/A' ?>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <button onclick="openModal('employeeModal<?= $employee['id'] ?>')"
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
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-5xl mb-3 text-gray-300"></i>
                                        <p class="text-lg font-medium">No employees found</p>
                                        <p class="text-sm">
                                            <?= !empty($hcmSearchTerm) ? 'No results for "' . htmlspecialchars($hcmSearchTerm) . '"' : 'No employee records available' ?>
                                        </p>
                                        <?php if (!empty($hcmStatusFilter) || !empty($hcmDepartmentFilter) || !empty($hcmRoleFilter)): ?>
                                            <a href="?tab=hcm&hcm_page=1#employeeList"
                                                class="mt-3 text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-times mr-1"></i>Clear all filters
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Pagination -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-4">
                <p class="text-sm text-gray-500">
                    Showing <span
                        class="font-medium"><?= $totalHCMEmployees > 0 ? min(1 + ($hcmPage - 1) * $hcmPerPage, $totalHCMEmployees) : 0 ?>-<?= min($hcmPage * $hcmPerPage, $totalHCMEmployees) ?></span>
                    of <span class="font-medium"><?= number_format($totalHCMEmployees) ?></span> employees
                </p>

                <?php if ($totalHCMPages > 1): ?>
                    <div class="flex items-center justify-end gap-2">
                        <!-- Previous button -->
                        <?php if ($hcmPage > 1): ?>
                            <a href="?tab=hcm&hcm_page=<?= $hcmPage - 1 ?><?= !empty($hcmSearchTerm) ? '&hcm_search=' . urlencode($hcmSearchTerm) : '' ?><?= !empty($hcmStatusFilter) ? '&hcm_status=' . urlencode($hcmStatusFilter) : '' ?><?= !empty($hcmDepartmentFilter) ? '&hcm_department=' . urlencode($hcmDepartmentFilter) : '' ?><?= !empty($hcmRoleFilter) ? '&hcm_role=' . urlencode($hcmRoleFilter) : '' ?>#employeeList"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <?php
                        $startPage = max(1, $hcmPage - 2);
                        $endPage = min($totalHCMPages, $hcmPage + 2);
                        $queryParams = [];
                        if (!empty($hcmSearchTerm))
                            $queryParams[] = 'hcm_search=' . urlencode($hcmSearchTerm);
                        if (!empty($hcmStatusFilter))
                            $queryParams[] = 'hcm_status=' . urlencode($hcmStatusFilter);
                        if (!empty($hcmDepartmentFilter))
                            $queryParams[] = 'hcm_department=' . urlencode($hcmDepartmentFilter);
                        if (!empty($hcmRoleFilter))
                            $queryParams[] = 'hcm_role=' . urlencode($hcmRoleFilter);
                        $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';

                        if ($startPage > 1) {
                            echo '<a href="?tab=hcm&hcm_page=1' . $queryString . '#employeeList" 
                               class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">1</a>';
                            if ($startPage > 2) {
                                echo '<span class="text-gray-400">...</span>';
                            }
                        }

                        for ($i = $startPage; $i <= $endPage; $i++) {
                            if ($i == $hcmPage) {
                                echo '<button class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-primary text-white">' . $i . '</button>';
                            } else {
                                echo '<a href="?tab=hcm&hcm_page=' . $i . $queryString . '#employeeList" 
                                   class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">' . $i . '</a>';
                            }
                        }

                        if ($endPage < $totalHCMPages) {
                            if ($endPage < $totalHCMPages - 1) {
                                echo '<span class="text-gray-400">...</span>';
                            }
                            echo '<a href="?tab=hcm&hcm_page=' . $totalHCMPages . $queryString . '" 
                               class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">' . $totalHCMPages . '</a>';
                        }
                        ?>

                        <!-- Next button -->
                        <?php if ($hcmPage < $totalHCMPages): ?>
                            <a href="?tab=hcm&hcm_page=<?= $hcmPage + 1 ?><?= !empty($hcmSearchTerm) ? '&hcm_search=' . urlencode($hcmSearchTerm) : '' ?><?= !empty($hcmStatusFilter) ? '&hcm_status=' . urlencode($hcmStatusFilter) : '' ?><?= !empty($hcmDepartmentFilter) ? '&hcm_department=' . urlencode($hcmDepartmentFilter) : '' ?><?= !empty($hcmRoleFilter) ? '&hcm_role=' . urlencode($hcmRoleFilter) : '' ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors duration-200">
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Department Summary -->
    <div class="mt-6 grid grid-cols-4 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php if (!empty($departmentStats)): ?>
            <?php foreach ($departmentStats as $dept):
                $staff = $dept['total'] - $dept['managers'];
                $icon = '';
                $deptName = strtolower($dept['department']);
                if (strpos($deptName, 'restaurant') !== false || strpos($deptName, 'server') !== false) {
                    $icon = 'fa-utensils';
                } elseif (strpos($deptName, 'kitchen') !== false || strpos($deptName, 'cook') !== false || strpos($deptName, 'chef') !== false) {
                    $icon = 'fa-fire';
                } elseif (strpos($deptName, 'house') !== false || strpos($deptName, 'clean') !== false) {
                    $icon = 'fa-broom';
                } elseif (strpos($deptName, 'hr') !== false || strpos($deptName, 'finance') !== false || strpos($deptName, 'marketing') !== false) {
                    $icon = 'fa-building';
                } elseif (strpos($deptName, 'bar') !== false) {
                    $icon = 'fa-wine-glass-alt';
                } elseif (strpos($deptName, 'front') !== false || strpos($deptName, 'desk') !== false) {
                    $icon = 'fa-bell-concierge';
                } else {
                    $icon = 'fa-users';
                }
                ?>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">
                                <?= htmlspecialchars($dept['department']) ?>
                            </p>
                            <p class="text-lg font-semibold text-gray-800"><?= number_format($dept['total']) ?></p>
                        </div>
                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas <?= $icon ?> text-gray-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">
                        <?= number_format($dept['managers']) ?> manager<?= $dept['managers'] != 1 ? 's' : '' ?> •
                        <?= number_format($staff) ?> staff
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-4 text-center py-8 text-gray-500">
                <p>No department data available</p>
            </div>
        <?php endif; ?>
    </div>
</div>