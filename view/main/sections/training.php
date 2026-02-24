<div class="tab-content" id="training-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Training Management</h2>
            <p class="text-gray-600 mt-1">Schedule and track employee training sessions</p>
        </div>
        <button class="btn-primary" onclick="openModal('newTrainingModal')">
            <i class="fas fa-plus mr-2"></i>Schedule Training
        </button>
    </div>

    <!-- Upcoming Trainings -->
    <div class="grid grid-cols-1 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Today's Training Sessions</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                    <div>
                        <p class="font-medium">New Hire Orientation</p>
                        <p class="text-sm text-gray-600">10:00 AM - 12:00 PM • Training Room A</p>
                        <p class="text-xs text-gray-500 mt-1">Trainer: Sarah Johnson • 12 attendees</p>
                    </div>
                    <span class="schedule-shift">In Progress</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                    <div>
                        <p class="font-medium">POS System Training</p>
                        <p class="text-sm text-gray-600">2:00 PM - 4:00 PM • Computer Lab</p>
                        <p class="text-xs text-gray-500 mt-1">Trainer: Mike Chen • 8 attendees</p>
                    </div>
                    <span class="schedule-shift">Upcoming</span>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Weekly Training Schedule</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3">Training</th>
                            <th class="text-left py-3">Date</th>
                            <th class="text-left py-3">Time</th>
                            <th class="text-left py-3">Trainer</th>
                            <th class="text-left py-3">Attendees</th>
                            <th class="text-left py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3">Food Safety Refresher</td>
                            <td class="py-3">Mar 18, 2024</td>
                            <td class="py-3">9:00 AM</td>
                            <td class="py-3">Lisa Wong</td>
                            <td class="py-3">15</td>
                            <td class="py-3"><span class="status-badge bg-green-100 text-green-800">Scheduled</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3">Customer Service Excellence</td>
                            <td class="py-3">Mar 19, 2024</td>
                            <td class="py-3">1:00 PM</td>
                            <td class="py-3">Sarah Johnson</td>
                            <td class="py-3">20</td>
                            <td class="py-3"><span class="status-badge bg-green-100 text-green-800">Scheduled</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>