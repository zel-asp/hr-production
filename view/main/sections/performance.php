<!-- Main Performance Management Content -->
<div class="tab-content" id="performance-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Performance Management</h2>
            <p class="text-gray-500 text-sm mt-1">Evaluate performance and determine employment status</p>
        </div>
    </div>

    <!-- Performance Dashboard -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Pending Evaluations</p>
            <p class="text-2xl font-bold text-gray-800"><?= $pendingCount['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-400 mt-1">Probationary reviews due</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Ready for Evaluation</p>
            <p class="text-2xl font-bold text-gray-800"><?= $pendingCount['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-400 mt-1">All employees</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">Need Improvement</p>
            <p class="text-2xl font-bold text-gray-800"><?= $needImprovement['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-400 mt-1">Based on recent evaluations</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-1">On PIP</p>
            <p class="text-2xl font-bold text-gray-800"><?= $activePipCount['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-400 mt-1">Performance improvement plans</p>
        </div>
    </div>

    <!-- Probationary Employees Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">Probationary Employees - Pending Evaluation</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Position</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Start
                                Date</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Review
                                Due</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($probationaryEmployees)): ?>
                            <?php foreach ($probationaryEmployees as $emp): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($emp['full_name']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($emp['position']) ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= $emp['start_date'] ? date('M d, Y', strtotime($emp['start_date'])) : '-' ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= $emp['hired_date'] ? date('M d, Y', strtotime($emp['hired_date'] . ' + 90 days')) : '-' ?>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                            Probationary
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <button type="button"
                                            class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1"
                                            onclick="openModal('performanceEvaluationModal<?= $emp['id'] ?>')">
                                            <i class="fas fa-star text-xs"></i>
                                            Evaluate
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-users text-gray-300 text-2xl"></i>
                                        <p>No probationary employees pending evaluation.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Evaluation Results -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Recent Evaluation Results</h3>
            <span class="text-xs font-medium bg-white text-gray-600 px-2.5 py-1 rounded-full border border-gray-200">
                <?= count($recentEvaluations) ?> Evaluations
            </span>
        </div>

        <div class="p-6">
            <?php if (!empty($recentEvaluations)): ?>
                <div class="space-y-4">
                    <?php foreach ($recentEvaluations as $eval): ?>
                        <?php
                        $isHighPerformance = $eval['overall_score'] >= 3.5;
                        $probationEndDate = date('M d, Y', strtotime($eval['hired_date'] . ' + 90 days'));
                        $isRegular = ($eval['status'] === 'Regular');
                        $hasPip = ($eval['status'] === 'Improvement');
                        ?>

                        <!-- Evaluation Card -->
                        <div
                            class="border border-gray-100 rounded-xl overflow-hidden hover:shadow-sm transition-shadow duration-200">
                            <!-- Card Header -->
                            <div
                                class="px-4 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                                        <?= strtoupper(substr($eval['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800"><?= htmlspecialchars($eval['full_name']) ?></h4>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($eval['position']) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                        <?= $isHighPerformance ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' ?>">
                                        <?= $isHighPerformance ? 'High Performer' : 'Needs Improvement' ?>
                                    </span>
                                    <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-lg border border-gray-200">
                                        <span
                                            class="text-sm font-bold <?= $isHighPerformance ? 'text-green-600' : 'text-yellow-600' ?>">
                                            <?= number_format($eval['overall_score'], 1) ?>
                                        </span>
                                        <span class="text-xs text-gray-400">/5.0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 bg-white">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                    <div class="flex-1 space-y-2">
                                        <p class="text-sm text-gray-600">
                                            <span class="text-gray-400">Probation ended:</span>
                                            <?= $probationEndDate ?>
                                        </p>
                                        <?php if (!empty($eval['interpretation'])): ?>
                                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                                                <?= htmlspecialchars($eval['interpretation']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <?php if ($isHighPerformance): ?>
                                            <?php if ($isRegular): ?>
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-full border border-blue-200">
                                                    <i class="fas fa-check-circle text-xs"></i> Regular
                                                </span>
                                                <form method="POST" action="/delete-evaluation" class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                                                    <input type="hidden" name="evaluation_id" value="<?= $eval['evaluation_id'] ?>">
                                                    <input type="hidden" name="employee_id" value="<?= $eval['employee_id'] ?>">
                                                    <input type="hidden" value="DELETE" name="__method">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <button type="submit"
                                                        class="text-sm text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 border border-red-200">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn-primary"
                                                    onclick="openModal('regularModal_<?= $eval['employee_id'] ?>')">
                                                    <i class="fas fa-user-check text-xs"></i>
                                                    Make Regular
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($hasPip): ?>
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs bg-purple-50 text-purple-700 px-2 py-1 rounded-full border border-purple-200">
                                                    <i class="fas fa-clock text-xs"></i> On PIP
                                                </span>
                                                <button class="btn-primary" onclick="openModal('pipModal_<?= $eval['employee_id'] ?>')">
                                                    <i class="fas fa-edit text-xs"></i>
                                                    Update PIP
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    class="text-sm text-gray-600 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1"
                                                    onclick="openModal('pipModal_<?= $eval['employee_id'] ?>')">
                                                    <i class="fas fa-file-signature text-xs"></i>
                                                    Create PIP
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 px-4">
                    <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                    </div>
                    <h4 class="text-base font-medium text-gray-800 mb-1">No evaluation results</h4>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto">
                        Complete performance evaluations to see results here. Evaluations help track employee progress and
                        performance.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>