<div id="addEmployeeModal" class="modal">
    <div class="modal-content max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Add New Employee</h3>
            <button onclick="closeModal('addEmployeeModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Employee ID</label>
                    <input type="text" class="profile-input w-full p-2 border rounded" placeholder="EMP001">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select class="profile-input w-full p-2 border rounded">
                        <option>Active</option>
                        <option>Probationary</option>
                        <option>Part-time</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name</label>
                    <input type="text" class="profile-input w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Last Name</label>
                    <input type="text" class="profile-input w-full p-2 border rounded" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" class="profile-input w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="tel" class="profile-input w-full p-2 border rounded">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Position</label>
                    <input type="text" class="profile-input w-full p-2 border rounded"
                        placeholder="e.g., Restaurant Server">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Department</label>
                    <select class="profile-input w-full p-2 border rounded">
                        <option>Restaurant</option>
                        <option>Kitchen</option>
                        <option>Housekeeping</option>
                        <option>Management</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Hire Date</label>
                    <input type="date" class="profile-input w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Employment Type</label>
                    <select class="profile-input w-full p-2 border rounded">
                        <option>Full-time</option>
                        <option>Part-time</option>
                        <option>Seasonal</option>
                        <option>Contract</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Address</label>
                <textarea class="profile-input w-full p-2 border rounded" rows="2"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Emergency Contact</label>
                <input type="text" class="profile-input w-full p-2 border rounded" placeholder="Name and Phone">
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('addEmployeeModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Add Employee</button>
            </div>
        </form>
    </div>
</div>