<div id="manualTimeModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Manual Time Entry</h3>
            <button onclick="closeModal('manualTimeModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Employee</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Select employee</option>
                    <option>Grace Lee</option>
                    <option>James Davis</option>
                    <option>Maria Garcia</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Date</label>
                <input type="date" class="profile-input w-full p-2 border rounded">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Time In</label>
                    <input type="time" class="profile-input w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Time Out</label>
                    <input type="time" class="profile-input w-full p-2 border rounded">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Break Duration (minutes)</label>
                <input type="number" class="profile-input w-full p-2 border rounded" value="60" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Reason for Manual Entry</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Missed clock-in</option>
                    <option>System error</option>
                    <option>Forgot to clock</option>
                    <option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Notes</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="2"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('manualTimeModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Save Entry</button>
            </div>
        </form>
    </div>
</div>