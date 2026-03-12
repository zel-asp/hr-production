<div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-gray-700">Leave Requests</h2>
        <button id="openLeaveModalBtn" onclick="openModal('leaveModal')"
            class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
            <i class="fa-solid fa-plus"></i>
            <span>request leave</span>
        </button>
    </div>



    <!-- Summary Stats -->
    <div class="grid grid-cols-3 gap-2 mb-4">
        <div class="bg-amber-50 rounded-lg p-2 text-center">
            <p class="text-lg font-bold text-amber-600"><?= $leaveStats['pending_count'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Pending</p>
        </div>
        <div class="bg-green-50 rounded-lg p-2 text-center">
            <p class="text-lg font-bold text-green-600"><?= $leaveStats['approved_count'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Approved</p>
        </div>
        <div class="bg-red-50 rounded-lg p-2 text-center">
            <p class="text-lg font-bold text-red-600"><?= $leaveStats['rejected_count'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Rejected</p>
        </div>
    </div>

    <p class="text-gray-500 text-sm mb-4">Complete request history (<?= $leaveStats['total_requests'] ?? 0 ?>)</p>

    <div class="space-y-3">
        <?php if (!empty($allLeaveRequests)): ?>
            <?php foreach ($allLeaveRequests as $request): ?>
                <?php
                // Set badge color based on leave type
                $badgeClass = 'bg-[#dbeafe] text-primary-hover';
                $typeDisplay = strtolower(str_replace(' ', '', $request['leave_type']));

                if ($request['leave_type'] == 'Sick Leave') {
                    $badgeClass = 'bg-[#f0e7fc] text-[#5940a0]';
                } elseif ($request['leave_type'] == 'Personal Day') {
                    $badgeClass = 'bg-gray-200 text-gray-800';
                } elseif ($request['leave_type'] == 'Remote Work') {
                    $badgeClass = 'bg-[#e0eee5] text-[#2b6b4a]';
                }

                // Status badge color
                $statusClass = 'text-amber-700 bg-amber-50';
                if ($request['status'] == 'Approved') {
                    $statusClass = 'text-green-700 bg-green-50';
                } elseif ($request['status'] == 'Rejected') {
                    $statusClass = 'text-red-700 bg-red-50';
                } elseif ($request['status'] == 'Cancelled') {
                    $statusClass = 'text-gray-700 bg-gray-100';
                }

                // Calculate date range display
                $startDate = date('M d', strtotime($request['start_date']));
                $endDate = date('M d', strtotime($request['end_date']));
                $dateRange = ($request['start_date'] == $request['end_date'])
                    ? $startDate
                    : $startDate . ' – ' . $endDate;

                // Calculate days
                $days = $request['total_days'] ??
                    (round((strtotime($request['end_date']) - strtotime($request['start_date'])) / (60 * 60 * 24)) + 1);

                // Time ago
                $submitted = strtotime($request['created_at']);
                $timeAgo = '';
                $diff = time() - $submitted;

                if ($diff < 60) {
                    $timeAgo = 'just now';
                } elseif ($diff < 3600) {
                    $mins = floor($diff / 60);
                    $timeAgo = $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
                } elseif ($diff < 86400) {
                    $hours = floor($diff / 3600);
                    $timeAgo = $hours . ' hr' . ($hours > 1 ? 's' : '') . ' ago';
                } elseif ($diff < 604800) {
                    $daysDiff = floor($diff / 86400);
                    $timeAgo = $daysDiff . ' day' . ($daysDiff > 1 ? 's' : '') . ' ago';
                } else {
                    $timeAgo = date('M d, Y', $submitted);
                }
                ?>

                <!-- Request Item -->
                <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md group hover:bg-[#e8eef5] transition">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="<?= $badgeClass ?> text-xs font-medium px-2.5 py-1 rounded-md">
                            <?= $typeDisplay ?>
                        </span>

                        <div class="flex-1">
                            <p class="text-sm font-medium">
                                <?= htmlspecialchars($request['leave_type']) ?> ·
                                <?= $dateRange ?>
                                (<?= $days ?> day<?= $days > 1 ? 's' : '' ?>)
                            </p>
                            <p class="text-xs text-gray-400 flex items-center gap-2">
                                <span><i class="fa-regular fa-clock mr-1"></i>submitted <?= $timeAgo ?></span>
                                <?php if (!empty($request['reason'])): ?>
                                    <span>·
                                        <?= htmlspecialchars(substr($request['reason'], 0, 30)) ?>
                                        <?= strlen($request['reason']) > 30 ? '...' : '' ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <?php if ($request['status'] == 'Pending'): ?>
                            <form action="/remove-request" method="POST">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="__method" value="DELETE">
                                <button
                                    class="opacity-0 group-hover:opacity-100 transition bg-red-600 hover:bg-red-700 text-white p-2 rounded-md text-xs"
                                    title="Delete request" type="submit"
                                    onclick="return confirm('Are you sure you want to delete this request?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <span class="<?= $statusClass ?> px-3 py-1 text-xs font-medium rounded-md">
                            <?= $request['status'] ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
                <p>No leave requests found</p>
                <p class="text-xs mt-1">Click "request leave" to submit your first request</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-6 flex justify-end gap-2">
        <?php if (($leaveStats['pending_count'] ?? 0) > 0): ?>
            <span class="text-xs text-gray-400 mr-auto">
                <i class="fa-regular fa-clock mr-1"></i>
                <?= $leaveStats['pending_count'] ?> pending
            </span>
        <?php endif; ?>
    </div>
</div>