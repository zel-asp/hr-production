<div class="tab-content" id="hmo-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">HMO & Benefits Administration</h2>
            <p class="text-gray-600 mt-1">Manage employee health insurance and benefits</p>
        </div>
        <button class="btn-primary" onclick="openModal('enrollBenefitModal')">
            <i class="fas fa-plus mr-2"></i>Enroll Employee
        </button>
    </div>

    <!-- Benefits Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-sm text-gray-600">Enrolled</p>
            <p class="text-2xl font-bold text-primary">142</p>
            <p class="text-xs text-gray-500">out of 156 employees</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Pending Enrollment</p>
            <p class="text-2xl font-bold text-yellow-600">14</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Claims This Month</p>
            <p class="text-2xl font-bold text-blue-600">23</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Premium Due</p>
            <p class="text-2xl font-bold text-red-600">₱45,200</p>
        </div>
    </div>

    <!-- HMO Coverage -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Coverage Summary</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium">Maxicare</p>
                        <p class="text-xs text-gray-600">Principal + 2 dependents</p>
                    </div>
                    <span class="compliance-badge badge-compliant">Active</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium">Dental Coverage</p>
                        <p class="text-xs text-gray-600">Annual limit: ₱10,000</p>
                    </div>
                    <span class="compliance-badge badge-compliant">Active</span>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Expiring Soon</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded">
                    <div>
                        <p class="font-medium">Grace Lee</p>
                        <p class="text-xs text-gray-600">HMO coverage ends Apr 30, 2024</p>
                    </div>
                    <button class="text-primary text-sm hover:underline">Renew</button>
                </div>
            </div>
        </div>
    </div>
</div>