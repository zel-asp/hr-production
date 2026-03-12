<div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">My Tasks</h2>

    <!-- Summary Stats (optional but nice to have) -->
    <?php if (!empty($allTasks)): ?>
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="bg-amber-50 rounded-lg p-2 text-center">
                <p class="text-lg font-bold text-amber-600">
                    <?= count(array_filter($allTasks, function ($task) {
                        return $task['status'] == 'Not Started';
                    })) ?>
                </p>
                <p class="text-xs text-gray-500">Not Started</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-2 text-center">
                <p class="text-lg font-bold text-blue-600">
                    <?= count(array_filter($allTasks, function ($task) {
                        return $task['status'] == 'Ongoing';
                    })) ?>
                </p>
                <p class="text-xs text-gray-500">In Progress</p>
            </div>
            <div class="bg-green-50 rounded-lg p-2 text-center">
                <p class="text-lg font-bold text-green-600">
                    <?= count(array_filter($allTasks, function ($task) {
                        return $task['status'] == 'Completed';
                    })) ?>
                </p>
                <p class="text-xs text-gray-500">Completed</p>
            </div>
        </div>
    <?php endif; ?>

    <p class="text-gray-500 text-sm mb-4">Complete task list (<?= count($allTasks ?? []) ?>)</p>

    <div class="space-y-3">
        <?php if (!empty($allTasks)): ?>
            <?php foreach ($allTasks as $task): ?>
                <!-- Task Item -->
                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md group hover:bg-[#e8eef5] transition">
                    <div class="flex items-center gap-3 flex-1">
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

                        <div class="flex-1">
                            <p class="text-sm font-medium">
                                <?= htmlspecialchars($task['task_description']) ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                due <?= date('M j', strtotime($task['due_date'])) ?> ·
                                <?= htmlspecialchars($task['priority']) ?> ·
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
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

                        <span class="<?= $statusBadgeClass ?> px-3 py-1 text-xs font-medium rounded-md">
                            <?= htmlspecialchars($statusDisplay) ?>
                        </span>
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

    <!-- Optional: Add pagination if needed -->
    <?php if (isset($totalTaskPages) && $totalTaskPages > 1): ?>
        <div class="flex justify-center items-center gap-2 mt-4">
            <?php if ($currentTaskPage > 1): ?>
                <a href="?tab=tasks&page=<?= $currentTaskPage - 1 ?>"
                    class="px-3 py-1 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalTaskPages; $i++): ?>
                <a href="?tab=tasks&page=<?= $i ?>"
                    class="px-3 py-1 <?= $i == $currentTaskPage ? 'bg-primary text-white' : 'bg-gray-100 hover:bg-gray-200' ?> rounded-md transition">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentTaskPage < $totalTaskPages): ?>
                <a href="?tab=tasks&page=<?= $currentTaskPage + 1 ?>"
                    class="px-3 py-1 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>