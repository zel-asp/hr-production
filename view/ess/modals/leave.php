<div id="leaveModal" class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-md w-full mx-4 p-6 shadow-xl">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-calendar-plus mr-2 text-primary"></i>request time off
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="leaveModal">
                <i class="fa-solid fa-circle-xmark fa-xl"></i>
            </button>
        </div>

        <form method="POST" action="/leave_request" id="leaveRequestForm" class="space-y-4">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Employee ID from session (employee_record_id is the employees.id) -->
            <input type="hidden" name="employee_record_id"
                value="<?= $_SESSION['employee']['employee_record_id'] ?? '' ?>">

            <!-- Leave Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Leave type</label>
                <select name="leave_type" required
                    class="w-full border border-gray-200 rounded-md p-2.5 text-sm bg-gray-100 focus:ring-2 focus:ring-[#b7d0e8] outline-none">
                    <option value="Annual Leave">Annual leave</option>
                    <option value="Sick Leave">Sick leave</option>
                    <option value="Personal Day">Personal day</option>
                    <option value="Remote Work">Remote work</option>
                    <option value="Emergency leave">Emergency leave</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" name="start_date" required
                        class="w-full border border-gray-200 rounded-md p-2.5 text-sm bg-gray-100 focus:ring-2 focus:ring-[#b7d0e8] outline-none"
                        value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="end_date" required
                        class="w-full border border-gray-200 rounded-md p-2.5 text-sm bg-gray-100 focus:ring-2 focus:ring-[#b7d0e8] outline-none"
                        value="<?= date('Y-m-d', strtotime('+1 day')) ?>" min="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason (optional)</label>
                <textarea name="reason" rows="2"
                    class="w-full border border-gray-200 rounded-md p-2.5 text-sm bg-gray-100 focus:ring-2 focus:ring-[#b7d0e8] outline-none"
                    placeholder="e.g. family trip..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-primary hover:bg-primary-hover text-white text-sm font-medium py-2.5 px-4 rounded-md transition flex-1">
                    <i class="fa-solid fa-paper-plane mr-1"></i>Submit
                </button>
                <button type="button"
                    class="close-modal bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2.5 px-4 rounded-md transition"
                    data-modal="leaveModal">
                    Cancel
                </button>
            </div>
        </form>

        <!-- Leave Balance -->
        <p class="text-xs text-gray-400 mt-3 text-center">
            Remaining annual leave: <span class="font-medium text-primary">18 days</span>
        </p>
    </div>
</div>