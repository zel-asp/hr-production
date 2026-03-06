<!-- Performance Evaluation Modals -->
<?php if (!empty($probationaryEmployees)): ?>
    <?php foreach ($probationaryEmployees as $emp): ?>
        <!-- Performance Evaluation Modal for <?= htmlspecialchars($emp['full_name']) ?> -->
        <div id="performanceEvaluationModal<?= $emp['id'] ?>" class="modal">
            <div
                class="modal-content bg-white rounded-[--radius-card] shadow-[--shadow-card] w-full max-w-3xl mx-4 p-6 relative">
                <!-- Close button -->
                <button type="button" onclick="closeModal('performanceEvaluationModal<?= $emp['id'] ?>')"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>

                <!-- Header -->
                <h3 class="text-xl font-semibold mb-6" style="color: var(--color-primary)">Performance Evaluation</h3>

                <form method="POST" action="/save-performance-evaluation">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">
                    <input type="hidden" name="review_period_start" value="<?= $emp['start_date'] ?>">
                    <input type="hidden" name="review_period_end"
                        value="<?= date('Y-m-d', strtotime($emp['hired_date'] . ' + 90 days')) ?>">
                    <input type="hidden" name="review_type" value="90-Day Probationary Review">

                    <!-- Employee Info Card -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Employee</p>
                                <p class="font-semibold"><?= htmlspecialchars($emp['full_name']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Position</p>
                                <p class="font-semibold"><?= htmlspecialchars($emp['position']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Review Period</p>
                                <p class="font-semibold">
                                    <?= $emp['start_date'] ? date('M d', strtotime($emp['start_date'])) : '-' ?> -
                                    <?= $emp['hired_date'] ? date('M d, Y', strtotime($emp['hired_date'] . ' + 90 days')) : '-' ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Review Type</p>
                                <p class="font-semibold">90-Day Probationary Review</p>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Criteria -->
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2 mb-6">
                        <?php
                        $criteriaLabels = [
                            1 => 'Job Knowledge',
                            2 => 'Quality of Work',
                            3 => 'Customer Service',
                            4 => 'Teamwork & Collaboration',
                            5 => 'Attendance & Punctuality'
                        ];
                        $criteriaDescriptions = [
                            1 => 'Understanding of role, procedures, and standards',
                            2 => 'Accuracy, thoroughness, and attention to detail',
                            3 => 'Interaction with customers and handling complaints',
                            4 => 'Working with colleagues and supporting team goals',
                            5 => 'Reliability and adherence to schedule'
                        ];
                        for ($i = 1; $i <= 5; $i++):
                            ?>
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-semibold"><?= $i ?></span>
                                            <p class="font-semibold"><?= $criteriaLabels[$i] ?></p>
                                        </div>
                                        <p class="text-sm text-gray-600 ml-8"><?= $criteriaDescriptions[$i] ?></p>
                                    </div>
                                    <select name="criteria_score[<?= $i ?>]" class="border rounded px-3 py-1 text-sm"
                                        onchange="updateOverallScore(<?= $emp['id'] ?>)" id="criteria<?= $i ?>_<?= $emp['id'] ?>">
                                        <option value="1">1 - Needs Improvement</option>
                                        <option value="2">2 - Developing</option>
                                        <option value="3" selected>3 - Meets Expectations</option>
                                        <option value="4">4 - Exceeds Expectations</option>
                                        <option value="5">5 - Outstanding</option>
                                    </select>
                                </div>
                                <textarea name="criteria_comment[<?= $i ?>]" class="w-full border rounded p-3 text-sm" rows="2"
                                    placeholder="Add your comments here..."></textarea>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- Overall Score -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Overall Performance Score</p>
                                <p class="text-sm text-gray-600">Average rating across all criteria</p>
                            </div>
                            <div class="text-center">
                                <span class="text-3xl font-bold" style="color: var(--color-primary)"
                                    id="overallScore_<?= $emp['id'] ?>">3.0</span>
                                <span class="text-gray-500">/5.0</span>
                                <input type="hidden" name="overall_score" id="overall_score_input_<?= $emp['id'] ?>"
                                    value="3.0">
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t">
                            <span class="text-sm font-medium">Interpretation:</span>
                            <span class="text-sm ml-2" id="scoreInterpretation_<?= $emp['id'] ?>">Meets Expectations</span>
                            <input type="hidden" name="interpretation" id="interpretation_input_<?= $emp['id'] ?>"
                                value="Meets Expectations">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button"
                            class="px-5 py-2 border rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"
                            onclick="closeModal('performanceEvaluationModal<?= $emp['id'] ?>')">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-check mr-2 text-xs"></i>Submit Evaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Regular Employment Confirmation Modals -->
<?php if (!empty($recentEvaluations)): ?>
    <?php foreach ($recentEvaluations as $eval): ?>

        <?php
        $hasExistingPip = !empty($eval['pip_id']);
        $employeeNeedImprovement = ($eval['status'] === 'Improvement');
        ?>

        <div id="regularModal_<?= $eval['employee_id'] ?>" class="modal">
            <div
                class="modal-content bg-white rounded-[--radius-card] shadow-[--shadow-card] w-full max-w-lg mx-4 p-6 relative">
                <button type="button" onclick="closeModal('regularModal_<?= $eval['employee_id'] ?>')"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>

                <h3 class="text-xl font-semibold mb-4" style="color: var(--color-primary)">Confirm Regular Employment</h3>

                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <p class="text-sm text-green-700">
                        Performance evaluation meets/exceeds expectations. Employee qualifies for regular status.
                    </p>
                </div>

                <form method="POST" action="/make-regular-employee" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="employee_id" value="<?= $eval['employee_id'] ?>">
                    <input type="hidden" name="evaluation_id" value="<?= $eval['evaluation_id'] ?>">

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-medium mb-2">Evaluation Summary</p>
                        <p class="text-sm">Overall Score: <span
                                class="font-bold text-green-600"><?= number_format($eval['overall_score'], 1) ?>/5.0</span></p>
                        <p class="text-sm mt-1"><?= htmlspecialchars($eval['interpretation']) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Effective Date</label>
                        <input type="date" name="effective_date" class="profile-input w-full p-2 border rounded"
                            value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">New Employment Details</label>
                        <select name="employment_type" class="profile-input w-full p-2 border rounded" required>
                            <option value="Regular Full-Time">Regular Full-Time</option>
                            <option value="Regular Part-Time">Regular Part-Time</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Manager Comments</label>
                        <textarea name="manager_comments" class="profile-input w-full p-2 border rounded" rows="3"
                            required><?= htmlspecialchars($eval['full_name']) ?> has demonstrated excellent performance during probationary period.</textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button type="button" class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                            onclick="closeModal('regularModal_<?= $eval['employee_id'] ?>')">Cancel</button>
                        <button type="submit" class="btn-primary px-4 py-2">
                            <i class="fas fa-user-check mr-2"></i>Confirm Regular Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="pipModal_<?= $eval['employee_id'] ?>" class="modal">
            <div
                class="modal-content bg-white rounded-[--radius-card] shadow-[--shadow-card] w-full max-w-lg mx-4 p-6 relative">
                <button type="button" onclick="closeModal('pipModal_<?= $eval['employee_id'] ?>')"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>

                <h3 class="text-xl font-semibold mb-4" style="color: var(--color-primary)">
                    <?= $hasExistingPip ? 'Update Performance Improvement Plan' : 'Performance Improvement Plan' ?>
                </h3>

                <div
                    class="<?= $hasExistingPip ? 'bg-blue-50 border-blue-400' : 'bg-yellow-50 border-yellow-400' ?> border-l-4 p-4 mb-6">
                    <p class="text-sm <?= $hasExistingPip ? 'text-blue-700' : 'text-yellow-700' ?>">
                        <?php if ($hasExistingPip): ?>
                            Existing PIP found. Update the improvement plan for this employee.
                        <?php else: ?>
                            Performance evaluation indicates need for improvement. Create PIP for employee.
                        <?php endif; ?>
                    </p>
                </div>

                <form method="POST"
                    action="<?= $employeeNeedImprovement ? '/update-performance-improvement-plan' : '/create-performance-improvement-plan' ?>"
                    class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="employee_id" value="<?= $eval['employee_id'] ?>">
                    <input type="hidden" name="evaluation_id" value="<?= $eval['evaluation_id'] ?>">
                    <?= $employeeNeedImprovement ? '<input type="hidden" value="PATCH" name="__method">' : '' ?>
                    <input type="hidden" name="pip_id" value="<?= $eval['pip_id'] ?>">

                    <div>
                        <label class="block text-sm font-medium mb-1">Employee</label>
                        <input type="text" class="profile-input w-full p-2 border rounded bg-gray-50"
                            value="<?= htmlspecialchars($eval['full_name']) ?>" readonly>
                    </div>

                    <!-- DYNAMIC Rating Input Section with actual scores from database -->
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-medium mb-2">Performance Ratings</p>

                        <?php
                        $criteriaLabels = [
                            1 => 'Job Knowledge',
                            2 => 'Quality of Work',
                            3 => 'Customer Service',
                            4 => 'Teamwork & Collaboration',
                            5 => 'Attendance & Punctuality'
                        ];

                        // Calculate total for display
                        $totalScore = 0;
                        for ($i = 1; $i <= 5; $i++):
                            $scoreField = "criteria_{$i}_score";
                            $currentScore = $eval[$scoreField] ?? 3;
                            $totalScore += $currentScore;
                            ?>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm"><?= $criteriaLabels[$i] ?>:</span>
                                <select name="criteria_score[<?= $i ?>]" class="border rounded px-3 py-1 text-sm w-24">
                                    <option value="1" <?= $currentScore == 1 ? 'selected' : '' ?>>1 - Needs Improvement</option>
                                    <option value="2" <?= $currentScore == 2 ? 'selected' : '' ?>>2 - Developing</option>
                                    <option value="3" <?= $currentScore == 3 ? 'selected' : '' ?>>3 - Meets Expectations</option>
                                    <option value="4" <?= $currentScore == 4 ? 'selected' : '' ?>>4 - Exceeds Expectations</option>
                                    <option value="5" <?= $currentScore == 5 ? 'selected' : '' ?>>5 - Outstanding</option>
                                </select>
                            </div>
                        <?php endfor; ?>

                        <div class="mt-3 pt-3 border-t">
                            <?php
                            $averageScore = $totalScore / 5;
                            $scoreColor = 'text-blue-600';
                            if ($averageScore < 2.5) {
                                $scoreColor = 'text-red-600';
                            } elseif ($averageScore >= 4) {
                                $scoreColor = 'text-green-600';
                            }
                            ?>
                            <p class="text-sm">Overall Score: <span class="font-bold <?= $scoreColor ?>">
                                    <?= number_format($averageScore, 1) ?>/5.0
                                </span></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Areas for Improvement</label>
                        <textarea name="improvement_areas" class="profile-input w-full p-2 border rounded" rows="3"
                            required><?= htmlspecialchars($eval['improvement_areas'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Specific Goals</label>
                        <input type="text" name="goal1" class="profile-input w-full p-2 border rounded mb-2"
                            placeholder="Goal 1: Improve customer service ratings to 4.0+"
                            value="<?= htmlspecialchars($eval['goal1'] ?? '') ?>">
                        <input type="text" name="goal2" class="profile-input w-full p-2 border rounded mb-2"
                            placeholder="Goal 2: Reduce errors in order processing"
                            value="<?= htmlspecialchars($eval['goal2'] ?? '') ?>">
                        <input type="text" name="goal3" class="profile-input w-full p-2 border rounded"
                            placeholder="Goal 3: Improve punctuality (no more than 1 late arrival)"
                            value="<?= htmlspecialchars($eval['goal3'] ?? '') ?>">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Start Date</label>
                            <input type="date" name="pip_start_date" class="profile-input w-full p-2 border rounded"
                                value="<?= $hasExistingPip ? $eval['pip_start_date'] : date('Y-m-d') ?>"
                                min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">End Date</label>
                            <input type="date" name="pip_end_date" class="profile-input w-full p-2 border rounded"
                                value="<?= $hasExistingPip ? $eval['pip_end_date'] : date('Y-m-d', strtotime('+30 days')) ?>"
                                required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button type="button" class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                            onclick="closeModal('pipModal_<?= $eval['employee_id'] ?>')">
                            Cancel
                        </button>

                        <?php if ($employeeNeedImprovement): ?>
                            <button type="submit" class="btn-primary">
                                Update PIP
                            </button>
                        <?php else: ?>
                            <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg">
                                Create PIP
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>