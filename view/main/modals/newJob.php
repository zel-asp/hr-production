<div id="newJobModal" class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Create New Job Posting</h3>
            <button onclick="closeModal('newJobModal')"
                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form method="POST" action="/postJob" class="space-y-4">
                <!-- Job Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Job Position <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200 text-gray-700"
                        value="Restaurant Server" name="position" placeholder="e.g. Senior Chef, Restaurant Manager">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200 text-gray-700 appearance-none"
                        name="department"
                        style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1rem;">
                        <option value="Management" selected>Management</option>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Hotel">Hotel</option>
                        <option value="HR">HR</option>
                        <option value="Logistic">Logistic</option>
                        <option value="Finance">Finance</option>
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200 text-gray-700"
                        value="Main Dining Room" name="location" placeholder="e.g. Downtown, North Wing">
                </div>

                <!-- Shift -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Shift <span class="text-red-500">*</span>
                    </label>
                    <select
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200 text-gray-700 appearance-none"
                        name="shift"
                        style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1rem;">
                        <option value="evening" selected>Evening (4pm-12am)</option>
                        <option value="gy">Graveyard (12am-6am)</option>
                        <option value="morning">Morning (6am-3pm)</option>
                    </select>
                </div>

                <!-- Rate Per Hour -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rate Per Hour (₱) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="10" min="0" name="rate_per_hour" placeholder="e.g. 150"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200 text-gray-700"
                        required>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors duration-200"
                        onclick="closeModal('newJobModal')">
                        Cancel
                    </button>
                    <button name="post" type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg transition-colors duration-200 flex items-center gap-2"
                        onclick="submitJobPosting(event)">
                        <i class="fas fa-plus-circle"></i>
                        Create Posting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>