<?php if (!empty($probationaryEmployees)): ?>
    <?php foreach ($probationaryEmployees as $emp): ?>
        <form method="POST" action="/save-performance-evaluation" id="evaluationForm_<?= $emp['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">
            <input type="hidden" name="review_period_start" value="<?= $emp['start_date'] ?>">
            <input type="hidden" name="review_period_end"
                value="<?= date('Y-m-d', strtotime($emp['hired_date'] . ' + 90 days')) ?>">
            <input type="hidden" name="review_type" value="90-Day Probationary Review">

            <div id="performanceEvaluationModal<?= $emp['id'] ?>"
                class="modal fixed inset-0 bg-gray-900 bg-opacity-40 overflow-y-auto h-full w-full hidden"
                style="backdrop-filter: blur(4px);">

                <!-- modal content here -->
                <div class="relative top-20 mx-auto p-6 max-w-2xl">
                    <div class="bg-white rounded-[--radius-card] shadow-[--shadow-card] overflow-hidden">

                        <!-- Header -->
                        <div class="flex justify-between items-center px-6 py-5 border-b border-[--color-border-light]">
                            <h3 class="text-xl font-semibold" style="color: var(--color-primary)">Performance Evaluation</h3>
                            <button onclick="closeModal('performanceEvaluationModal<?= $emp['id'] ?>')"
                                class="text-gray-400 hover:text-gray-600 transition-colors rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-100">
                                <i class="fas fa-times text-xl"></i> </button>
                        </div> <!-- Employee Info Card -->
                        <div class="p-6 border-b border-[--color-border-light]"
                            style="background-color: var(--color-bg-input-alt);">
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide" style="color: var(--color-primary)">
                                        Employee</p>
                                    <p class="font-semibold text-gray-800 mt-1"><?= htmlspecialchars($emp['full_name']) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide" style="color: var(--color-primary)">
                                        Position</p>
                                    <p class="font-semibold text-gray-800 mt-1"> <?= htmlspecialchars($emp['position']) ?> <span
                                            class="text-xs font-normal text-gray-500 ml-2">(Probationary)</span> </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide" style="color: var(--color-primary)">
                                        Review Period</p>
                                    <p class="font-semibold text-gray-800 mt-1">
                                        <?= $emp['start_date'] ? date('M d', strtotime($emp['start_date'])) : '-' ?> -
                                        <?= $emp['hired_date'] ? date('M d, Y', strtotime($emp['hired_date'] . ' + 90 days')) : '-' ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide" style="color: var(--color-primary)">
                                        Review Type</p>
                                    <p class="font-semibold text-gray-800 mt-1">90-Day Probationary Review</p>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Criteria -->
                        <div class="p-6 max-h-96 overflow-y-auto" style="background-color: var(--color-bg-body);">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <span class="w-1 h-5 rounded-full mr-2" style="background-color: var(--color-primary);"></span>
                                Evaluation Criteria
                            </h4>

                            <div class="space-y-4">
                                <?php for ($i = 1; $i <= 5; $i++):
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
                                    ?>
                                    <div
                                        class="bg-white rounded-[--radius-card] p-5 shadow-[--shadow-soft] hover:shadow-md transition-shadow">
                                        <div class="flex flex-wrap gap-4 items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span
                                                        class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold text-white"
                                                        style="background-color: var(--color-primary);"><?= $i ?></span>
                                                    <p class="font-semibold text-gray-800"><?= $criteriaLabels[$i] ?></p>
                                                </div>
                                                <p class="text-sm text-gray-500 ml-8"><?= $criteriaDescriptions[$i] ?></p>
                                            </div>

                                            <select name="criteria_score[<?= $i ?>]"
                                                class="border border-[--color-border-input] rounded-[--radius-md] px-3 py-2 text-sm bg-[--color-bg-input] text-gray-700 focus:outline-none focus:ring-0"
                                                style="box-shadow: var(--focus-ring);" id="criteria<?= $i ?>_<?= $emp['id'] ?>"
                                                onchange="updateOverallScore(<?= $emp['id'] ?>)">
                                                <option value="1">1 - Needs Improvement</option>
                                                <option value="2">2 - Developing</option>
                                                <option value="3" selected>3 - Meets Expectations</option>
                                                <option value="4">4 - Exceeds Expectations</option>
                                                <option value="5">5 - Outstanding</option>
                                            </select>
                                        </div>

                                        <textarea name="criteria_comment[<?= $i ?>]"
                                            class="w-full border border-[--color-border-input] rounded-[--radius-md] p-3 text-sm bg-[--color-bg-input] focus:outline-none focus:ring-0"
                                            style="box-shadow: var(--focus-ring);" rows="2"
                                            placeholder="Add your comments here..."></textarea>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Overall Score -->
                        <div class="p-6 border-y border-[--color-border-light]"
                            style="background-color: var(--color-bg-input-alt);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium" style="color: var(--color-primary)">Overall Performance Score
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">Average rating across all criteria</p>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold" style="color: var(--color-primary)"
                                        id="overallScore_<?= $emp['id'] ?>">3.0</div>
                                    <input type="hidden" name="overall_score" id="overall_score_input_<?= $emp['id'] ?>"
                                        value="3.0">
                                    <div class="text-xs text-gray-400 mt-0.5">out of 5.0</div>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-[--color-border-light]">
                                <div class="flex items-center">
                                    <span class="text-xs font-medium uppercase tracking-wide mr-2"
                                        style="color: var(--color-primary);">Interpretation:</span>
                                    <span class="text-sm font-medium text-gray-700"
                                        id="scoreInterpretation_<?= $emp['id'] ?>">Meets Expectations</span>
                                    <input type="hidden" name="interpretation" id="interpretation_input_<?= $emp['id'] ?>"
                                        value="Meets Expectations">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 px-6 py-4 bg-white">
                            <button type="button"
                                class="px-5 py-2 border border-[--color-border-input] rounded-[--radius-pill] text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"
                                onclick="closeModal('performanceEvaluationModal<?= $emp['id'] ?>')">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-white text-sm font-medium rounded-[--radius-pill] transition-colors hover:bg-[--color-primary-hover] shadow-sm"
                                style="background-color: var(--color-primary);">
                                <i class="fas fa-check mr-2 text-xs"></i>Submit Evaluation
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Regular Employment Confirmation Modal -->
<div id="regularModal_<?= $modalData['employee_id'] ?>"
    class="modal fixed inset-0 bg-gray-900 bg-opacity-40 overflow-y-auto h-full w-full hidden"
    style="backdrop-filter: blur(4px);">
    <div class="relative top-20 mx-auto p-6 max-w-2xl">
        <div class="bg-white rounded-[--radius-card] shadow-[--shadow-card] overflow-hidden">
            <div class="flex justify-between items-center px-6 py-5 border-b border-[--color-border-light]">
                <h3 class="text-xl font-semibold" style="color: var(--color-primary)">Confirm Regular Employment</h3>
                <button onclick="closeModal('regularModal_<?= $modalData['employee_id'] ?>')"
                    class="text-gray-400 hover:text-gray-600 transition-colors rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="bg-green-50 border-l-4 border-green-400 p-4 m-6">
                <div class="flex">
                    <div class="shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            Performance evaluation meets/exceeds expectations. Employee qualifies for regular status.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="/make-regular-employee" class="p-6 pt-0 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="employee_id" value="<?= $modalData['employee_id'] ?>">
                <input type="hidden" name="evaluation_id" value="<?= $modalData['evaluation_id'] ?>">

                <div class="border rounded-lg p-4 bg-gray-50">
                    <p class="font-medium mb-2">Evaluation Summary</p>
                    <p class="text-sm">Overall Score: <span class="font-bold text-green-600">
                            <?= number_format($modalData['overall_score'], 1) ?>/5.0
                        </span></p>
                    <p class="text-sm mt-1">
                        <?= htmlspecialchars($modalData['interpretation']) ?>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Effective Date</label>
                    <input type="date" name="effective_date" class="profile-input w-full p-2 border rounded"
                        value="<?= date('Y-m-d') ?>" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">New Employment Details</label>
                    <div class="space-y-2">
                        <select name="employment_type" class="profile-input w-full p-2 border rounded" required>
                            <option value="Regular Full-Time">Regular Full-Time</option>
                            <option value="Regular Part-Time">Regular Part-Time</option>
                        </select>
                        <input type="text" name="updated_salary" class="profile-input w-full p-2 border rounded"
                            placeholder="Updated salary/rate (if applicable)">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Manager Comments</label>
                    <textarea name="manager_comments" class="profile-input w-full p-2 border rounded" rows="3"
                        required><?= htmlspecialchars($modalData['full_name']) ?> has demonstrated excellent performance during probationary period.</textarea>
                </div>

                <div class="flex items-center gap-3 border-t pt-4">
                    <input type="checkbox" name="notify_employee" id="notifyRegular_<?= $modalData['employee_id'] ?>"
                        class="w-4 h-4" checked>
                    <label for="notifyRegular_<?= $modalData['employee_id'] ?>" class="text-sm font-medium">Send
                        confirmation and updated contract to employee</label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                        onclick="closeModal('regularModal_<?= $modalData['employee_id'] ?>')">Cancel</button>
                    <button type="submit" class="btn-primary px-4 py-2">
                        <i class="fas fa-user-check mr-2"></i>Confirm Regular Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Performance Improvement Plan Modal -->
