<div class="tab-content" id="payroll-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Payroll Management</h2>
            <p class="text-gray-600 mt-1">Process payroll and manage compensation</p>
        </div>
        <button class="btn-success" onclick="processPayroll()">
            <i class="fas fa-calculator mr-2"></i>Process Payroll
        </button>
    </div>

    <!-- Payroll Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-sm text-gray-600">Payroll Period</p>
            <p class="text-lg font-semibold">Mar 1-15, 2024</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Total Gross Pay</p>
            <p class="text-2xl font-bold text-primary">₱1,245,000</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Net Pay</p>
            <p class="text-2xl font-bold text-green-600">₱1,089,375</p>
        </div>
    </div>

    <!-- Payroll List -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Payroll Summary</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3">Employee</th>
                        <th class="text-left py-3">Regular Hours</th>
                        <th class="text-left py-3">Overtime</th>
                        <th class="text-left py-3">Gross Pay</th>
                        <th class="text-left py-3">Deductions</th>
                        <th class="text-left py-3">Net Pay</th>
                        <th class="text-left py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-3">Grace Lee</td>
                        <td class="py-3">80</td>
                        <td class="py-3">5</td>
                        <td class="py-3">₱25,500</td>
                        <td class="py-3">₱3,825</td>
                        <td class="py-3 font-medium">₱21,675</td>
                        <td class="py-3"><span class="status-badge bg-green-100 text-green-800">Processed</span></td>
                    </tr>
                    <tr class="border-b border-gray-100">
                        <td class="py-3">James Davis</td>
                        <td class="py-3">80</td>
                        <td class="py-3">0</td>
                        <td class="py-3">₱18,400</td>
                        <td class="py-3">₱2,760</td>
                        <td class="py-3 font-medium">₱15,640</td>
                        <td class="py-3"><span class="status-badge bg-yellow-100 text-yellow-800">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>