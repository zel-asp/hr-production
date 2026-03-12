<div id="trainingModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Schedule Training Program</h3>
            <button onclick="closeModal('trainingModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="/addTraining" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Hidden field to track if we should notify -->
            <input type="hidden" name="notify" id="notifyFlag" value="false">
            <!-- Training Type -->
            <div>
                <label class="block text-sm font-medium mb-1">Training Type</label>
                <select name="training_type" class="profile-input w-full p-2 border rounded" id="trainingType" required
                    onchange="toggleProviderFields()">
                    <option value="">Select Training Type</option>
                    <option value="internal">Internal Training</option>
                    <option value="external">External Training</option>
                    <option value="certification">Certification Program</option>
                </select>
            </div>

            <!-- Provider dropdown -->
            <div id="providerDropdown" class="hidden">
                <label class="block text-sm font-medium mb-1">Select Training Provider</label>
                <select class="profile-input w-full p-2 border rounded" name="provider_id" required>
                    <option value="">Select Provider</option>
                    <?php foreach ($trainingProviders as $trainingProvider): ?>
                        <option value="<?= $trainingProvider['id'] ?>">
                            <?= htmlspecialchars($trainingProvider['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Training Details -->
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-sm font-medium">Competency to Assess</label>
                    <select class="profile-input" name="competency_id" required>
                        <option value="">Select competency</option>
                        <?php foreach ($competencies as $competency): ?>
                            <option value="<?= $competency['id'] ?>">
                                <?= htmlspecialchars($competency['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Schedule Training -->
            <div class="border-t pt-4">
                <h4 class="font-medium mb-3">Schedule Training</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" name="start_date" class="profile-input w-full p-2 border rounded"
                            min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" name="end_date" class="profile-input w-full p-2 border rounded"
                            min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Time</label>
                        <input type="time" name="start_time" class="profile-input w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Time</label>
                        <input type="time" name="end_time" class="profile-input w-full p-2 border rounded">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium mb-1">Location/Venue</label>
                    <input type="text" name="venue" class="profile-input w-full p-2 border rounded"
                        placeholder="e.g., Training Room A / Zoom link">
                </div>
            </div>

            <!-- Employee Selection -->
            <div>
                <label class="block text-sm font-medium mb-1">Select Employee</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" name="employee_id" required>
                    <option value="">Select employee</option>
                    <?php foreach ($employeeRole as $employee): ?>
                        <option value="<?= $employee['id'] ?>">
                            <?= htmlspecialchars($employee['full_name']) ?> -
                            <?= htmlspecialchars($employee['position']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('trainingModal')">Cancel
                </button>
                <button type="submit" class="btn-primary px-4 py-2">
                    <i class="fas fa-calendar-check mr-2"></i>Schedule
                </button>
            </div>
        </form>
    </div>
</div>