<div id="assessmentModal" class="modal">
    <div class="modal-content max-w-3xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Competency Assessment Form</h3>
            <button onclick="closeModal('assessmentModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="competencyAssessmentForm" method="POST" action="/competency-assessment">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <h4 class="font-semibold text-blue-800 mb-2">Step 1: Prepare Assessment</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Employee</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" name="assigned_to"
                            id="employeeSelect" required onchange="showCompetencyName()">
                            <option value="">Select employee with completed training</option>
                            <?php if (!empty($employeesForDropdown)): ?>
                                <?php foreach ($employeesForDropdown as $employee): ?>
                                    <option value="<?= $employee['id'] ?>"
                                        data-competency-name="<?= htmlspecialchars($employee['competency_name']) ?>"
                                        data-competency-id="<?= $employee['competency_id'] ?>">
                                        <?= htmlspecialchars($employee['full_name']) ?> -
                                        <?= htmlspecialchars($employee['position']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No employees available</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Competency to Assess</label>
                        <input type="text" class="profile-input bg-gray-100" id="competencyNameDisplay" readonly
                            placeholder="Competency name will appear here">
                        <input type="hidden" name="competency_id" id="competencyIdInput">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Assessment Date</label>
                        <input type="date" class="profile-input" name="assessment_date" value="<?= date('Y-m-d') ?>"
                            min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Evaluator</label>
                        <select class="profile-input" name="assessor_id" required>
                            <option value="">Select Evaluator</option>
                            <?php foreach ($evaluators as $evaluator): ?>
                                <option value="<?= $evaluator['id'] ?>">
                                    <?= htmlspecialchars($evaluator['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Evaluation Form -->
            <div class="mb-4">
                <h4 class="font-semibold mb-2">Step 2: Evaluate Competency</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium">Proficiency Level</label>
                        <select class="profile-input" name="proficiency_level" required>
                            <option value="">Select Level</option>
                            <option value="1">1 - Novice (Needs supervision)</option>
                            <option value="2">2 - Developing (Can perform basic tasks)</option>
                            <option value="3">3 - Intermediate (Works independently)</option>
                            <option value="4">4 - Advanced (Guides others)</option>
                            <option value="5">5 - Expert (Strategic advisor)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Assessment Notes</label>
                        <textarea class="profile-input" name="assessment_notes" rows="3"
                            placeholder="Provide specific examples and observations..." required></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Evaluation -->
            <div class="flex justify-end gap-2 mb-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('assessmentModal')">Cancel</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check mr-2"></i>Submit Evaluation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Competency Assessment Modals (one for each assessment) -->
<?php if (!empty($recentAssessments)): ?>
    <?php foreach ($recentAssessments as $assessment):
        $proficiencyLevels = ['1 - Beginner', '2 - Developing', '3 - Intermediate', '4 - Advanced', '5 - Expert'];
        $assessedLevel = $proficiencyLevels[$assessment['proficiency_level'] - 1] ?? $assessment['proficiency_level'] . ' - Unknown';
        $requiredLevel = $proficiencyLevels[$assessment['required_level'] - 1] ?? $assessment['required_level'] . ' - Unknown';
        $hasGap = $assessment['gap_level'] > 0;
        $modalId = 'gapModal' . $assessment['id'];

        // Employee email is already in the query
        $employeeEmail = $assessment['email'] ?? '';
        ?>

        <!-- Gap Analysis Modal for <?= htmlspecialchars($assessment['employee_name']) ?> - <?= htmlspecialchars($assessment['competency_name']) ?> -->
        <div id="<?= $modalId ?>" class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-semibold">
                                <?= strtoupper(substr($assessment['employee_name'], 0, 1)) ?>
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Competency Gap Analysis</h3>
                            <p class="text-sm text-gray-500">
                                <?= htmlspecialchars($assessment['employee_name']) ?> •
                                <?= htmlspecialchars($assessment['competency_name']) ?>
                            </p>
                        </div>
                    </div>
                    <button onclick="closeModal('<?= $modalId ?>')"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <?php if ($hasGap): ?>
                        <!-- Gap Identified View -->
                        <div class="space-y-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex gap-3">
                                    <div class="shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800">
                                            Competency gap detected
                                        </p>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            <?= htmlspecialchars($assessment['employee_name']) ?> requires
                                            improvement in
                                            <?= htmlspecialchars($assessment['competency_name']) ?>
                                            (Level
                                            <?= $assessment['proficiency_level'] ?> of
                                            <?= $assessment['required_level'] ?>)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Current vs Required Comparison -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Competency Gap Analysis</h4>
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Current Level:
                                                <?= $assessment['proficiency_level'] ?>/5
                                            </span>
                                            <span>
                                                <?= $assessedLevel ?>
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-500 h-2 rounded-full"
                                                style="width: <?= ($assessment['proficiency_level'] / 5) * 100 ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Required Level:
                                                <?= $assessment['required_level'] ?>/5
                                            </span>
                                            <span>
                                                <?= $requiredLevel ?>
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full"
                                                style="width: <?= ($assessment['required_level'] / 5) * 100 ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <span class="text-xs font-medium <?= $assessment['gap_class'] ?>">
                                            Gap:
                                            <?= $assessment['gap_level'] ?> level
                                            <?= $assessment['gap_level'] > 1 ? 's' : '' ?> below requirement
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <form action="/assign-interventions" method="POST" id="interventionForm<?= $assessment['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="assessment_id" value="<?= $assessment['id'] ?>">
                                <input type="hidden" name="employee_id" value="<?= $assessment['employee_id'] ?>">
                                <input type="hidden" name="employee_name"
                                    value="<?= htmlspecialchars($assessment['employee_name']) ?>">
                                <input type="hidden" name="competency_name"
                                    value="<?= htmlspecialchars($assessment['competency_name']) ?>">
                                <input type="hidden" name="current_level" value="<?= $assessment['proficiency_level'] ?>">
                                <input type="hidden" name="required_level" value="<?= $assessment['required_level'] ?>">

                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                                        <i class="fas fa-graduation-cap text-blue-500"></i>
                                        Recommended Learning Interventions
                                    </h4>
                                    <div class="space-y-2" id="interventionsList<?= $assessment['id'] ?>">
                                        <!-- Intervention 1: Excellence Training -->
                                        <div
                                            class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="flex items-start gap-3">
                                                <input type="checkbox" name="interventions[]" value="excellence"
                                                    class="mt-1 rounded border-gray-300 text-gray-600 focus:ring-gray-200 competency-checkbox-<?= $assessment['id'] ?>">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800">
                                                        <?= htmlspecialchars($assessment['competency_name']) ?> Excellence Training
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5">Online course • 4 hours</p>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 mt-2">
                                                        Recommended
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Intervention 3: Mentoring Program (Optional additional intervention) -->
                                        <div
                                            class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="flex items-start gap-3">
                                                <input type="checkbox" name="interventions[]" value="mentoring"
                                                    class="mt-1 rounded border-gray-300 text-gray-600 focus:ring-gray-200 competency-checkbox-<?= $assessment['id'] ?>">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800">One-on-One Mentoring Program</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">Mentoring • 3 months</p>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 mt-2">
                                                        Intensive
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                    <!-- Email Link for Gap View -->
                                    <?php if (!empty($employeeEmail)):
                                        // Prepare intervention list for email
                                        $interventionList = "- " . htmlspecialchars($assessment['competency_name']) . " Excellence Training (Online course • 4 hours)\n";
                                        $interventionList .= "- One-on-One Mentoring Program (Mentoring • 3 months)";

                                        $gapEmailSubject = "Competency Gap Identified - " . htmlspecialchars($assessment['competency_name']);
                                        $gapEmailBody = "Dear " . htmlspecialchars($assessment['employee_name']) . ",\n\n" .
                                            "During your recent competency assessment, we identified a gap in " . htmlspecialchars($assessment['competency_name']) . ".\n\n" .
                                            "Current Level: Level " . $assessment['proficiency_level'] . " (" . $assessedLevel . ")\n" .
                                            "Required Level: Level " . $assessment['required_level'] . " (" . $requiredLevel . ")\n" .
                                            "Gap: " . $assessment['gap_level'] . " level(s) below requirement\n\n" .
                                            "To help you improve, we have assigned the following learning interventions:\n" .
                                            $interventionList . "\n\n" .
                                            "Please complete these interventions by the assigned due dates. Our HR team will reach out with more details.\n\n" .
                                            "Best regards,\nHR Department";
                                        ?>
                                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($employeeEmail) ?>&su=<?= urlencode($gapEmailSubject) ?>&body=<?= urlencode($gapEmailBody) ?>"
                                            target="_blank"
                                            class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-indigo-600 hover:bg-indigo-50 rounded-md text-sm font-medium transition border border-gray-200"
                                            title="Send email via Gmail">
                                            <i class="fas fa-envelope text-xs"></i>
                                            <span>Email Employee</span>
                                        </a>
                                    <?php endif; ?>

                                    <div class="flex gap-2">
                                        <button type="button" onclick="closeModal('<?= $modalId ?>')"
                                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn-primary"
                                            onclick="return validateInterventions(<?= $assessment['id'] ?>)">
                                            <i class="fas fa-graduation-cap"></i>
                                            Assign Interventions
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <?php else: ?>
                        <!-- No Gap View -->
                        <div class="space-y-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex gap-3">
                                    <div class="shrink-0">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-800">No competency gaps identified
                                        </p>
                                        <p class="text-sm text-green-700 mt-1">
                                            <?= htmlspecialchars($assessment['employee_name']) ?> has met all
                                            required standards for
                                            <?= htmlspecialchars($assessment['competency_name']) ?>.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Summary -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Performance Summary</h4>
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Achieved Level:
                                                <?= $assessment['proficiency_level'] ?>/5
                                            </span>
                                            <span>
                                                <?= $assessedLevel ?>
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full"
                                                style="width: <?= ($assessment['proficiency_level'] / 5) * 100 ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Required Level:
                                                <?= $assessment['required_level'] ?>/5
                                            </span>
                                            <span>
                                                <?= $requiredLevel ?>
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full"
                                                style="width: <?= ($assessment['required_level'] / 5) * 100 ?>%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recognition Message and Email -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-envelope text-blue-500"></i>
                                    Recognition Message
                                </h4>

                                <?php
                                $recognitionEmailSubject = "Congratulations on Your Competency Achievement!";
                                $recognitionEmailBody = "Dear " . htmlspecialchars($assessment['employee_name']) . ",\n\n" .
                                    "Congratulations! Your recent competency assessment for " . htmlspecialchars($assessment['competency_name']) . " has met all required standards.\n\n" .
                                    "Achieved Level: Level " . $assessment['proficiency_level'] . " (" . $assessedLevel . ")\n" .
                                    "Required Level: Level " . $assessment['required_level'] . " (" . $requiredLevel . ")\n\n" .
                                    "Your performance demonstrates strong proficiency in this area. Keep up the excellent work!\n\n" .
                                    "Best regards,\nHR Department";
                                ?>

                                <textarea id="notificationMessage<?= $assessment['id'] ?>"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400"
                                    rows="4" readonly><?= $recognitionEmailBody ?></textarea>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                <!-- Email Link for No Gap View -->
                                <?php if (!empty($employeeEmail)): ?>
                                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($employeeEmail) ?>&su=<?= urlencode($recognitionEmailSubject) ?>&body=<?= urlencode($recognitionEmailBody) ?>"
                                        target="_blank"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-indigo-600 hover:bg-indigo-50 rounded-md text-sm font-medium transition border border-gray-200"
                                        title="Send email via Gmail">
                                        <i class="fas fa-envelope text-xs"></i>
                                        <span>Send Recognition Email</span>
                                    </a>
                                <?php endif; ?>

                                <button onclick="closeModal('<?= $modalId ?>')"
                                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                    Close
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>