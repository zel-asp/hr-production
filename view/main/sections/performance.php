<!-- Main Performance Management Content -->
<div class="tab-content" id="performance-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Performance Management</h2>
            <p class="text-gray-600 mt-1">Evaluate performance and determine employment status</p>
        </div>
    </div>

    <!-- Performance Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-sm text-gray-600">Pending Evaluations</p>
            <p class="text-2xl font-bold text-primary"><?= $pendingCount['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Probationary reviews due</p>
        </div>

        <div class="card p-4">
            <p class="text-sm text-gray-600">Ready for Evaluation </p>
            <p class="text-2xl font-bold text-blue-600">
                <?= $pendingCount['count'] ?? 0 ?>
            </p>
            <p class="text-xs text-gray-500">All employees</p>
        </div>

        <div class="card p-4">
            <p class="text-sm text-gray-600">Need Improvement</p>
            <p class="text-2xl font-bold text-red-600"><?= $needImprovement['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Based on recent evaluations</p>
        </div>

        <div class="card p-4">
            <p class="text-sm text-gray-600">On PIP</p>
            <p class="text-2xl font-bold text-yellow-600"><?= $activePipCount['count'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Performance improvement plans</p>
        </div>
    </div>

    <!-- Probationary Employees Table -->
    <div class="card p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Probationary Employees - Pending Evaluation</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Employee</th>
                        <th class="text-left py-3">Position</th>
                        <th class="text-left py-3">Start Date</th>
                        <th class="text-left py-3">Review Due</th>
                        <th class="text-left py-3">Status</th>
                        <th class="text-left py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($probationaryEmployees)): ?>
                        <?php foreach ($probationaryEmployees as $emp): ?>
                            <tr class="border-b border-gray-100">
                                <td class="py-3 font-medium">
                                    <?= htmlspecialchars($emp['full_name']) ?>
                                </td>
                                <td class="py-3">
                                    <?= htmlspecialchars($emp['position']) ?>
                                </td>
                                <td class="py-3">
                                    <?= $emp['start_date'] ? date('M d, Y', strtotime($emp['start_date'])) : '-' ?>
                                </td>
                                <td class="py-3">
                                    <?= $emp['hired_date'] ? date('M d, Y', strtotime($emp['hired_date'] . ' + 90 days')) : '-' ?>
                                </td>
                                <td class="py-3">
                                    <span class="status-badge bg-yellow-100 text-yellow-800">Probationary</span>
                                </td>
                                <td class="py-3">
                                    <button type="button" class="text-primary hover:text-primary-dark"
                                        onclick="openModal('performanceEvaluationModal<?= $emp['id'] ?>')">
                                        <i class="fas fa-star mr-1"></i>Evaluate
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                No probationary employees pending evaluation.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Evaluation Results -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Evaluation Results</h3>
            <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
                <?= count($recentEvaluations) ?> Evaluations
            </span>
        </div>

        <?php if (!empty($recentEvaluations)): ?>
            <div class="space-y-4">
                <?php foreach ($recentEvaluations as $eval): ?>
                    <?php
                    $isHighPerformance = $eval['overall_score'] >= 3.5;
                    $probationEndDate = date('M d, Y', strtotime($eval['hired_date'] . ' + 90 days'));
                    $isRegular = ($eval['status'] === 'Regular');
                    $hasPip = ($eval['status'] === 'Improvement');
                    ?>

                    <?php if ($isHighPerformance): ?>
                        <?php if ($isHighPerformance): ?>
                            <!-- High Performance - Regular Status -->
                            <div
                                class="bg-linear-to-r from-green-50 to-white rounded-r-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4 border-l-4 border-green-400">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="bg-green-100 rounded-full p-2.5 shrink-0">
                                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($eval['full_name']) ?></p>
                                                <span class="text-xs bg-green-200 text-green-800 px-2 py-0.5 rounded-full w-fit">High
                                                    Performer</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">
                                                <?= htmlspecialchars($eval['position']) ?> •
                                                <span class="text-gray-500">Probation ended <?= $probationEndDate ?></span>
                                            </p>
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-1.5">
                                                    <span
                                                        class="text-lg font-bold text-green-700"><?= number_format($eval['overall_score'], 1) ?></span>
                                                    <span class="text-xs text-gray-500">/5.0</span>
                                                </div>
                                                <div class="h-2 w-24 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-green-500 rounded-full"
                                                        style="width: <?= ($eval['overall_score'] / 5) * 100 ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <?php if ($isRegular): ?>
                                            <span
                                                class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full flex items-center gap-1 mr-auto sm:mr-0">
                                                <i class="fas fa-check-circle text-xs"></i> Regular
                                            </span>

                                            <!-- DELETE BUTTON - Styled like PIP button but with red colors -->
                                            <form method="POST" action="/delete-evaluation" class="inline-block"
                                                onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                                                <input type="hidden" name="evaluation_id" value="<?= $eval['evaluation_id'] ?>">
                                                <input type="hidden" name="employee_id" value="<?= $eval['employee_id'] ?>">
                                                <input type="hidden" value="DELETE" name="__method">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <button type="submit"
                                                    class="group bg-linear-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white text-sm font-medium px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                    <span>Delete</span>
                                                    <i
                                                        class="fas fa-chevron-right text-xs opacity-70 group-hover:translate-x-0.5 transition-transform"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- MAKE REGULAR BUTTON - Styled like PIP button but with green/blue colors -->
                                            <button
                                                class="group bg-linear-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                                onclick="openModal('regularModal_<?= $eval['employee_id'] ?>')">
                                                <i class="fas fa-user-check text-xs"></i>
                                                <span>Make Regular</span>
                                                <i
                                                    class="fas fa-chevron-right text-xs opacity-70 group-hover:translate-x-0.5 transition-transform"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($eval['interpretation'])): ?>
                                    <p class="text-sm text-gray-600 mt-3 pt-3 border-t border-green-200">
                                        <?= htmlspecialchars($eval['interpretation']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Low Performance - PIP Required -->
                        <div
                            class="bg-linear-to-r from-yellow-50 to-white rounded-r-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4 border-l-4 border-yellow-400">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <div class="bg-yellow-100 rounded-full p-2.5 shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($eval['full_name']) ?></p>
                                            <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded-full w-fit">Needs
                                                Improvement</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <?= htmlspecialchars($eval['position']) ?> •
                                            <span class="text-gray-500">Probation ended <?= $probationEndDate ?></span>
                                        </p>
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-lg font-bold text-yellow-700"><?= number_format($eval['overall_score'], 1) ?></span>
                                                <span class="text-xs text-gray-500">/5.0</span>
                                            </div>
                                            <div class="h-2 w-24 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-yellow-500 rounded-full"
                                                    style="width: <?= ($eval['overall_score'] / 5) * 100 ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <?php if ($hasPip): ?>
                                        <span
                                            class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full flex items-center gap-1 mr-auto sm:mr-0">
                                            <i class="fas fa-clock text-xs"></i> On PIP
                                        </span>
                                        <!-- UPDATE PIP BUTTON -->
                                        <button
                                            class="group bg-linear-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                            onclick="openModal('pipModal_<?= $eval['employee_id'] ?>')">
                                            <i class="fas fa-edit text-xs"></i>
                                            <span>Update PIP</span>
                                            <i
                                                class="fas fa-chevron-right text-xs opacity-70 group-hover:translate-x-0.5 transition-transform"></i>
                                        </button>
                                    <?php else: ?>
                                        <!-- CREATE PIP BUTTON -->
                                        <button
                                            class="group bg-linear-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                            onclick="openModal('pipModal_<?= $eval['employee_id'] ?>')">
                                            <i class="fas fa-file-signature text-xs"></i>
                                            <span>Create PIP</span>
                                            <i
                                                class="fas fa-chevron-right text-xs opacity-70 group-hover:translate-x-0.5 transition-transform"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($eval['interpretation'])): ?>
                                <p class="text-sm text-gray-600 mt-3 pt-3 border-t border-yellow-200">
                                    <?= htmlspecialchars($eval['interpretation']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 px-4">
                <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                </div>
                <h4 class="text-base font-medium text-gray-900 mb-1">No evaluation results</h4>
                <p class="text-sm text-gray-500 max-w-sm mx-auto">
                    Complete performance evaluations to see results here. Evaluations help track employee progress and
                    performance.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>