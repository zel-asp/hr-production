<div id="newClaimModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Submit New Claim</h3>
            <button onclick="closeModal('newClaimModal')" class="text-gray-500 hover:text-gray-700">
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
                <label class="block text-sm font-medium mb-1">Claim Type</label>
                <select class="profile-input w-full p-2 border rounded">
                    <option>Meal Allowance</option>
                    <option>Transportation</option>
                    <option>Medical Reimbursement</option>
                    <option>Training Expense</option>
                    <option>Uniform Allowance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Amount</label>
                <input type="number" class="profile-input w-full p-2 border rounded" placeholder="₱0.00">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Date of Expense</label>
                <input type="date" class="profile-input w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="2"
                    placeholder="Describe the expense..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Attach Receipt</label>
                <input type="file" class="profile-input w-full p-2 border rounded" accept="image/*,.pdf">
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('newClaimModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Submit Claim</button>
            </div>
        </form>
    </div>
</div>