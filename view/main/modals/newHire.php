<div id="addNewHireModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Add New Hire</h3>
            <button onclick="closeModal('addNewHireModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div><label class="block text-sm font-medium">Full Name</label><input type="text" class="profile-input"
                    placeholder="John Doe"></div>
            <div><label class="block text-sm font-medium">Position</label><input type="text" class="profile-input"
                    placeholder="Restaurant Server"></div>
            <div><label class="block text-sm font-medium">Start Date</label><input type="date" class="profile-input">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('addNewHireModal')">Cancel</button>
                <button type="submit" class="btn-primary" onclick="addNewHire()">Add New Hire</button>
            </div>
        </form>
    </div>
</div>