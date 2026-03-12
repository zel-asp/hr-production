<div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h2 class="text-base font-semibold text-gray-800">Notes & Recognition</h2>
            <?php if ($noteNewCount > 0): ?>
                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                    <?= $noteNewCount ?> new
                </span>
            <?php endif; ?>
        </div>
        <a href="/?tab=announcements" class="text-xs text-gray-500 hover:text-gray-700">
            View all
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-amber-50 rounded-lg p-3 text-center">
            <p class="text-xl font-bold text-amber-600"><?= $noteTotalRecognitions ?></p>
            <p class="text-xs text-gray-500">Recognition</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-3 text-center">
            <p class="text-xl font-bold text-blue-600"><?= $noteTotalNotes ?></p>
            <p class="text-xs text-gray-500">Notes</p>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="space-y-3">
        <?php if (!empty($noteActivities)): ?>
            <?php foreach ($noteActivities as $activity): ?>
                <!-- Activity Item -->
                <div
                    class="flex items-start gap-3 p-3 rounded-lg border <?= $activity['type'] == 'recognition' ? 'bg-amber-50 border-amber-200' : 'bg-gray-50 border-gray-200' ?>">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center <?= $activity['type'] == 'recognition' ? 'bg-amber-200' : 'bg-gray-200' ?>">
                            <i
                                class="fas <?= $activity['type'] == 'recognition' ? 'fa-star text-amber-600' : 'fa-bell text-gray-600' ?> text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-medium <?= $activity['type'] == 'recognition' ? 'text-amber-800' : 'text-gray-700' ?>">
                                <?= $activity['type'] == 'recognition' ? '🏆 Recognition' : '📋 Note' ?>
                            </span>
                            <span class="text-xs text-gray-400">
                                <?= noteTimeAgo($activity['created_at']) ?>
                            </span>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            <?= htmlspecialchars($activity['title'] ?? ($activity['type'] == 'recognition' ? 'Recognition' : 'Note')) ?>
                        </p>
                        <?php if (!empty($activity['content'])): ?>
                            <p class="text-sm text-gray-600 mt-1">
                                <?= htmlspecialchars($activity['content']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($activity['author_name'])): ?>
                            <p class="text-xs text-gray-500 mt-1">
                                — <?= htmlspecialchars($activity['author_name']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-sm text-gray-400 text-center py-4">No recent activity</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($noteTotalPages > 1): ?>
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">Page <?= $notePage ?> of <?= $noteTotalPages ?></p>
            <div class="flex gap-1">
                <?php if ($notePage > 1): ?>
                    <a href="?tab=dashboard&note_page=<?= $notePage - 1 ?>"
                        class="w-7 h-7 flex items-center justify-center text-xs rounded border border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <?php if ($notePage < $noteTotalPages): ?>
                    <a href="?tab=dashboard&note_page=<?= $notePage + 1 ?>"
                        class="w-7 h-7 flex items-center justify-center text-xs rounded border border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>