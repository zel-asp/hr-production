<div id="newJobModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Create New Job Posting</h3>
            <button onclick="closeModal('newJobModal')" class="text-gray-500 hover:text-gray-700">
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

        <form method="POST" action="/postJob" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Job Position</label>
                <input type="text" class="profile-input" value="Restaurant Server" name="position">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select class="profile-input" name="department">
                    <option value="restaurant" selected>Restaurant</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" class="profile-input" value="Main Dining Room" name="location">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                <select class="profile-input" name="shift">
                    <option value="evening" selected>Evening (4pm-12am)</option>
                    <option value="gy">Grave yard (12am-6am)</option>

                    <option value="morning">Morning (6am-3pm)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range</label>
                <input type="text" class="profile-input" value="$15-20/hr + tips" name="salary">
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('newJobModal')">Cancel</button>
                <button name="post" type="submit" class="btn-primary" onclick="submitJobPosting(event)">Create
                    Posting</button>
            </div>
        </form>
    </div>
</div>