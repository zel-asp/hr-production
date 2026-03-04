<div class="tab-content" id="hmo-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">HMO & Benefits Administration</h2>
            <p class="text-gray-500 text-sm mt-1">Manage employee health insurance and benefits</p>
        </div>
        <button class="btn-primary" onclick="openModal('enrollBenefitModal')">
            <i class="fas fa-plus"></i>
            Enroll Employee
        </button>
    </div>

    <!-- Benefits Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Enrolled</p>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-bold text-gray-800">142</p>
                <span class="text-sm text-gray-500">/156</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">91% coverage rate</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pending Enrollment</p>
            <p class="text-2xl font-bold text-gray-800">14</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting requirements</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Claims This Month</p>
            <p class="text-2xl font-bold text-gray-800">23</p>
            <p class="text-xs text-gray-400 mt-1">₱67,200 total value</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Premium Due</p>
            <p class="text-2xl font-bold text-gray-800">₱45.2K</p>
            <p class="text-xs text-gray-400 mt-1">Due Apr 30, 2024</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Coverage Summary -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Coverage Summary</h3>
                <span
                    class="text-xs font-medium bg-white text-gray-600 px-2.5 py-1 rounded-full border border-gray-200">
                    Active Plans
                </span>
            </div>

            <div class="p-6">
                <div class="space-y-3">
                    <!-- Maxicare Card -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Maxicare</h4>
                                    <p class="text-xs text-gray-500 mt-1">Principal + 2 dependents</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-xs text-gray-400">Coverage limit: ₱200,000</span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-400">120 enrolled</span>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                Active
                            </span>
                        </div>
                    </div>

                    <!-- Dental Coverage Card -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tooth text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Dental Coverage</h4>
                                    <p class="text-xs text-gray-500 mt-1">Annual limit: ₱10,000</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-xs text-gray-400">85 enrolled</span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-400">₱4,500 avg claim</span>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                Active
                            </span>
                        </div>
                    </div>

                    <!-- Vision Coverage Card -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-eye text-amber-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Vision Care</h4>
                                    <p class="text-xs text-gray-500 mt-1">Annual eye exam + ₱3,000 frame allowance</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="text-xs text-gray-400">62 enrolled</span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-400">Renews Jun 2024</span>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                Active
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Coverage Footer -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total Monthly Premium</span>
                        <span class="font-medium text-gray-800">₱124,500</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <span class="text-gray-500">Company Share</span>
                        <span class="font-medium text-gray-800">₱87,150 (70%)</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <span class="text-gray-500">Employee Share</span>
                        <span class="font-medium text-gray-800">₱37,350 (30%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiring Soon & Recent Enrollments -->
        <div class="space-y-6">
            <!-- Expiring Soon Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Expiring Soon</h3>
                    <span
                        class="text-xs font-medium bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-full border border-yellow-200">
                        Next 30 days
                    </span>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        <!-- Expiring Item -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Grace Lee</p>
                                    <p class="text-xs text-gray-500">HMO coverage ends Apr 30, 2024</p>
                                </div>
                            </div>
                            <button
                                class="text-sm text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 border border-gray-200">
                                Renew
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">James Davis</p>
                                    <p class="text-xs text-gray-500">Dental coverage ends May 15, 2024</p>
                                </div>
                            </div>
                            <button
                                class="text-sm text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 border border-gray-200">
                                Renew
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Maria Rodriguez</p>
                                    <p class="text-xs text-gray-500">Vision care ends May 22, 2024</p>
                                </div>
                            </div>
                            <button
                                class="text-sm text-gray-600 hover:text-gray-800 bg-white hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors duration-200 border border-gray-200">
                                Renew
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">
                            View all expiring (8)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Enrollments</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                JL
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">John Lee</p>
                                <p class="text-xs text-gray-400">Enrolled in Maxicare • 2 days ago</p>
                            </div>
                            <span class="text-xs text-gray-500">Processed</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                ST
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Sarah Tan</p>
                                <p class="text-xs text-gray-400">Enrolled in Dental • 3 days ago</p>
                            </div>
                            <span class="text-xs text-gray-500">Processed</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-medium">
                                MC
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Mike Chen</p>
                                <p class="text-xs text-gray-400">Enrolled in Vision • 5 days ago</p>
                            </div>
                            <span class="text-xs text-yellow-600">Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-3">
                <button
                    class="p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-left">
                    <i class="fas fa-file-medical text-gray-600 mb-2"></i>
                    <p class="text-sm font-medium text-gray-800">Process Claim</p>
                    <p class="text-xs text-gray-500">3 pending</p>
                </button>
                <button
                    class="p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-left">
                    <i class="fas fa-chart-line text-gray-600 mb-2"></i>
                    <p class="text-sm font-medium text-gray-800">Usage Report</p>
                    <p class="text-xs text-gray-500">View analytics</p>
                </button>
            </div>
        </div>
    </div>

</div>