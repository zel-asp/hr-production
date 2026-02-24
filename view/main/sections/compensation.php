<div class="tab-content" id="compensation-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Compensation Planning</h2>
            <p class="text-gray-600 mt-1">Manage salary structures and compensation reviews</p>
        </div>
        <button class="btn-primary" onclick="openModal('salaryReviewModal')">
            <i class="fas fa-plus mr-2"></i>New Salary Review
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Salary Bands -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Salary Bands by Position</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Restaurant Server</span>
                        <span>₱18K - ₱25K</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: 70%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Line Cook</span>
                        <span>₱20K - ₱28K</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill blue" style="width: 65%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Sous Chef</span>
                        <span>₱35K - ₱50K</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill yellow" style="width: 60%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Reviews -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Upcoming Compensation Reviews</h3>
            <div class="space-y-3">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium">Grace Lee</p>
                            <p class="text-sm text-gray-600">Restaurant Server</p>
                        </div>
                        <span class="text-sm text-orange-600">Due Apr 15</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Current: ₱21,000 • Proposed: ₱23,500</p>
                </div>
            </div>
        </div>
    </div>
</div>