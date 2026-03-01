<!-- View All Tasks Modal -->
<div id="viewAllTasksModal"
    class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-lg w-full mx-4 p-6 shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-solid fa-list-check mr-2 text-primary"></i>All Tasks
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="viewAllTasksModal">
                <i class="fa-solid fa-circle-xmark fa-xl"></i>
            </button>
        </div>
        <p class="text-gray-500 text-sm mb-4">Complete task list</p>

        <div class="space-y-3">
            <?php if (!empty($allTasks)): ?>
                <?php foreach ($allTasks as $task): ?>
                    <!-- Task Item -->
                    <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                        <div class="flex items-center gap-3">
                            <?php
                            // Set icon class based on task type
                            $iconClass = 'fa-solid fa-circle-check';
                            $bgClass = 'bg-[#e1eaf1] text-primary';
                            $displayType = 'todo';

                            switch ($task['task_type']) {
                                case 'training_module':
                                    $iconClass = 'fa-solid fa-clock';
                                    $bgClass = 'bg-[#f0e7fc] text-[#5940a0]';
                                    $displayType = 'training';
                                    break;
                                case 'paperwork':
                                    $iconClass = 'fa-solid fa-file-lines';
                                    $bgClass = 'bg-gray-200 text-gray-800';
                                    $displayType = 'hr';
                                    break;
                                case 'equipment_setup':
                                    $iconClass = 'fa-solid fa-circle-check';
                                    $bgClass = 'bg-[#dbeafe] text-primary-hover';
                                    $displayType = 'review';
                                    break;
                                default:
                                    $displayType = $task['task_type'];
                            }

                            // Set status badge color and display text
                            $statusBadgeClass = 'text-amber-700 bg-amber-50';
                            $statusDisplay = 'pending';

                            if ($task['status'] == 'Ongoing') {
                                $statusBadgeClass = 'text-blue-700 bg-blue-50';
                                $statusDisplay = 'in progress';
                            } elseif ($task['status'] == 'Completed') {
                                $statusBadgeClass = 'text-green-700 bg-green-50';
                                $statusDisplay = 'completed';
                            }
                            ?>

                            <span class="<?= $bgClass ?> text-xs font-medium px-2.5 py-1 rounded-md">
                                <i class="<?= $iconClass ?> mr-1"></i>
                                <?= htmlspecialchars($displayType) ?>
                            </span>

                            <div>
                                <p class="text-sm font-medium">
                                    <?= htmlspecialchars($task['task_description']) ?>
                                </p>
                                <p class="text-xs text-gray-400">
                                    due <?= date('M j', strtotime($task['due_date'])) ?> ·
                                    <?= htmlspecialchars($task['priority']) ?> ·
                                    <?= htmlspecialchars($task['assigned_staff']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="<?= $statusBadgeClass ?> px-3 py-1 text-xs font-medium rounded-md">
                                <?= htmlspecialchars($statusDisplay) ?>
                            </span>

                            <?php if ($task['status'] == 'Not Started'): ?>
                                <!-- Start Button Form - Only for Not Started -->
                                <form method="POST" action="/tasks/start">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="__method" value="PATCH">
                                    <input type="hidden" name="action" value="start">
                                    <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-700 transition">
                                        Start
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($task['status'] == 'Ongoing'): ?>
                                <!-- Done Button Form - Only for Ongoing -->
                                <form method="POST" action="/tasks/complete">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="__method" value="PATCH">
                                    <input type="hidden" name="action" value="complete">
                                    <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                    <button type="submit"
                                        class="bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-700 transition">
                                        Done
                                    </button>
                                </form>
                            <?php elseif ($task['status'] == 'Completed'): ?>
                                <!-- Disabled Done Button - Only for Completed -->
                                <button class="bg-gray-400 text-white px-3 py-1 rounded-md text-xs cursor-not-allowed" disabled>
                                    Done
                                </button>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-circle-check text-4xl mb-3"></i>
                    <p>No tasks assigned yet</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 flex justify-end">
            <button
                class="close-modal bg-white text-primary px-4 py-2 rounded-md text-sm font-medium hover:bg-[#d9e2ed]"
                data-modal="viewAllTasksModal">
                Close
            </button>
        </div>
    </div>
</div>