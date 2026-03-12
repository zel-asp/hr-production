<div class="tab-content" id="learning-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Learning Management</h2>
            <p class="text-gray-600 mt-1">Track and manage new employees through their onboarding
                journey</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Onboarding Progress Board -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Onboarding Progress Board -->
            <div class="card p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Learning Progress</h3>
                    <div class="relative">
                        <select id="departmentFilter"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 pr-8 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="all">All Departments</option>
                            <?php if (!empty($departments)): ?>
                                <?php foreach ($departments as $dept): ?>
                                    <?php if (!empty($dept['department'])): ?>
                                        <option value="<?= htmlspecialchars($dept['department']) ?>">
                                            <?= htmlspecialchars($dept['department']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Filter Status Bar (will be shown when filter is active) -->
                <div id="filterStatusBar"
                    class="hidden mb-4 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-blue-700">
                        <i class="fas fa-filter mr-1"></i>
                        Filtering by: <span id="activeFilterLabel"></span>
                    </span>
                    <button onclick="resetDepartmentFilter()" class="text-xs text-blue-700 hover:text-blue-900">
                        <i class="fas fa-times mr-1"></i>Clear
                    </button>
                </div>

                <div class="space-y-4" id="onboardingCardsContainer">
                    <?php if (!empty($onboardingTasks)): ?>
                        <?php foreach ($onboardingTasks as $hire):
                            $totalTasks = $hire['total_tasks'] ?: 0;
                            $completedTasks = $hire['completed_tasks'] ?: 0;
                            $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                            // Get tasks for this specific applicant
                            $applicantSpecificTasks = array_filter($applicantTasks, function ($task) use ($hire) {
                                return $task['assigned_to'] == $hire['employee_id'];
                            });

                            // Calculate weeks
                            $startDate = new DateTime($hire['start_date'] ?? $hire['hired_date']);
                            $now = new DateTime();
                            $weeksDiff = $startDate->diff($now)->days / 7;
                            $weekNumber = min(4, max(1, floor($weeksDiff) + 1));
                            ?>
                            <!-- Onboarding for <?= htmlspecialchars($hire['full_name']) ?> -->
                            <div class="onboarding-card p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
                                data-department="<?= htmlspecialchars($hire['department'] ?? '') ?>"
                                data-applicant-id="<?= $hire['employee_id'] ?>">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-semibold">
                                            <?= strtoupper(substr($hire['full_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <h4 class="font-medium"><?= htmlspecialchars($hire['full_name']) ?></h4>
                                            <p class="text-sm text-gray-600">
                                                <?= htmlspecialchars($hire['position']) ?> •
                                                Started
                                                <?= date('M j, Y', strtotime($hire['start_date'] ?? $hire['hired_date'])) ?>
                                                <?php if (!empty($hire['department'])): ?>
                                                    <span class="ml-2 text-xs bg-gray-100 px-2 py-0.5 rounded-full">
                                                        <?= htmlspecialchars($hire['department']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="status-badge <?= $progressPercent >= 75 ? 'bg-green-100 text-green-800' : ($progressPercent >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        Week <?= $weekNumber ?> of 4
                                    </span>
                                </div>

                                <!-- Overall Progress Bar -->
                                <?php if ($totalTasks > 0): ?>
                                    <div class="mb-3">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-medium">Overall Progress</span>
                                            <span><?= $progressPercent ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill <?= $progressPercent >= 75 ? 'green' : ($progressPercent >= 50 ? 'blue' : 'yellow') ?>"
                                                style="width: <?= $progressPercent ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Assigned Tasks Section -->
                                <div class="mt-4 pt-3 border-t border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="text-sm font-semibold text-gray-700">Assigned Tasks</h5>
                                        <button onclick="openModal('addTaskModal<?= $hire['employee_id'] ?>')"
                                            class="text-xs text-primary hover:underline">
                                            <i class="fas fa-plus mr-1"></i>Add Task
                                        </button>
                                    </div>

                                    <!-- Task List -->
                                    <div class="space-y-2">
                                        <?php if (!empty($applicantSpecificTasks)): ?>
                                            <?php
                                            // Sort tasks by status
                                            usort($applicantSpecificTasks, function ($a, $b) {
                                                $order = ['overdue' => 1, 'ongoing' => 2, 'not_started' => 3, 'completed' => 4];
                                                return ($order[$a['task_status_display']] ?? 5) <=> ($order[$b['task_status_display']] ?? 5);
                                            });

                                            foreach ($applicantSpecificTasks as $task):
                                                $dueDate = new DateTime($task['due_date']);
                                                $today = new DateTime();
                                                $today->setTime(0, 0, 0);
                                                $dueDate->setTime(0, 0, 0);

                                                $interval = $today->diff($dueDate);
                                                $daysDiff = ($dueDate < $today) ? -$interval->days : $interval->days;

                                                $taskClass = '';
                                                $dueText = '';
                                                $dueClass = '';
                                                $checked = $task['status'] == 'Completed';

                                                if ($task['status'] == 'Completed') {
                                                    $taskClass = 'bg-green-50';
                                                    $dueText = 'Completed ' . date('M j', strtotime($task['updated_at']));
                                                    $dueClass = 'text-gray-600';
                                                } elseif ($dueDate < $today && $task['status'] != 'Completed') {
                                                    $taskClass = 'bg-red-50';
                                                    $dueText = 'Overdue by ' . abs($daysDiff) . ' day' . (abs($daysDiff) > 1 ? 's' : '');
                                                    $dueClass = 'text-red-600';
                                                } elseif ($dueDate == $today && $task['status'] != 'Completed') {
                                                    $taskClass = 'bg-orange-50';
                                                    $dueText = 'Due Today';
                                                    $dueClass = 'text-orange-600';
                                                } elseif ($daysDiff == 1 && $task['status'] != 'Completed') {
                                                    $taskClass = 'bg-yellow-50';
                                                    $dueText = 'Due Tomorrow';
                                                    $dueClass = 'text-yellow-700';
                                                } elseif ($daysDiff > 1 && $task['status'] != 'Completed') {
                                                    $taskClass = '';
                                                    $dueText = 'Due ' . $dueDate->format('M j');
                                                    $dueClass = 'text-gray-600';
                                                } elseif ($task['status'] == 'Ongoing' && $daysDiff > 1) {
                                                    $taskClass = 'bg-blue-50';
                                                    $dueText = 'Due in ' . $daysDiff . ' days';
                                                    $dueClass = 'text-blue-600';
                                                } elseif ($task['status'] == 'Ongoing' && $daysDiff == 1) {
                                                    $taskClass = 'bg-blue-50';
                                                    $dueText = 'Due Tomorrow';
                                                    $dueClass = 'text-blue-600';
                                                } elseif ($task['status'] == 'Ongoing' && $daysDiff == 0) {
                                                    $taskClass = 'bg-blue-50';
                                                    $dueText = 'Due Today';
                                                    $dueClass = 'text-orange-600';
                                                } elseif ($task['status'] == 'Not Started' && $daysDiff > 0) {
                                                    $taskClass = '';
                                                    $dueText = 'Due ' . $dueDate->format('M j');
                                                    $dueClass = 'text-gray-600';
                                                }
                                                ?>
                                                <!-- Task Item -->
                                                <div class="flex items-center justify-between text-sm <?= $taskClass ?> p-2 rounded-lg"
                                                    id="task<?= $task['id'] ?>">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <input type="checkbox"
                                                            onchange="updateTaskStatus(<?= $task['id'] ?>, this.checked ? 'Completed' : 'Not Started')"
                                                            <?= $checked ? 'checked' : '' ?> class="rounded text-primary" disabled>
                                                        <div class="flex-1">
                                                            <span
                                                                class="<?= $checked ? 'line-through text-gray-500' : 'font-medium' ?>">
                                                                <?= htmlspecialchars($task['task_type']) ?>
                                                                <?php if ($task['priority'] == 'urgent' && !$checked): ?>
                                                                    <span
                                                                        class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">Urgent</span>
                                                                <?php elseif ($task['priority'] == 'high' && !$checked): ?>
                                                                    <span
                                                                        class="ml-2 text-xs bg-orange-100 text-orange-800 px-2 py-0.5 rounded-full">High</span>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right ml-4 flex items-center gap-3">
                                                        <?php if ($dueText): ?>
                                                            <span class="text-xs font-medium <?= $dueClass ?>">
                                                                <i class="fas fa-calendar-alt mr-1"></i><?= $dueText ?>
                                                            </span>
                                                        <?php endif; ?>

                                                        <?php if ($task['priority'] == 'urgent' && !$checked): ?>
                                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                                        <?php endif; ?>

                                                        <!-- Delete Button Form -->
                                                        <form method="POST" action="/delete-task"
                                                            onsubmit="return confirm('Are you sure you want to delete this task?');"
                                                            class="inline">
                                                            <input type="hidden" value="DELETE" name="__method">
                                                            <input type="hidden" name="csrf_token"
                                                                value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                            <button type="submit" name="delete-taskBtn"
                                                                class="text-gray-400 hover:text-red-500 transition-colors duration-200"
                                                                title="Delete task">
                                                                <i class="fas fa-trash-alt text-sm"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-500 italic p-2">
                                                <i class="fas fa-info-circle mr-1"></i>No tasks assigned yet
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Next Task -->
                                    <?php
                                    $nextTask = array_filter($applicantSpecificTasks, function ($task) {
                                        return $task['status'] != 'Completed' && $task['due_date'] >= date('Y-m-d');
                                    });
                                    usort($nextTask, function ($a, $b) {
                                        return strtotime($a['due_date']) - strtotime($b['due_date']);
                                    });
                                    $nextTask = reset($nextTask);
                                    ?>
                                    <?php if ($nextTask): ?>
                                        <div class="flex justify-between items-center text-sm mt-3">
                                            <div>
                                                <span class="text-gray-600">Next Task:</span>
                                                <span class="font-medium ml-1">
                                                    <?= htmlspecialchars($nextTask['task_type']) ?> -
                                                    <?= date('M j', strtotime($nextTask['due_date'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            No active onboarding processes found
                        </div>
                    <?php endif; ?>
                    <?php if ($totalOnboardingPages > 1): ?>
                        <div class="flex justify-center items-center gap-2 mt-6">
                            <?php if ($obPage > 1): ?>
                                <a href="?tab=learning&ob_page=<?= $obPage - 1 ?>"
                                    class="px-3 py-1 border rounded hover:bg-gray-100">
                                    Previous
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalOnboardingPages; $i++): ?>
                                <a href="?tab=learning&ob_page=<?= $i ?>"
                                    class="px-3 py-1 border rounded <?= $i == $obPage ? 'bg-primary text-white' : 'hover:bg-gray-100' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($obPage < $totalOnboardingPages): ?>
                                <a href="?tab=learning&ob_page=<?= $obPage + 1 ?>"
                                    class="px-3 py-1 border rounded hover:bg-gray-100">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Task Assignment Card -->
            <form action="/assignTask" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Quick Task Assignment</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Task To</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                name="assigned_to" required>
                                <option value="">Select new hire</option>
                                <?php if (!empty($hiredEmployees)): ?>
                                    <?php foreach ($hiredEmployees as $employee): ?>
                                        <option value="<?= $employee['id'] ?>">
                                            <?= htmlspecialchars($employee['full_name']) ?> -
                                            <?= htmlspecialchars($employee['position']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                            <select name="task_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                required>
                                <option value="">Select task type</option>
                                <option value="paperwork">Paperwork</option>
                                <option value="training_module">Training Module</option>
                                <option value="equipment_setup">Equipment Setup</option>
                                <option value="mentor_meeting">Mentor Meeting</option>
                                <option value="certification">Certification</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Task Description</label>
                            <input type="text" name="task_description" placeholder="e.g., Complete W-4 Form"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                <input type="date" name="due_date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select name="priority"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                                    <option value="">Select priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full btn-primary py-2">
                            <i class="fas fa-plus-circle mr-2"></i>Assign Task
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>