<div id="updateProgressModal1" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Update Progress - Grace Lee</h3>
            <button onclick="closeModal('updateProgressModal1')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div><label class="block text-sm font-medium">Current Progress (%)</label><input type="number"
                    class="profile-input" value="75" min="0" max="100"></div>
            <div><label class="block text-sm font-medium">Notes</label><textarea class="profile-input"
                    rows="3">15% increase achieved so far</textarea></div>
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('updateProgressModal1')">Cancel</button>
                <button type="submit" class="btn-primary" onclick="updateProgress()">Update Progress</button>
            </div>
        </form>
    </div>
</div>