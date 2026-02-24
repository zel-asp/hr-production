<div id="editJobModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 id="editJobTitle" class="text-xl font-semibold">Edit Job</h3>
            <button onclick="closeModal('editJobModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-4 space-y-2">
                <?php foreach ($_SESSION['error'] as $msg): ?>
                    <div class="flex items-center justify-between bg-red-100 text-red-700 px-4 py-3 rounded shadow-md"
                        role="alert">
                        <span>
                            <?= htmlspecialchars($msg) ?>
                        </span>
                        <button onclick="this.parentElement.remove();"
                            class="ml-4 font-bold text-red-700 hover:text-red-900">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="/update-job" method="POST" class="space-y-4">
            <input type="hidden" name="job_id" id="editJobId">
            <input type="hidden" value="PATCH" name="__method">
            <div>
                <label class="block text-sm font-medium">Job Title</label>
                <input type="text" name="job_title" id="editJobPosition" class="profile-input" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Location</label>
                <input type="text" name="location" id="editJobLocation" class="profile-input" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Shift</label>
                <input type="text" name="shift" id="editJobShift" class="profile-input" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Salary Range</label>
                <input type="text" name="salary_range" id="editJobSalary" class="profile-input">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" onclick="closeModal('editJobModal')">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>