<div id="pipModal_<?= $modalData['employee_id'] ?>"
    class="modal fixed inset-0 bg-gray-900 bg-opacity-40 overflow-y-auto h-full w-full hidden"
    style="backdrop-filter: blur(4px);">
    <div class="relative top-20 mx-auto p-6 max-w-2xl">
        <div class="bg-white rounded-[--radius-card] shadow-[--shadow-card] overflow-hidden">
            <div class="flex justify-between items-center px-6 py-5 border-b border-[--color-border-light]">
                <h3 class="text-xl font-semibold" style="color: var(--color-primary)">Performance Improvement Plan (PIP)
                </h3>
                <button onclick="closeModal('pipModal_<?= $modalData['employee_id'] ?>')"
                    class="text-gray-400 hover:text-gray-600 transition-colors rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 m-6">
                <div class="flex">
                    <div class="shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Performance evaluation indicates need for improvement. Create PIP for employee.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="/create-performance-improvement-plan" class="p-6 pt-0 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="employee_id" value="<?= $modalData['employee_id'] ?>">
                <input type="hidden" name="evaluation_id" value="<?= $modalData['evaluation_id'] ?>">

                <div>
                    <label class="block text-sm font-medium mb-1">Employee</label>
                    <input type="text" class="profile-input w-full p-2 border rounded"
                        value="<?= htmlspecialchars($modalData['full_name']) ?>" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Areas for Improvement</label>
                    <textarea name="improvement_areas" class="profile-input w-full p-2 border rounded" rows="3"
                        placeholder="List specific areas needing improvement..." required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Specific Goals</label>
                    <div class="space-y-2">
                        <input type="text" name="goal1" class="profile-input w-full p-2 border rounded"
                            placeholder="Goal 1: Improve customer service ratings to 4.0+">
                        <input type="text" name="goal2" class="profile-input w-full p-2 border rounded"
                            placeholder="Goal 2: Reduce errors in order processing">
                        <input type="text" name="goal3" class="profile-input w-full p-2 border rounded"
                            placeholder="Goal 3: Improve punctuality (no more than 1 late arrival)">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" name="pip_start_date" class="profile-input w-full p-2 border rounded"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" name="pip_end_date" class="profile-input w-full p-2 border rounded"
                            value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Required Actions</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="actions[]" value="weekly_checkin" class="mr-2"> <span
                                class="text-sm">Weekly check-in meetings</span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="actions[]" value="additional_training" class="mr-2"> <span
                                class="text-sm">Additional training sessions</span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="actions[]" value="shadow_employee" class="mr-2"> <span
                                class="text-sm">Shadow senior employee</span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="actions[]" value="daily_tracking" class="mr-2"> <span
                                class="text-sm">Daily performance tracking</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Consequences if not met</label>
                    <textarea name="consequences" class="profile-input w-full p-2 border rounded"
                        rows="2">Failure to meet improvement goals may result in extension of probationary period or further action.</textarea>
                </div>

                <div class="flex items-center gap-3 border-t pt-4">
                    <input type="checkbox" name="notify_employee" id="notifyPIP_<?= $modalData['employee_id'] ?>"
                        class="w-4 h-4" checked>
                    <label for="notifyPIP_<?= $modalData['employee_id'] ?>" class="text-sm font-medium">Notify employee
                        and schedule PIP meeting</label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                        onclick="closeModal('pipModal_<?= $modalData['employee_id'] ?>')">Cancel</button>
                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-file-signature mr-2"></i>Create PIP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>