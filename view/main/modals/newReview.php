<div id="newReviewModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Start New Performance Review</h3>
            <button onclick="closeModal('newReviewModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div><label class="block text-sm font-medium">Employee</label><select class="profile-input">
                    <option>Grace Lee</option>
                </select></div>
            <div><label class="block text-sm font-medium">Review Type</label><select class="profile-input">
                    <option>Probationary (90-day)</option>
                </select></div>
            <div><label class="block text-sm font-medium">Due Date</label><input type="date" class="profile-input"
                    value="2024-04-15"></div>
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('newReviewModal')">Cancel</button>
                <button type="submit" class="btn-primary" onclick="startNewReview()">Create Review</button>
            </div>
        </form>
    </div>
</div>