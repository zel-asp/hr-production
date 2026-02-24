<div id="salaryReviewModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">New Salary Review</h3>
            <button onclick="closeModal('salaryReviewModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Employee</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Select employee</option>
                    <option>Grace Lee - Restaurant Server</option>
                    <option>James Davis - Line Cook</option>
                    <option>Maria Garcia - Sous Chef</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Review Date</label>
                <input type="date" class="profile-input w-full p-2 border rounded">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Current Salary</label>
                    <input type="text" class="profile-input w-full p-2 border rounded bg-gray-100" readonly
                        value="₱21,000">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Proposed Salary</label>
                    <input type="text" class="profile-input w-full p-2 border rounded" placeholder="₱0.00">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Increase Percentage</label>
                <input type="number" class="profile-input w-full p-2 border rounded" min="0" max="100" step="0.1"
                    placeholder="e.g., 10%">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Effective Date</label>
                <input type="date" class="profile-input w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Reason for Increase</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Annual Review</option>
                    <option>Promotion</option>
                    <option>Market Adjustment</option>
                    <option>Merit Increase</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Comments</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="3"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('salaryReviewModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Submit Review</button>
            </div>
        </form>
    </div>
</div>