<div class="bg-white border border-gray-200 rounded-md p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Notes & Recognition</h2>
        <?php if ($noteNewCount > 0): ?>
            <span class="text-xs bg-amber-100 text-amber-600 px-2 py-1 rounded-full animate-pulse">
                <i class="fas fa-star mr-1"></i><?= $noteNewCount ?> new
            </span>
        <?php endif; ?>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-2 mb-4">
        <div class="bg-amber-50 rounded-lg p-2 text-center">
            <p class="text-lg font-bold text-amber-600"><?= $noteTotalRecognitions ?></p>
            <p class="text-xs text-gray-500">Recognition</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-2 text-center">
            <p class="text-lg font-bold text-blue-600"><?= $noteTotalNotes ?></p>
            <p class="text-xs text-gray-500">Notes</p>
        </div>
    </div>

    <p class="text-gray-500 text-sm mb-4">Recent activity</p>

    <!-- Activity Feed -->
    <div class="space-y-3">
        <?php if (!empty($noteActivities)): ?>
            <?php foreach ($noteActivities as $activity): ?>
                <?php if ($activity['type'] == 'recognition'): ?>
                    <!-- Recognition Item -->
                    <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-md border border-amber-100">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-amber-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-amber-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-medium text-amber-800">Recognition</p>
                                <span class="text-[10px] text-gray-400"><?= noteTimeAgo($activity['created_at']) ?></span>
                            </div>
                            <p class="text-sm text-gray-700 mt-1">
                                <?= htmlspecialchars($activity['title'] ?? 'Recognition') ?>
                            </p>
                            <?php if (!empty($activity['content'])): ?>
                                <p class="text-xs text-gray-500 mt-1 italic">
                                    "<?= htmlspecialchars($activity['content']) ?>"
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Note Item -->
                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-md border border-blue-100">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-sticky-note text-blue-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-medium text-blue-800">
                                    <?= htmlspecialchars($activity['title'] ?? 'Note from Admin') ?>
                                </p>
                                <span class="text-[10px] text-gray-400"><?= noteTimeAgo($activity['created_at']) ?></span>
                            </div>
                            <p class="text-sm text-gray-700 mt-1">
                                <?= htmlspecialchars($activity['content']) ?>
                            </p>
                            <?php if (!empty($activity['author_name'])): ?>
                                <p class="text-xs text-gray-500 mt-1">
                                    — <?= htmlspecialchars($activity['author_name']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-6 text-gray-400">
                <i class="fas fa-bell text-3xl mb-2"></i>
                <p class="text-sm">No recent activity</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($noteTotalPages > 1): ?>
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Page <?= $notePage ?> of <?= $noteTotalPages ?>
            </p>
            <div class="flex gap-1">
                <?php if ($notePage > 1): ?>
                    <a href="?tab=dashboard&note_page=<?= $notePage - 1 ?>"
                        class="w-7 h-7 flex items-center justify-center text-xs rounded bg-white border border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php if ($notePage < $noteTotalPages): ?>
                    <a href="?tab=dashboard&note_page=<?= $notePage + 1 ?>"
                        class="w-7 h-7 flex items-center justify-center text-xs rounded bg-white border border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>