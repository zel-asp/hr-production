<div id="newCompetencyModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Add New Competency</h3>
            <button onclick="closeModal('newCompetencyModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Competency Name</label>
                <input type="text" class="profile-input" placeholder="e.g., Customer Service">
            </div>
            <div>
                <label class="block text-sm font-medium">Category</label>
                <select class="profile-input">
                    <option>Core</option>
                    <option>Technical</option>
                    <option>Leadership</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea class="profile-input" rows="3"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('newCompetencyModal')">Cancel</button>
                <button type="submit" class="btn-primary">Add Competency</button>
            </div>
        </form>
    </div>
</div>