<!-- Gap Analysis Modal -->
<div id="gapModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Competency Gap Analysis</h3>
            <button onclick="closeModal('gapModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Gap Identified View -->
        <div id="gapIdentified" class="space-y-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Competency gap detected for Grace Lee in Customer Service
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold mb-2">Recommended Learning Interventions</h4>
                <div class="space-y-2">
                    <div class="border rounded-lg p-3">
                        <div class="flex items-start">
                            <input type="checkbox" class="mt-1 mr-2">
                            <div>
                                <p class="font-medium">Customer Service Excellence Training</p>
                                <p class="text-sm text-gray-600">Online course - 4 hours</p>
                                <span class="status-badge bg-blue-100 text-blue-800">Recommended</span>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded-lg p-3">
                        <div class="flex items-start">
                            <input type="checkbox" class="mt-1 mr-2">
                            <div>
                                <p class="font-medium">Shadow Senior Staff</p>
                                <p class="text-sm text-gray-600">On-the-job training - 2 weeks</p>
                                <span class="status-badge bg-green-100 text-green-800">Available</span>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded-lg p-3">
                        <div class="flex items-start">
                            <input type="checkbox" class="mt-1 mr-2">
                            <div>
                                <p class="font-medium">Communication Skills Workshop</p>
                                <p class="text-sm text-gray-600">In-person session - 1 day</p>
                                <span class="status-badge bg-purple-100 text-purple-800">Optional</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button class="btn-primary" onclick="assignInterventions()">
                    <i class="fas fa-graduation-cap mr-2"></i>Assign Interventions
                </button>
            </div>
        </div>

        <!-- No Gap View -->
        <div id="noGap" class="hidden space-y-4">
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            No competency gaps identified for this assessment.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold mb-2">Notification to Employee</h4>
                <textarea class="profile-input" rows="4" readonly>
Dear Grace Lee,

Congratulations! Your recent competency assessment for Customer Service has met all required standards. Your performance demonstrates strong proficiency in this area.

Keep up the excellent work!

Best regards,
HR Department
                </textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button class="btn-primary" onclick="sendNotification()">
                    <i class="fas fa-envelope mr-2"></i>Send Notification
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="tab-content" id="competency-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Competency Assessment System</h2>
            <p class="text-gray-600 mt-1">Assess, evaluate, and manage employee competencies</p>
        </div>
        <button class="btn-primary" onclick="openModal('assessmentModal')">
            <i class="fas fa-clipboard-list mr-2"></i>New Assessment
        </button>
    </div>

    <!-- Assessment Queue -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold">Pending Assessments</h3>
                <span class="text-2xl font-bold text-primary">8</span>
            </div>
            <p class="text-sm text-gray-600">Awaiting evaluation</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold">Completed This Month</h3>
                <span class="text-2xl font-bold text-green-600">12</span>
            </div>
            <p class="text-sm text-gray-600">+3 from last month</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold">Gaps Identified</h3>
                <span class="text-2xl font-bold text-yellow-600">4</span>
            </div>
            <p class="text-sm text-gray-600">Requiring intervention</p>
        </div>
    </div>

    <!-- Recent Assessments -->
    <div class="card p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Recent Assessments</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4">Employee</th>
                        <th class="text-left py-3 px-4">Competency</th>
                        <th class="text-left py-3 px-4">Assessed Level</th>
                        <th class="text-left py-3 px-4">Required Level</th>
                        <th class="text-left py-3 px-4">Gap</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">Grace Lee</td>
                        <td class="py-3 px-4">Customer Service</td>
                        <td class="py-3 px-4">2 - Developing</td>
                        <td class="py-3 px-4">3 - Intermediate</td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-yellow-100 text-yellow-800">1 level gap</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-yellow-100 text-yellow-800">Gap Identified</span>
                        </td>
                        <td class="py-3 px-4">
                            <button class="text-primary hover:text-primary-dark" onclick="openGapAnalysis(true)">
                                <i class="fas fa-chart-line mr-1"></i>Review Gap
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">John Smith</td>
                        <td class="py-3 px-4">Food Safety</td>
                        <td class="py-3 px-4">4 - Advanced</td>
                        <td class="py-3 px-4">4 - Advanced</td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-green-100 text-green-800">No gap</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="py-3 px-4">
                            <button class="text-primary hover:text-primary-dark" onclick="openGapAnalysis(false)">
                                <i class="fas fa-bell mr-1"></i>Notify
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Learning Interventions -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Active Learning Interventions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-semibold">Customer Service Excellence</h4>
                    <span class="status-badge bg-blue-100 text-blue-800">In Progress</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Assigned to: Grace Lee, Maria Garcia</p>
                <div class="flex justify-between text-sm">
                    <span>Due: March 15, 2026</span>
                    <span class="font-medium">2/4 sessions completed</span>
                </div>
                <div class="progress-bar mt-2">
                    <div class="progress-fill blue" style="width: 50%"></div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-semibold">Food Safety Certification</h4>
                    <span class="status-badge bg-green-100 text-green-800">Completed</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Assigned to: John Smith</p>
                <div class="flex justify-between text-sm">
                    <span>Completed: Feb 20, 2026</span>
                    <span class="font-medium text-green-600">Certified</span>
                </div>
            </div>
        </div>
    </div>
</div>