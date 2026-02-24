<div id="createScheduleModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Create Weekly Schedule</h3>
            <button onclick="closeModal('createScheduleModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Schedule Week</label>
                <input type="week" class="profile-input w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Department</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>All Departments</option>
                    <option>Restaurant</option>
                    <option>Kitchen</option>
                    <option>Housekeeping</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Schedule Template</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Default Week</option>
                    <option>Weekend Focus</option>
                    <option>Weekday Focus</option>
                    <option>Holiday Schedule</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Notes</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="2"
                    placeholder="Special instructions..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('createScheduleModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Create Schedule</button>
            </div>
        </form>
    </div>
</div>