<!-- Main Content -->
<div class="tab-content" id="competency-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Competency Assessment System</h2>
            <p class="text-gray-500 text-sm mt-1">Assess, evaluate, and manage employee competencies</p>
        </div>
        <button class="btn-primary" onclick="openModal('assessmentModal')">
            <i class="fas fa-clipboard-list"></i>
            New Assessment
        </button>
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
                                            <button
                                                class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1"
                                                onclick="openGapAnalysis(true, <?= $assessment['employee_id'] ?>, '<?= htmlspecialchars($assessment['employee_name']) ?>', '<?= htmlspecialchars($assessment['competency_name']) ?>', <?= $assessment['proficiency_level'] ?>, <?= $assessment['required_level'] ?>)">
                                                <i class="fas fa-chart-line text-xs"></i>
                                                Review Gap
                                            </button>
                                        <?php else: ?>
                                            <button
                                                class="text-sm text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1"
                                                onclick="openGapAnalysis(false, <?= $assessment['employee_id'] ?>, '<?= htmlspecialchars($assessment['employee_name']) ?>', '<?= htmlspecialchars($assessment['competency_name']) ?>')">
                                                <i class="fas fa-bell text-xs"></i>
                                                Notify
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
                        <a href="/main?tab=competency&competency_page=<?= $i ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                    <?= $i == $competencyPage
                        ? 'bg-primary text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Gap Analysis Modal -->
<div id="gapModal" class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Competency Gap Analysis</h3>
            <button onclick="closeModal('gapModal')"
                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Gap Identified View -->
            <div id="gapIdentified" class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-800" id="gapTitle">
                                Competency gap detected
                            </p>
                            <p class="text-sm text-yellow-700 mt-1" id="gapDescription">
                                Loading gap details...
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Recommended Learning Interventions</h4>
                    <div class="space-y-2" id="interventionsList">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button onclick="closeModal('gapModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button onclick="assignInterventions()"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-graduation-cap"></i>
                        Assign Interventions
                    </button>
                </div>
            </div>

            <!-- No Gap View -->
            <div id="noGap" class="hidden space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800" id="noGapTitle">No competency gaps identified
                            </p>
                            <p class="text-sm text-green-700 mt-1" id="noGapDescription">
                                Loading employee details...
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Notification to Employee</h4>
                    <textarea id="notificationMessage"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400"
                        rows="4" readonly></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button onclick="closeModal('gapModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button onclick="sendNotification()"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-envelope"></i>
                        Send Notification
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Gap Analysis Modal functions
    function openGapAnalysis(hasGap, employeeId, employeeName, competencyName, currentLevel, requiredLevel) {
        if (hasGap) {
            document.getElementById('gapIdentified').classList.remove('hidden');
            document.getElementById('noGap').classList.add('hidden');

            document.getElementById('gapDescription').textContent =
                `${employeeName} requires improvement in ${competencyName} (Level ${currentLevel} of ${requiredLevel})`;

            // Load interventions
            loadInterventions(competencyName);
        } else {
            document.getElementById('gapIdentified').classList.add('hidden');
            document.getElementById('noGap').classList.remove('hidden');

            document.getElementById('noGapDescription').textContent =
                `${employeeName} has met all required standards for ${competencyName}.`;

            document.getElementById('notificationMessage').value =
                `Dear ${employeeName},\n\nCongratulations! Your recent competency assessment for ${competencyName} has met all required standards. Your performance demonstrates strong proficiency in this area.\n\nKeep up the excellent work!\n\nBest regards,\nHR Department`;
        }

        openModal('gapModal');
    }

    function loadInterventions(competencyName) {
        const interventionsList = document.getElementById('interventionsList');

        // Mock interventions - in production, fetch from database
        const interventions = [
            {
                title: competencyName + ' Excellence Training',
                type: 'Online course',
                duration: '4 hours',
                badge: 'Recommended',
                badgeClass: 'bg-blue-50 text-blue-700 border-blue-200'
            },
            {
                title: 'Shadow Senior Staff',
                type: 'On-the-job training',
                duration: '2 weeks',
                badge: 'Available',
                badgeClass: 'bg-green-50 text-green-700 border-green-200'
            },
            {
                title: 'Communication Skills Workshop',
                type: 'In-person session',
                duration: '1 day',
                badge: 'Optional',
                badgeClass: 'bg-purple-50 text-purple-700 border-purple-200'
            }
        ];

        interventionsList.innerHTML = interventions.map(intervention => `
        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex items-start gap-3">
                <input type="checkbox" class="mt-1 rounded border-gray-300 text-gray-600 focus:ring-gray-200">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">${intervention.title}</p>
                    <p class="text-xs text-gray-500 mt-0.5">${intervention.type} • ${intervention.duration}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${intervention.badgeClass} mt-2">
                        ${intervention.badge}
                    </span>
                </div>
            </div>
        </div>
    `).join('');
    }

    function assignInterventions() {
        const selected = document.querySelectorAll('#interventionsList input:checked');
        if (selected.length === 0) {
            alert('Please select at least one intervention');
            return;
        }

        alert('Interventions assigned successfully');
        closeModal('gapModal');
    }

    function sendNotification() {
        alert('Notification sent successfully');
        closeModal('gapModal');
    }
</script>