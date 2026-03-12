<!-- Main Content -->
<div class="tab-content" id="competency-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Competency Assessment System</h2>
            <p class="text-gray-500 text-sm mt-1">Assess, evaluate, and manage employee competencies</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary" onclick="openModal('assessmentModal')">
                <i class="fas fa-clipboard-list"></i>
                New Assessment
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Pending Assessments</p>
                <span class="text-2xl font-bold text-gray-800"><?= $trainingStats['pending_assessment'] ?? 0 ?></span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Awaiting evaluation</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Completed This Month</p>
                <span class="text-2xl font-bold text-gray-800"><?= $completedThisMonth ?></span>
            </div>
            <p class="text-xs <?= $monthlyComparison >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
                <?= $monthlyComparison >= 0 ? '+' : '' ?><?= $monthlyComparison ?> from last month
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Gaps Identified</p>
                <span class="text-2xl font-bold text-gray-800"><?= $gapsIdentified ?></span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Requiring intervention</p>
        </div>
    </div>

    <!-- Interventions Section -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-semibold text-gray-800">Learning Interventions</h3>
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full"><?= $totalInterventions ?? 0 ?>
                    total</span>
            </div>
        </div>

        <div class="p-6">
            <?php if (!empty($interventionAssignments)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Employee</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Competency</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Intervention</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Assigned Date</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Due Date</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Status</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($interventionAssignments as $intervention): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-medium">
                                                <?= $intervention['initials'] ?>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    <?= htmlspecialchars($intervention['full_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    <?= htmlspecialchars($intervention['position']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm"><?= htmlspecialchars($intervention['competency_name']) ?></td>
                                    <td class="py-3 px-4 text-sm font-medium">
                                        <?= htmlspecialchars($intervention['intervention_title']) ?>
                                    </td>
                                    <td class="py-3 px-4 text-sm"><?= $intervention['formatted_assigned'] ?></td>
                                    <td class="py-3 px-4 text-sm">
                                        <?= $intervention['formatted_due'] ?>
                                        <?php if ($intervention['days_remaining'] > 0 && $intervention['status'] == 'pending'): ?>
                                            <span class="text-xs text-gray-400 block"><?= $intervention['days_remaining'] ?> days
                                                left</span>
                                        <?php elseif ($intervention['days_remaining'] < 0 && $intervention['status'] == 'pending'): ?>
                                            <span class="text-xs text-red-500">Overdue by
                                                <?= abs($intervention['days_remaining']) ?> days</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $intervention['status_class'] ?>">
                                            <?= ucfirst($intervention['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <!-- View Details Button - Eye Icon -->
                                            <button onclick="openModal('viewInterventionModal<?= $intervention['id'] ?>')"
                                                class="text-blue-600 hover:text-blue-800 text-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Edit Button - Pencil Icon -->
                                            <button onclick="openModal('editInterventionModal<?= $intervention['id'] ?>')"
                                                class="text-gray-600 hover:text-gray-800 text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Interventions Pagination -->
                <?php if ($totalInterventionPages > 1): ?>
                    <div class="flex justify-center mt-6 gap-2">
                        <?php for ($i = 1; $i <= $totalInterventionPages; $i++): ?>
                            <a href="/main?tab=competency&intervention_page=<?= $i ?>"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                            <?= $i == $interventionPage ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-graduation-cap text-4xl mb-2"></i>
                    <p class="text-sm">No interventions assigned yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Assessments Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Assessments</h3>

            <!-- Pagination -->
            <?php if ($totalCompetencyPages > 1): ?>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Page <?= $competencyPage ?> of <?= $totalCompetencyPages ?></span>
                    <div class="flex gap-1">
                        <?php if ($competencyPage > 1): ?>
                            <a href="/main?tab=competency&competency_page=<?= $competencyPage - 1 ?>"
                                class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 text-sm transition-colors">Prev</a>
                        <?php endif; ?>
                        <?php if ($competencyPage < $totalCompetencyPages): ?>
                            <a href="/main?tab=competency&competency_page=<?= $competencyPage + 1 ?>"
                                class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 text-sm transition-colors">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Competency</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assessed Level</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Required Level</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Gap</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentAssessments)): ?>
                            <?php foreach ($recentAssessments as $assessment):
                                $proficiencyLevels = ['1 - Beginner', '2 - Developing', '3 - Intermediate', '4 - Advanced', '5 - Expert'];
                                $assessedLevel = $proficiencyLevels[$assessment['proficiency_level'] - 1] ?? $assessment['proficiency_level'] . ' - Unknown';
                                $requiredLevel = $proficiencyLevels[$assessment['required_level'] - 1] ?? $assessment['required_level'] . ' - Unknown';
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-medium text-gray-800">
                                            <?= htmlspecialchars($assessment['employee_name']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            <?= htmlspecialchars($assessment['employee_position'] ?? '') ?>
                                        </p>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        <?= htmlspecialchars($assessment['competency_name']) ?>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= $assessedLevel ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= $requiredLevel ?></td>
                                    <td class="py-3 px-4">
                                        <?php if ($assessment['gap_level'] > 0): ?>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $assessment['gap_class'] ?>">
                                                <?= $assessment['gap_level'] ?> level gap
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                No gap
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $assessment['status_class'] ?>">
                                            <?= $assessment['gap_status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if ($assessment['gap_level'] > 0): ?>
                                            <!-- Gap detected - Show Review Gap button -->
                                            <button onclick="openModal('gapModal<?= $assessment['id'] ?>')"
                                                class="group relative text-sm text-yellow-600 hover:text-yellow-700 bg-yellow-50 hover:bg-yellow-100 px-3 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-1.5 border border-yellow-200 shadow-sm hover:shadow"
                                                title="Review competency gap">
                                                <i class="fas fa-chart-line text-xs"></i>
                                                <span>Review Gap</span>
                                                <span
                                                    class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                    View gap analysis
                                                </span>
                                            </button>
                                        <?php else: ?>
                                            <!-- No gap - Show Notify button -->
                                            <button onclick="openModal('gapModal<?= $assessment['id'] ?>')"
                                                class="group relative text-sm text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-1.5 border border-green-200 shadow-sm hover:shadow"
                                                title="Send recognition">
                                                <i class="fas fa-bell text-xs"></i>
                                                <span>Notify</span>
                                                <span
                                                    class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                                                    Send recognition message
                                                </span>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-clipboard-check text-5xl mb-3 text-gray-300"></i>
                                        <p class="text-lg font-medium">No assessments found</p>
                                        <p class="text-sm">No competency assessments have been conducted yet</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Pagination -->
            <?php if ($totalCompetencyPages > 1): ?>
                <div class="flex justify-center mt-6 gap-2">
                    <?php for ($i = 1; $i <= $totalCompetencyPages; $i++): ?>
                        <a href="/main?tab=competency&competency_page=<?= $i ?>"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                        <?= $i == $competencyPage ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View Intervention Modal -->
<?php foreach ($interventionAssignments as $intervention): ?>
    <div id="viewInterventionModal<?= $intervention['id'] ?>"
        class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-800">Intervention Details</h3>
                <button onclick="closeModal('viewInterventionModal<?= $intervention['id'] ?>')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-4 space-y-3">
                <!-- Employee -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 font-semibold"><?= $intervention['initials'] ?></span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($intervention['full_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($intervention['position']) ?></p>
                    </div>
                </div>

                <!-- Competency & Type -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 p-2 rounded">
                        <p class="text-xs text-gray-400">Competency</p>
                        <p class="font-medium text-sm"><?= htmlspecialchars($intervention['competency_name']) ?></p>
                    </div>
                    <div class="bg-gray-50 p-2 rounded">
                        <p class="text-xs text-gray-400">Type</p>
                        <p class="font-medium text-sm"><?= $intervention['intervention_type'] ?></p>
                    </div>
                </div>

                <!-- Title -->
                <div class="bg-gray-50 p-2 rounded">
                    <p class="text-xs text-gray-400">Title</p>
                    <p class="font-medium text-sm"><?= htmlspecialchars($intervention['intervention_title']) ?></p>
                </div>

                <!-- Levels -->
                <div class="space-y-2">
                    <div>
                        <div class="flex justify-between text-xs">
                            <span>Current: <?= $intervention['current_level'] ?>/5</span>
                            <span
                                class="text-gray-400"><?= $intervention['current_level'] == 1 ? 'Beginner' : ($intervention['current_level'] == 2 ? 'Developing' : ($intervention['current_level'] == 3 ? 'Intermediate' : ($intervention['current_level'] == 4 ? 'Advanced' : 'Expert'))) ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-yellow-500 h-1.5 rounded-full"
                                style="width: <?= ($intervention['current_level'] / 5) * 100 ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs">
                            <span>Required: <?= $intervention['required_level'] ?>/5</span>
                            <span
                                class="text-gray-400"><?= $intervention['required_level'] == 1 ? 'Beginner' : ($intervention['required_level'] == 2 ? 'Developing' : ($intervention['required_level'] == 3 ? 'Intermediate' : ($intervention['required_level'] == 4 ? 'Advanced' : 'Expert'))) ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-green-500 h-1.5 rounded-full"
                                style="width: <?= ($intervention['required_level'] / 5) * 100 ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Dates & Status -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 p-2 rounded">
                        <p class="text-xs text-gray-400">Due Date</p>
                        <p class="font-medium text-sm"><?= $intervention['formatted_due'] ?></p>
                        <?php if ($intervention['days_remaining'] > 0 && $intervention['status'] == 'pending'): ?>
                            <p class="text-xs text-gray-400"><?= $intervention['days_remaining'] ?> days left</p>
                        <?php elseif ($intervention['days_remaining'] < 0 && $intervention['status'] == 'pending'): ?>
                            <p class="text-xs text-red-500">Overdue by <?= abs($intervention['days_remaining']) ?> days</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-gray-50 p-2 rounded">
                        <p class="text-xs text-gray-400">Status</p>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $intervention['status_class'] ?>"><?= ucfirst($intervention['status']) ?></span>
                    </div>
                </div>

                <!-- Notes if any -->
                <?php if (!empty($intervention['notes'])): ?>
                    <div class="bg-gray-50 p-2 rounded">
                        <p class="text-xs text-gray-400">Notes</p>
                        <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($intervention['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-4 border-t border-gray-100 sticky bottom-0 bg-white">
                <button type="button" onclick="closeModal('viewInterventionModal<?= $intervention['id'] ?>')"
                    class="w-full px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Intervention Modal -->
    <div id="editInterventionModal<?= $intervention['id'] ?>"
        class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-800">Edit Intervention</h3>
                <button onclick="closeModal('editInterventionModal<?= $intervention['id'] ?>')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="/update-intervention" method="POST" class="p-4 space-y-3">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="intervention_id" value="<?= $intervention['id'] ?>">
                <input type="hidden" name="__method" value="PATCH">

                <!-- Status - Hidden field with current value -->
                <input type="hidden" name="status" value="<?= $intervention['status'] ?>">

                <!-- Employee (read-only) -->
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-400">Employee</p>
                    <p class="font-medium"><?= htmlspecialchars($intervention['full_name']) ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($intervention['position']) ?></p>
                </div>

                <!-- Competency (read-only) -->
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-400">Competency</p>
                    <p class="font-medium"><?= htmlspecialchars($intervention['competency_name']) ?></p>
                </div>

                <!-- Title (editable) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="intervention_title"
                        value="<?= htmlspecialchars($intervention['intervention_title']) ?>" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Current Level & New Level -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Current Level</label>
                        <input type="text" value="Level <?= $intervention['current_level'] ?>"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50" disabled readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">New Level</label>
                        <select name="new_level" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                            <option value="1" <?= ($intervention['new_level'] ?? 0) == 1 ? 'selected' : '' ?>>Level 1 -
                                Beginner</option>
                            <option value="2" <?= ($intervention['new_level'] ?? 0) == 2 ? 'selected' : '' ?>>Level 2 -
                                Developing</option>
                            <option value="3" <?= ($intervention['new_level'] ?? 0) == 3 ? 'selected' : '' ?>>Level 3 -
                                Intermediate</option>
                            <option value="4" <?= ($intervention['new_level'] ?? 0) == 4 ? 'selected' : '' ?>>Level 4 -
                                Advanced</option>
                            <option value="5" <?= ($intervention['new_level'] ?? 0) == 5 ? 'selected' : '' ?>>Level 5 - Expert
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Due Date (editable) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="<?= $intervention['due_date'] ?>"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Notes (editable) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500"><?= htmlspecialchars($intervention['notes'] ?? '') ?></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 pt-2 sticky bottom-0 bg-white">
                    <button type="button" onclick="closeModal('editInterventionModal<?= $intervention['id'] ?>')"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg border border-gray-200">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script>
    function assignInterventions() {
        const selected = document.querySelectorAll('#interventionsList input:checked');
        if (selected.length === 0) {
            alert('Please select at least one intervention');
            return;
        }
        alert('Interventions assigned successfully');
        closeModal('gapModal');
    }

    function validateInterventions(assessmentId) {
        const checkboxes = document.querySelectorAll(`.competency-checkbox-${assessmentId}:checked`);
        if (checkboxes.length === 0) {
            alert('Please select at least one intervention to assign.');
            return false;
        }
        return confirm(`Assign ${checkboxes.length} intervention(s) to this employee?`);
    }

    function startIntervention(id) {
        if (confirm('Start this intervention?')) {
            // Add AJAX call here
            alert('Intervention started');
        }
    }

    function completeIntervention(id) {
        if (confirm('Mark this intervention as completed?')) {
            // Add AJAX call here
            alert('Intervention completed');
        }
    }

    function editIntervention(id) {
        alert('Edit intervention: ' + id);
        // You can open an edit modal here
    }
</script>