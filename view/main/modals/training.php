<div id="newTrainingModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Schedule Training Session</h3>
            <button onclick="closeModal('newTrainingModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Training Title</label>
                <input type="text" class="profile-input w-full p-2 border rounded"
                    placeholder="e.g., Food Safety Certification">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Trainer</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Select trainer</option>
                    <option>Sarah Johnson</option>
                    <option>Mike Chen</option>
                    <option>Lisa Wong</option>
                    <option>External Trainer</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" class="profile-input w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Time</label>
                    <input type="time" class="profile-input w-full p-2 border rounded">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Duration (hours)</label>
                <input type="number" class="profile-input w-full p-2 border rounded" min="0.5" step="0.5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Location/Room</label>
                <input type="text" class="profile-input w-full p-2 border rounded" placeholder="e.g., Training Room A">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Maximum Attendees</label>
                <input type="number" class="profile-input w-full p-2 border rounded" min="1">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="3"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Target Participants</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>All Departments</option>
                    <option>Restaurant Staff</option>
                    <option>Kitchen Staff</option>
                    <option>Management</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('newTrainingModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Schedule Training</button>
            </div>
        </form>
    </div>
</div>