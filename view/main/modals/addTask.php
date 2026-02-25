<!-- Add Task Modals -->
<?php if (!empty($hiredApplicants)): ?>
    <?php foreach ($hiredApplicants as $applicant): ?>
        <div id="addTaskModal<?= $applicant['id'] ?>" class="modal">
            <div class="modal-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Add Task for
                        <?= htmlspecialchars($applicant['full_name']) ?>
                    </h3>
                    <button onclick="closeModal('addTaskModal<?= $applicant['id'] ?>')"
                        class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="/assignTask" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="assigned_to" value="<?= $applicant['id'] ?>">
                    <input type="hidden" name="applicant_name" value="<?= htmlspecialchars($applicant['full_name']) ?>">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                        <select name="task_type" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">Select task type</option>
                            <option value="paperwork">Paperwork</option>
                            <option value="training_module">Training Module</option>
                            <option value="equipment_setup">Equipment Setup</option>
                            <option value="mentor_meeting">Mentor Meeting</option>
                            <option value="certification">Certification</option>
                            <option value="orientation">Orientation</option>
                            <option value="policy_review">Policy Review</option>
                            <option value="uniform_fitting">Uniform Fitting</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Task Description</label>
                        <textarea name="task_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2"
                            placeholder="Enter detailed task description..." required></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" name="due_date" class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign To (Staff)</label>
                        <select name="assigned_staff" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">Select staff member</option>
                            <?php if (!empty($staffMembers)): ?>
                                <?php foreach ($staffMembers as $staff): ?>
                                    <option value="<?= htmlspecialchars($staff['assigned_staff']) ?>">
                                        <?= htmlspecialchars($staff['assigned_staff']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="Sarah Reyes">Sarah Reyes - Manager</option>
                                <option value="Mike Dela Cruz">Mike Dela Cruz - Trainer</option>
                                <option value="Lisa Martinez">Lisa Martinez - HR</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Additional optional fields -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="Not Started">Not Started</option>
                                <option value="Ongoing">Ongoing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select name="department" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">Select (optional)</option>
                                <option value="Kitchen">Kitchen</option>
                                <option value="Service">Service</option>
                                <option value="Management">Management</option>
                                <option value="HR">HR</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('addTaskModal<?= $applicant['id'] ?>')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-plus-circle mr-2"></i>Add Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>