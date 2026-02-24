<div class="tab-content" id="performance-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Performance Management</h2>
            <p class="text-gray-600 mt-1">Comprehensive performance tracking for all employees</p>
        </div>
        <div class="flex gap-2">
            <button class="btn-primary" onclick="openModal('newReviewModal')">
                <i class="fas fa-plus mr-2"></i>New Review
            </button>
        </div>
    </div>

    <!-- Performance Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-sm text-gray-600">Active Reviews</p>
            <p class="text-2xl font-bold text-primary">8</p>
            <p class="text-xs text-gray-500">4 due this week</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Goals On Track</p>
            <p class="text-2xl font-bold text-green-600">12/15</p>
            <p class="text-xs text-gray-500">80% completion rate</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-600">Avg Rating</p>
            <p class="text-2xl font-bold text-yellow-600">4.2/5.0</p>
            <p class="text-xs text-gray-500">Based on 24 reviews</p>
        </div>
    </div>

    <!-- Performance Goals Tracking -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Performance Goals Tracking</h3>

        <!-- Individual Goal with progress -->
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="font-medium">Grace Lee</p>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">On
                            Track</span>
                    </div>
                    <p class="text-sm text-gray-600">Improve upselling by 20%</p>
                </div>
                <button onclick="openModal('goalDetailModal1')" class="text-primary hover:underline text-sm">View
                    Details</button>
            </div>

            <!-- Progress tracking -->
            <div class="mt-3">
                <div class="flex justify-between text-xs mb-1">
                    <span>Progress: <span class="font-medium" id="goal-progress-1">15%
                            increase</span></span>
                    <span><span class="font-medium" id="goal-percentage-1">75</span>% to goal</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill green" id="goal-progress-bar-1" style="width: 75%"></div>
                </div>

                <!-- Milestone tracking - MODIFIED: Made responsive -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-4">
                    <div class="text-center">
                        <div
                            class="w-6 h-6 bg-green-100 rounded-full mx-auto flex items-center justify-center text-green-600 text-xs">
                            ✓</div>
                        <p class="text-xs mt-1">Week 1</p>
                        <p class="text-xs text-gray-500">5%</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="w-6 h-6 bg-green-100 rounded-full mx-auto flex items-center justify-center text-green-600 text-xs">
                            ✓</div>
                        <p class="text-xs mt-1">Week 2</p>
                        <p class="text-xs text-gray-500">10%</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="w-6 h-6 bg-blue-100 rounded-full mx-auto flex items-center justify-center text-blue-600 text-xs">
                            ●</div>
                        <p class="text-xs mt-1">Week 3</p>
                        <p class="text-xs text-gray-500">15%</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="w-6 h-6 bg-gray-100 rounded-full mx-auto flex items-center justify-center text-gray-400 text-xs">
                            ○</div>
                        <p class="text-xs mt-1">Week 4</p>
                        <p class="text-xs text-gray-500">20%</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center text-sm border-t pt-3">
                <span class="text-gray-600">Target: 20% by Apr 30, 2024</span>
                <button onclick="openModal('updateProgressModal1')" class="text-primary hover:underline">
                    <i class="fas fa-pen mr-1"></i>Update Progress
                </button>
            </div>
        </div>

        <!-- Performance Ratings Summary -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="font-medium mb-3">Recent Performance Ratings</h4>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm">Grace Lee</span>
                    <div class="flex items-center">
                        <span class="text-yellow-400">★★★★☆</span>
                        <span class="text-sm ml-2">4.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>