<!-- Main Training Management Content -->
<div class="tab-content" id="training-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Training Management</h2>
            <p class="text-gray-500 text-sm mt-1">Manage training programs based on competency gaps</p>
        </div>
        <button class="btn-primary" onclick="openModal('trainingModal')">
            <i class="fas fa-plus"></i>
            New Training Program
        </button>
    </div>

    <!-- Training Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Active Trainings</p>
            <p class="text-2xl font-bold text-gray-800"><?= $trainingStats['active_trainings'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Internal</p>
            <p class="text-2xl font-bold text-gray-800"><?= $trainingStats['internal_count'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">External</p>
            <p class="text-2xl font-bold text-gray-800"><?= $trainingStats['external_count'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Certifications</p>
            <p class="text-2xl font-bold text-gray-800"><?= $trainingStats['certification_count'] ?? 0 ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pending Assessment</p>
            <p class="text-2xl font-bold text-yellow-600"><?= $trainingStats['pending_assessment'] ?? 0 ?></p>
        </div>
    </div>

    <!-- Training Schedule Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Training Schedule</h3>

            <!-- Filter and Pagination -->
            <div class="flex items-center gap-4">
                <select
                    class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-gray-200 focus:border-gray-400"
                    onchange="window.location.href = '?tab=training&training_filter=' + this.value + '&training_page=1'">
                    <option value="all" <?= $trainingFilter == 'all' ? 'selected' : '' ?>>All Trainings</option>
                    <option value="scheduled" <?= $trainingFilter == 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="completed" <?= $trainingFilter == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="pending" <?= $trainingFilter == 'pending' ? 'selected' : '' ?>>Pending Assessment
                    </option>
                </select>

                <?php if ($totalTrainingPages > 1): ?>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Page <?= $trainingPage ?> of <?= $totalTrainingPages ?></span>
                        <div class="flex gap-1">
                            <?php if ($trainingPage > 1): ?>
                                <a href="?tab=training&training_filter=<?= $trainingFilter ?>&training_page=<?= $trainingPage - 1 ?>"
                                    class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 text-sm transition-colors">Prev</a>
                            <?php endif; ?>
                            <?php if ($trainingPage < $totalTrainingPages): ?>
                                <a href="?tab=training&training_filter=<?= $trainingFilter ?>&training_page=<?= $trainingPage + 1 ?>"
                                    class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 text-sm transition-colors">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Training Title</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provider</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Schedule</th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-left py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($trainingSchedule)): ?>
                            <?php foreach ($trainingSchedule as $training):
                                // Determine type badge color
                                $typeClass = '';
                                switch ($training['training_type']) {
                                    case 'internal':
                                        $typeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        break;
                                    case 'external':
                                        $typeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                        break;
                                    case 'certification':
                                        $typeClass = 'bg-green-50 text-green-700 border-green-200';
                                        break;
                                }

                                // Determine status badge color
                                $statusClass = '';
                                switch ($training['status']) {
                                    case 'Scheduled':
                                        $statusClass = 'bg-green-50 text-green-700 border-green-200';
                                        break;
                                    case 'Completed':
                                        $statusClass = 'bg-gray-100 text-gray-700 border-gray-200';
                                        break;
                                    case 'Missed':
                                        $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                        break;
                                }

                                // Determine assessment badge
                                $assessmentClass = '';
                                $assessmentText = '';
                                switch ($training['assessment_status']) {
                                    case 'completed':
                                        $assessmentClass = 'bg-green-50 text-green-700 border-green-200';
                                        $assessmentText = 'Passed';
                                        break;
                                    case 'failed':
                                        $assessmentClass = 'bg-red-50 text-red-700 border-red-200';
                                        $assessmentText = 'Failed';
                                        break;
                                    case 'pending':
                                        $assessmentClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        $assessmentText = 'Pending';
                                        break;
                                    default:
                                        $assessmentClass = 'bg-gray-50 text-gray-500 border-gray-200';
                                        $assessmentText = 'Not Started';
                                }

                                // Format schedule
                                $scheduleText = date('M j', strtotime($training['start_date']));
                                if ($training['end_date'] && $training['end_date'] != $training['start_date']) {
                                    $scheduleText .= ' - ' . date('M j, Y', strtotime($training['end_date']));
                                } else {
                                    $scheduleText .= ', ' . date('Y', strtotime($training['start_date']));
                                }
                                if ($training['start_time']) {
                                    $scheduleText .= ' · ' . date('g:i A', strtotime($training['start_time']));
                                }
                                ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-3">
                                        <?php if ($training['competency_name']): ?>
                                            <p class="text-xs text-gray-400 mt-0.5">Competency:
                                                <?= htmlspecialchars($training['competency_name']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-sm font-medium text-gray-800">
                                            <?= htmlspecialchars($training['employee_name'] ?? 'N/A') ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            <?= htmlspecialchars($training['employee_position'] ?? '') ?>
                                        </p>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?= $typeClass ?>">
                                            <?= ucfirst($training['training_type']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">
                                        <?= htmlspecialchars($training['provider_name'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600"><?= $scheduleText ?></td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                            <?= $training['status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?= $assessmentClass ?>">
                                            <?= $assessmentText ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-alt text-5xl mb-3 text-gray-300"></i>
                                        <p class="text-lg font-medium">No trainings found</p>
                                        <p class="text-sm">No training schedules available for the selected filter</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Pagination -->
            <?php if ($totalTrainingPages > 1): ?>
                <div class="flex justify-center mt-6 gap-2">
                    <?php for ($i = 1; $i <= $totalTrainingPages; $i++): ?>
                        <a href="?tab=training&training_filter=<?= $trainingFilter ?>&training_page=<?= $i ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                       <?= $i == $trainingPage
                           ? 'bg-gray-800 text-white'
                           : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($completedTrainings)): ?>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-semibold text-gray-800">Completed Trainings & Results</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($completedTrainings as $training):
                        $assessmentResult = $training['assessment_status'] == 'completed' ? 'Passed' : 'Failed';
                        $resultClass = $training['assessment_status'] == 'completed' ? 'text-green-600' : 'text-red-600';
                        ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($training['title']) ?></p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs text-gray-500"><i class="far fa-calendar mr-1"></i>Completed:
                                            <?= date('M j, Y', strtotime($training['end_date'] ?? $training['start_date'])) ?></span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-xs text-gray-500"><i class="far fa-user mr-1"></i>Trainer:
                                            <?= htmlspecialchars($training['provider_name'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $training['assessment_status'] == 'completed' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                                    <?= $assessmentResult ?>
                                </span>
                            </div>

                            <!-- Results per employee -->
                            <div class="mt-3">
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                            <?= strtoupper(substr($training['employee_name'], 0, 2)) ?>
                                        </span>
                                        <span
                                            class="text-sm text-gray-700"><?= htmlspecialchars($training['employee_name']) ?></span>
                                        <span
                                            class="text-xs text-gray-400">(<?= htmlspecialchars($training['employee_position']) ?>)</span>
                                    </div>
                                    <span class="text-xs font-medium <?= $resultClass ?>">
                                        <?= $assessmentResult ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Competency Update -->
                            <?php if ($training['competency_id'] && $training['assessment_status'] == 'completed'): ?>
                                <div class="mt-3 p-2 bg-gray-50 rounded-lg text-xs text-gray-500 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-green-500"></i>
                                    <span>Competency levels updated: <?= htmlspecialchars($training['competency_name']) ?> (Level
                                        increased)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>