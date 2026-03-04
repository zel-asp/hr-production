<div class="tab-content" id="succession-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Succession Planning</h2>
            <p class="text-gray-500 text-sm mt-1">Employees ready for promotion or advancement</p>
        </div>
    </div>

    <!-- Succession Pipeline Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Ready for Promotion</p>
            <p class="text-2xl font-bold text-gray-800">24</p>
            <p class="text-xs text-gray-400 mt-1">Completed all requirements</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Training Completed</p>
            <p class="text-2xl font-bold text-gray-800">156</p>
            <p class="text-xs text-gray-400 mt-1">Total certifications earned</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">No Competency Gaps</p>
            <p class="text-2xl font-bold text-gray-800">18</p>
            <p class="text-xs text-gray-400 mt-1">Fully qualified employees</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Open Positions</p>
            <p class="text-2xl font-bold text-gray-800">7</p>
            <p class="text-xs text-gray-400 mt-1">Available for promotion</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter by:</span>
                <select
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option>All Departments</option>
                    <option>Management</option>
                    <option>Restaurant</option>
                    <option>Kitchen</option>
                    <option>Housekeeping</option>
                    <option>Admin</option>
                </select>
                <select
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option>All Positions</option>
                    <option>Entry Level</option>
                    <option>Supervisory</option>
                    <option>Managerial</option>
                    <option>Executive</option>
                </select>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <span class="text-sm text-gray-500">Sort by:</span>
                <select
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option>Readiness Score</option>
                    <option>Department</option>
                    <option>Name</option>
                    <option>Date Completed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Succession Candidates List -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Succession Candidates</h3>
            <span
                class="text-xs font-medium bg-green-50 text-green-700 px-2.5 py-1 rounded-full border border-green-200">
                24 candidates ready
            </span>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <!-- Candidate 1 - Fully Qualified -->
                <div
                    class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200 bg-linear-to-r from-white to-gray-50">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h4 class="text-lg font-semibold text-gray-800">Maria Garcia</h4>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>Ready Now
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Sous Chef • Kitchen Department</p>

                                <!-- Completion Badges -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded-md text-xs border border-green-200">
                                        <i class="fas fa-check-circle text-xs"></i> All Tasks Complete
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs border border-blue-200">
                                        <i class="fas fa-graduation-cap text-xs"></i> 5/5 Trainings
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-md text-xs border border-purple-200">
                                        <i class="fas fa-star text-xs"></i> No Competency Gaps
                                    </span>
                                </div>

                                <!-- Qualification Details -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fas fa-tasks text-gray-400 w-4"></i>
                                        <span class="text-gray-600">Tasks: <span class="font-medium text-gray-800">12/12
                                                completed</span></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fas fa-clock text-gray-400 w-4"></i>
                                        <span class="text-gray-600">Last Training: <span
                                                class="font-medium text-gray-800">Mar 2026</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex lg:flex-col gap-2 lg:min-w-[140px]">
                            <button
                                class="w-full px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center justify-center gap-2"
                                onclick="openModal('promoteModal_1')">
                                <i class="fas fa-arrow-up"></i>
                                Promote
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Candidate 2 - Fully Qualified -->
                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h4 class="text-lg font-semibold text-gray-800">John Smith</h4>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>Ready Now
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Restaurant Manager • F&B Department</p>

                                <!-- Completion Badges -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded-md text-xs border border-green-200">
                                        <i class="fas fa-check-circle text-xs"></i> All Tasks Complete
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs border border-blue-200">
                                        <i class="fas fa-graduation-cap text-xs"></i> 8/8 Trainings
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-md text-xs border border-purple-200">
                                        <i class="fas fa-star text-xs"></i> No Competency Gaps
                                    </span>
                                </div>

                                <!-- Qualification Details -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fas fa-tasks text-gray-400 w-4"></i>
                                        <span class="text-gray-600">Tasks: <span class="font-medium text-gray-800">15/15
                                                completed</span></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fas fa-clock text-gray-400 w-4"></i>
                                        <span class="text-gray-600">Last Training: <span
                                                class="font-medium text-gray-800">Feb 2026</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex lg:flex-col gap-2 lg:min-w-[140px]">
                            <button
                                class="w-full px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center justify-center gap-2"
                                onclick="openModal('promoteModal_2')">
                                <i class="fas fa-arrow-up"></i>
                                Promote
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Candidate 3 - Qualified but with notes -->
                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-amber-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h4 class="text-lg font-semibold text-gray-800">Lisa Kim</h4>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-clock mr-1 text-xs"></i>Ready in 3 months
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Pastry Chef • Kitchen Department</p>

                                <!-- Completion Badges -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded-md text-xs border border-green-200">
                                        <i class="fas fa-check-circle text-xs"></i> All Tasks Complete
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs border border-blue-200">
                                        <i class="fas fa-graduation-cap text-xs"></i> 6/7 Trainings
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-md text-xs border border-purple-200">
                                        <i class="fas fa-star text-xs"></i> No Competency Gaps
                                    </span>
                                </div>

                                <!-- Qualification Details -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fas fa-tasks text-gray-400 w-4"></i>
                                        <span class="text-gray-600">Tasks: <span class="font-medium text-gray-800">10/10
                                                completed</span></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fas fa-clock text-gray-400 w-4"></i>
                                        <span class="text-gray-600">Pending: <span class="font-medium text-amber-600">1
                                                training</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex lg:flex-col gap-2 lg:min-w-[140px]">
                            <button
                                class="w-full px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed flex items-center justify-center gap-2"
                                disabled>
                                <i class="fas fa-arrow-up"></i>
                                Not Ready
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500">Showing <span class="font-medium">1-3</span> of <span
                        class="font-medium">24</span> candidates</p>
                <div class="flex items-center gap-2">
                    <button
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors"
                        disabled>
                        <i class="fas fa-chevron-left text-xs"></i>
                    </button>
                    <button
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-gray-800 text-white">1</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">3</button>
                    <span class="text-gray-400">...</span>
                    <button
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">8</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Footer -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Top Departments with Ready Candidates</h4>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Kitchen</span>
                    <span class="font-medium text-gray-800">8 candidates</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Restaurant</span>
                    <span class="font-medium text-gray-800">7 candidates</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Management</span>
                    <span class="font-medium text-gray-800">4 candidates</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Readiness Summary</h4>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Ready Now</span>
                    <span class="font-medium text-green-600">12 candidates</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Ready in 1-3 months</span>
                    <span class="font-medium text-amber-600">8 candidates</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Ready in 3-6 months</span>
                    <span class="font-medium text-blue-600">4 candidates</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Next Steps</h4>
            <ul class="space-y-2 text-xs text-gray-600">
                <li class="flex items-center gap-2">
                    <i class="fas fa-arrow-right text-gray-400"></i>
                    7 positions waiting for promotion
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-arrow-right text-gray-400"></i>
                    3 succession interviews scheduled
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-arrow-right text-gray-400"></i>
                    2 candidates in final review
                </li>
            </ul>
        </div>
    </div>
    </di v>