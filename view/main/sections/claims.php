<div class="tab-content" id="claims-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Claims & Reimbursement</h2>
            <p class="text-gray-600 mt-1">Manage employee expense claims and reimbursements</p>
        </div>
        <button class="btn-primary" onclick="openModal('newClaimModal')">
            <i class="fas fa-plus mr-2"></i>New Claim
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-sm text-gray-600">Pending Claims</p>
            <p class="text-2xl font-bold text-yellow-600">8</p>
            <p class="text-xs text-gray-500">₱24,500 total</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Approved</p>
            <p class="text-2xl font-bold text-green-600">15</p>
            <p class="text-xs text-gray-500">₱45,200 total</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Processed This Month</p>
            <p class="text-2xl font-bold text-blue-600">23</p>
            <p class="text-xs text-gray-500">₱67,800 total</p>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Claims</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Employee</th>
                        <th class="text-left py-3">Type</th>
                        <th class="text-left py-3">Amount</th>
                        <th class="text-left py-3">Date</th>
                        <th class="text-left py-3">Status</th>
                        <th class="text-left py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-3">Grace Lee</td>
                        <td class="py-3">Meal Allowance</td>
                        <td class="py-3">₱850</td>
                        <td class="py-3">Mar 15, 2024</td>
                        <td class="py-3"><span class="status-badge bg-yellow-100 text-yellow-800">Pending</span></td>
                        <td class="py-3">
                            <button class="text-primary hover:underline mr-2">View</button>
                            <button class="text-green-600 hover:underline">Approve</button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-100">
                        <td class="py-3">James Davis</td>
                        <td class="py-3">Transportation</td>
                        <td class="py-3">₱320</td>
                        <td class="py-3">Mar 14, 2024</td>
                        <td class="py-3"><span class="status-badge bg-green-100 text-green-800">Approved</span></td>
                        <td class="py-3">
                            <button class="text-primary hover:underline">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>