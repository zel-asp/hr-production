<div class="tab-content" id="competency-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Competency Management</h2>
            <p class="text-gray-600 mt-1">Track and assess employee skills and competencies</p>
        </div>
        <button class="btn-primary" onclick="openModal('newCompetencyModal')">
            <i class="fas fa-plus mr-2"></i>Add Competency
        </button>
    </div>

    <!-- Competency Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold">Core Competencies</h3>
                <span class="text-2xl font-bold text-primary">12</span>
            </div>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Customer Service</span>
                        <span class="font-medium">85%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: 85%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Food Safety</span>
                        <span class="font-medium">92%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: 92%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Team Collaboration</span>
                        <span class="font-medium">78%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill yellow" style="width: 78%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold">Technical Skills</h3>
                <span class="text-2xl font-bold text-primary">8</span>
            </div>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>POS Systems</span>
                        <span class="font-medium">88%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: 88%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Knife Skills</span>
                        <span class="font-medium">75%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill yellow" style="width: 75%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Inventory Management</span>
                        <span class="font-medium">70%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill yellow" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold">Leadership Skills</h3>
                <span class="text-2xl font-bold text-primary">6</span>
            </div>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Team Management</span>
                        <span class="font-medium">82%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: 82%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Conflict Resolution</span>
                        <span class="font-medium">68%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill yellow" style="width: 68%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Training & Mentoring</span>
                        <span class="font-medium">71%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill yellow" style="width: 71%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Competency Table -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Employee Competency Matrix</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4">Employee</th>
                        <th class="text-left py-3 px-4">Position</th>
                        <th class="text-left py-3 px-4">Customer Service</th>
                        <th class="text-left py-3 px-4">Food Safety</th>
                        <th class="text-left py-3 px-4">POS Systems</th>
                        <th class="text-left py-3 px-4">Leadership</th>
                        <th class="text-left py-3 px-4">Overall</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-3 px-4 font-medium">Grace Lee</td>
                        <td class="py-3 px-4">Restaurant Server</td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-green-100 text-green-800">Expert</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-green-100 text-green-800">Advanced</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-blue-100 text-blue-800">Intermediate</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge bg-yellow-100 text-yellow-800">Developing</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-medium">85%</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>