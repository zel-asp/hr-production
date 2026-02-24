<div class="tab-content" id="shift-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Shift & Schedule Management</h2>
            <p class="text-gray-600 mt-1">Create and manage employee schedules</p>
        </div>
        <button class="btn-primary" onclick="openModal('createScheduleModal')">
            <i class="fas fa-plus mr-2"></i>Create Schedule
        </button>
    </div>

    <!-- Week View -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Weekly Schedule</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 border rounded-lg text-sm">← Prev</button>
                <button class="px-3 py-1 border rounded-lg text-sm">Next →</button>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div>
                    <p class="font-medium">Grace Lee - Restaurant Server</p>
                    <p class="text-sm">Morning Shift (7am-3pm)</p>
                </div>
                <span class="schedule-shift">Mon, Wed, Fri</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div>
                    <p class="font-medium">James Davis - Line Cook</p>
                    <p class="text-sm">Evening Shift (3pm-11pm)</p>
                </div>
                <span class="schedule-shift">Tue, Thu, Sat</span>
            </div>
        </div>
    </div>

    <!-- Shift Swap Requests -->
    <div class="card p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Shift Swap Requests</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium">Maria Garcia → James Davis</p>
                    <p class="text-sm text-gray-600">Wed, Mar 20 • Morning to Evening</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-green-500 text-white rounded text-sm">Approve</button>
                    <button class="px-3 py-1 bg-red-500 text-white rounded text-sm">Deny</button>
                </div>
            </div>
        </div>
    </div>
</div>