<!-- Schedule Management Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- My Schedule Card (List View) -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">My Schedule</h3>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full border border-blue-200">
                    Week <?= $scheduleWeekNumber ?>
                </span>
                <span class="text-xs text-gray-400"><?= $scheduleWeekLabel ?></span>
            </div>
        </div>

        <div class="p-6">

            <!-- Schedule List -->
            <div class="space-y-2">
                <?php foreach ($scheduleWeekDays as $day): ?>
                    <div
                        class="flex items-center p-3 <?= $day['is_today'] ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-100' ?> rounded-lg border hover:border-gray-200 transition-colors">
                        <div class="w-24">
                            <span class="text-sm font-medium text-gray-800">
                                <?= $day['day_name'] ?>
                            </span>
                            <span class="text-xs text-gray-400 block">
                                <?= $day['formatted_date'] ?>
                            </span>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <?php if ($day['has_shift'] && $day['shift']): ?>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-2 h-2 <?= getShiftDotColor($day['shift']['shift_name']) ?> rounded-full"></span>
                                    <span class="text-sm text-gray-600">
                                        <?= formatScheduleTime($day['shift']['start_time']) ?> -
                                        <?= formatScheduleTime($day['shift']['end_time']) ?>
                                    </span>
                                    <span
                                        class="text-xs <?= getShiftBadgeColor($day['shift']['shift_name']) ?> px-2 py-0.5 rounded-full">
                                        <?= htmlspecialchars($day['shift']['shift_name'] ?? 'Shift') ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    <span class="text-sm text-gray-400">Day Off</span>
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Rest Day</span>
                                </div>
                            <?php endif; ?>

                            <?php if ($day['is_today']): ?>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Today</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty(array_filter($scheduleWeekDays, fn($day) => $day['has_shift']))): ?>
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-calendar-alt text-3xl mb-2 text-gray-300"></i>
                    <p class="text-sm">No schedule found for this week</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Shifts Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Shifts</h3>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                <?php if (!empty($scheduleUpcomingShifts)): ?>
                    <?php foreach ($scheduleUpcomingShifts as $shift): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 <?= getShiftBadgeColor($shift['shift_name']) ?> bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i
                                        class="fas <?= getShiftIcon($shift['shift_name']) ?> <?= str_replace('bg-', 'text-', getShiftBadgeColor($shift['shift_name'])) ?> text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800"><?= $shift['display_label'] ?></p>
                                    <p class="text-xs text-gray-400">
                                        <?= formatScheduleTime($shift['start_time']) ?> -
                                        <?= formatScheduleTime($shift['end_time']) ?>
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs <?= getShiftBadgeColor($shift['shift_name']) ?> px-2 py-1 rounded-full">
                                <?= htmlspecialchars($shift['shift_name'] ?? 'Shift') ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-calendar-day text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">No upcoming shifts</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Shift Swap Request Form -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-semibold text-gray-800">Request Shift Swap</h3>
    </div>

    <div class="p-6">
        <form class="space-y-4" method="POST" action="/request-swap-schedule">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Swap With -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Swap With <span
                        class="text-red-400">*</span></label>
                <select name="swap_with_employee_id" id="swap_with_employee_id"
                    class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200"
                    required>
                    <option value="">Select colleague to swap with</option>
                    <?php if (!empty($shiftEmployees)): ?>
                        <?php foreach ($shiftEmployees as $colleague): ?>
                            <option value="<?= htmlspecialchars($colleague['id']) ?>"
                                data-shift-id="<?= $colleague['shift_id'] ?? '' ?>"
                                data-shift-display="<?= htmlspecialchars($colleague['shift_display'] ?? $colleague['current_shift'] ?? 'No Shift') ?>">
                                <?= htmlspecialchars($colleague['full_name']) ?>
                                (<?= htmlspecialchars($colleague['current_shift'] ?? 'No Shift') ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Date to Swap -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date to Swap <span
                        class="text-red-400">*</span></label>
                <input type="date" name="swap_date" id="swap_date"
                    class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200"
                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                    required>
            </div>

            <!-- Your Current Shift -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Current Shift</label>
                <div class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                    <?php
                    if (!empty($shiftEmployeeShift)) {
                        $shiftTime = date('g:i A', strtotime($shiftEmployeeShift['start_time'])) . ' - ' .
                            date('g:i A', strtotime($shiftEmployeeShift['end_time']));
                        echo htmlspecialchars($shiftEmployeeShift['shift_name'] . ' • ' . $shiftTime);
                    } else {
                        echo 'No shift assigned';
                    }
                    ?>
                </div>
                <input type="hidden" name="requester_shift_id" value="<?= $shiftEmployeeShift['id'] ?? '' ?>">
            </div>

            <!-- Their Current Shift (Dynamic) -->
            <div id="their_shift_container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-1">Their Current Shift</label>
                <div class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600"
                    id="their_shift_display">
                </div>
                <input type="hidden" name="swap_with_shift_id" id="swap_with_shift_id" value="">
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Swap <span
                        class="text-red-400">*</span></label>
                <textarea name="reason" rows="3"
                    class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all duration-200"
                    placeholder="Please provide reason for shift swap..." required></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="this.form.reset()"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Submit Swap Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Recent Swap Requests -->
<div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Recent Swap Requests</h3>
        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Last 5 requests</span>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 text-xs font-medium text-gray-500">Date</th>
                        <th class="text-left py-2 text-xs font-medium text-gray-500">Swap With</th>
                        <th class="text-left py-2 text-xs font-medium text-gray-500">Your Shift</th>
                        <th class="text-left py-2 text-xs font-medium text-gray-500">Their Shift</th>
                        <th class="text-left py-2 text-xs font-medium text-gray-500">Reason</th>
                        <th class="text-left py-2 text-xs font-medium text-gray-500">Status</th>
                        <?php if (!empty($receivedSwapRequests)): ?>
                            <th class="text-left py-2 text-xs font-medium text-gray-500">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sentSwapRequests)): ?>
                        <?php foreach ($sentSwapRequests as $request): ?>
                            <tr class="border-b border-gray-50">
                                <td class="py-2 text-sm">
                                    <?= $request['formatted_swap_date'] ?>
                                </td>
                                <td class="py-2 text-sm font-medium text-gray-800">
                                    <?= htmlspecialchars($request['swap_with_name']) ?>
                                </td>
                                <td class="py-2 text-sm">
                                    <?= $request['requester_shift_name'] ?? 'N/A' ?>
                                </td>
                                <td class="py-2 text-sm">
                                    <?= $request['swap_with_shift_name'] ?? 'N/A' ?>
                                </td>
                                <td class="py-2 text-sm">
                                    <?= htmlspecialchars($request['reason']) ?>
                                </td>
                                <td class="py-2">
                                    <span class="px-2 py-1 <?= $request['status_class'] ?> text-xs rounded-full">
                                        <?= $request['status'] ?>
                                    </span>
                                </td>
                                <?php if (!empty($receivedSwapRequests)): ?>
                                    <td class="py-2"></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= !empty($receivedSwapRequests) ? '7' : '6' ?>"
                                class="py-4 text-center text-sm text-gray-400">
                                No swap requests found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($receivedSwapRequests)): ?>
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Pending Approval</h4>
                <?php foreach ($receivedSwapRequests as $request): ?>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                <?= htmlspecialchars($request['requester_name']) ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= $request['formatted_swap_date'] ?> •
                                <?= $request['requester_shift_name'] ?? 'N/A' ?> →
                                <?= $request['swap_with_shift_name'] ?? 'N/A' ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <?= htmlspecialchars($request['reason']) ?>
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="/approve-swap-request" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" name="action" value="approve"
                                    class="px-3 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Approve
                                </button>
                            </form>
                            <form method="POST" action="/reject-swap-request" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" name="action" value="reject"
                                    class="px-3 py-1 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Show their shift when employee is selected
    document.getElementById('swap_with_employee_id').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const shiftId = selected.dataset.shiftId;
        const shiftDisplay = selected.dataset.shiftDisplay;
        const container = document.getElementById('their_shift_container');
        const display = document.getElementById('their_shift_display');
        const hiddenInput = document.getElementById('swap_with_shift_id');

        if (shiftId && shiftId !== '') {
            container.style.display = 'block';
            display.textContent = shiftDisplay;
            hiddenInput.value = shiftId;
        } else {
            container.style.display = 'none';
            hiddenInput.value = '';
        }
    });
</script>