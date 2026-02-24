<div id="enrollBenefitModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Enroll Employee in Benefits</h3>
            <button onclick="closeModal('enrollBenefitModal')" class="text-gray-500 hover:text-gray-700">
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
                <label class="block text-sm font-medium mb-1">Benefit Type</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>HMO - Principal</option>
                    <option>HMO - Principal + 1 Dependent</option>
                    <option>HMO - Principal + 2 Dependents</option>
                    <option>Dental Insurance</option>
                    <option>Life Insurance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Provider</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Maxicare</option>
                    <option>Medicard</option>
                    <option>Intellicare</option>
                    <option>AXA</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Effective Date</label>
                    <input type="date" class="profile-input w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Expiry Date</label>
                    <input type="date" class="profile-input w-full p-2 border rounded">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Coverage Amount</label>
                <input type="text" class="profile-input w-full p-2 border rounded" placeholder="e.g., ₱100,000">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Monthly Premium</label>
                <input type="text" class="profile-input w-full p-2 border rounded" placeholder="₱0.00">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Dependents (if applicable)</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="2"
                    placeholder="List dependents..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('enrollBenefitModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Enroll Employee</button>
            </div>
        </form>
    </div>
</div>