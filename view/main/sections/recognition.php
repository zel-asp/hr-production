<!-- Social Recognition Section -->
<div class="tab-content" id="recognition-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Mentorship & Recognition</h2>
            <p class="text-gray-500 text-sm mt-1">Assign mentors and recognize top performers</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModal('assignMentorModal')" class="btn-primary">
                <i class="fas fa-user-plus"></i>
                Assign Mentor
            </button>
            <button onclick="openModal('giveRecognitionModal')"
                class="px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-award"></i>
                Give Recognition
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter by:</span>
                <select name="recognition_filter" onchange="applyRecognitionFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="all" <?= $recognitionFilter == 'all' ? 'selected' : '' ?>>All Performers</option>
                    <option value="rated" <?= $recognitionFilter == 'rated' ? 'selected' : '' ?>>Highly Rated</option>
                    <option value="attendance" <?= $recognitionFilter == 'attendance' ? 'selected' : '' ?>>Perfect
                        Attendance</option>
                    <option value="efficient" <?= $recognitionFilter == 'efficient' ? 'selected' : '' ?>>Most Efficient
                    </option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Sort by:</span>
                <select name="recognition_sort" onchange="applyRecognitionFilter()"
                    class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="recent" <?= $recognitionSort == 'recent' ? 'selected' : '' ?>>Most Recent</option>
                    <option value="name" <?= $recognitionSort == 'name' ? 'selected' : '' ?>>Mentor Name</option>
                    <option value="mentees" <?= $recognitionSort == 'mentees' ? 'selected' : '' ?>>Most Mentees</option>
                </select>
            </div>
            <div class="flex-1 max-w-xs">
                <div class="relative">
                    <input type="text" name="recognition_search" placeholder="Search mentors..."
                        value="<?= htmlspecialchars($recognitionSearch) ?>"
                        class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200"
                        onkeypress="if(event.key === 'Enter') applyRecognitionFilter()">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                </div>
            </div>
            <?php if (!empty($recognitionSearch) || $recognitionFilter != 'all' || $recognitionSort != 'recent'): ?>
                <a href="?tab=recognition" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Active Mentors</p>
            <p class="text-xl font-bold text-gray-800"><?= $activeMentorsCount ?></p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Total Mentees</p>
            <p class="text-xl font-bold text-gray-800"><?= $totalMentees ?></p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">Recognitions</p>
            <p class="text-xl font-bold text-gray-800"><?= $recognitionStats['total_recognitions'] ?? 0 ?></p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-500">EOM Awards</p>
            <p class="text-xl font-bold text-gray-800"><?= $recognitionStats['eom_count'] ?? 0 ?></p>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Mentor Assignments & Feed (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Active Mentor Assignments -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Active Mentor Assignments</h3>
                    <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full border border-blue-200">
                        <?= $activeMentorsCount ?> Active
                    </span>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <?php if (!empty($mentorAssignments)): ?>
                            <?php foreach ($mentorAssignments as $mentor): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-gray-800">
                                                    <?= htmlspecialchars($mentor['mentor_name']) ?>
                                                </h4>
                                                <span
                                                    class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">Mentor</span>
                                            </div>
                                            <p class="text-xs text-gray-400"><?= htmlspecialchars($mentor['mentor_position']) ?>
                                                · <?= $mentor['mentor_years_exp'] ?> years exp</p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="text-xs text-gray-400">
                                                    <?= $mentor['total_mentees'] ?>
                                                    mentees
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-full">Active</span>
                                        <p class="text-xs text-gray-400 mt-2">Since
                                            <?= date('M Y', strtotime($mentor['earliest_assignment'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-6 text-gray-500">
                                <i class="fas fa-user-tie text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">No active mentor assignments found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Recognitions Feed - Improved Design -->
            <?php if (!empty($recentRecognitions)): ?>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-amber-50 to-orange-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-award text-amber-500"></i>
                                Recent Recognitions
                            </h3>
                            <span
                                class="text-xs bg-white text-amber-600 px-2 py-1 rounded-full border border-amber-200 shadow-sm">
                                <i class="fas fa-star mr-1 text-amber-400"></i> Celebrating excellence
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="space-y-4">
                            <?php foreach ($recentRecognitions as $index => $rec):
                                // Alternate colors for visual interest
                                $colors = ['amber', 'blue', 'green', 'purple', 'pink'];
                                $color = $colors[$index % count($colors)];
                                ?>
                                <div class="relative group">
                                    <!-- Decorative accent line -->
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-1 bg-<?= $color ?>-400 rounded-l-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    </div>

                                    <div
                                        class="flex items-start gap-4 p-4 bg-linear-to-r from-gray-50 to-white rounded-lg border border-gray-100 hover:border-<?= $color ?>-200 hover:shadow-md transition-all duration-200">
                                        <!-- Avatar with recognition icon -->
                                        <div class="relative">
                                            <div
                                                class="w-12 h-12 bg-<?= $color ?>-100 rounded-xl flex items-center justify-center text-<?= $color ?>-700 font-semibold text-sm shadow-sm">
                                                <?= $rec['initials'] ?>
                                            </div>
                                            <div
                                                class="absolute -bottom-1 -right-1 w-5 h-5 bg-<?= $color ?>-500 rounded-full flex items-center justify-center text-white text-[10px] border-2 border-white">
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-base font-semibold text-gray-800">
                                                        <?= htmlspecialchars($rec['full_name']) ?>
                                                    </h4>
                                                    <span class="text-xs text-gray-400">
                                                        <i class="far fa-calendar-alt mr-1"></i><?= $rec['formatted_date'] ?>
                                                    </span>
                                                </div>
                                                <span
                                                    class="px-3 py-1 bg-<?= $color ?>-50 text-<?= $color ?>-700 text-xs font-medium rounded-full border border-<?= $color ?>-200 shadow-sm">
                                                    <i class="fas fa-trophy mr-1 text-<?= $color ?>-500"></i>
                                                    <?= htmlspecialchars($rec['recognition_type']) ?>
                                                </span>
                                            </div>

                                            <!-- Recognition message in a card -->
                                            <div class="mt-2 p-3 bg-white rounded-lg border border-gray-100 shadow-sm">
                                                <p class="text-sm text-gray-700 leading-relaxed">
                                                    "<?= htmlspecialchars($rec['performance_highlight']) ?>"
                                                </p>
                                            </div>

                                            <!-- Recognizer info with badge -->
                                            <?php if ($rec['recognizer_name']): ?>
                                                <div class="flex items-center gap-2 mt-3">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs text-gray-400">Recognized by</span>
                                                        <span
                                                            class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded-md">
                                                            <i class="fas fa-user-check text-gray-500 mr-1 text-xs"></i>
                                                            <?= htmlspecialchars($rec['recognizer_name']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-amber-50 to-orange-50">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-award text-amber-500"></i>
                            Recent Recognitions
                        </h3>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-3xl text-amber-300"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-700 mb-2">No recognitions yet</h4>
                        <p class="text-sm text-gray-400 mb-4">Be the first to recognize a team member's achievement!</p>
                        <button onclick="openModal('giveRecognitionModal')"
                            class="inline-flex items-center gap-2 text-sm bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-award"></i>
                            Give Recognition
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Top Performers List - Updated with Mentor Ratings -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-amber-500"></i>
                    Top Performers This Month
                </h3>
                <div class="space-y-4">
                    <?php if (!empty($topPerformers)): ?>
                        <?php foreach ($topPerformers as $performer): ?>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="relative">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-800">
                                                <?= htmlspecialchars($performer['full_name']) ?>
                                            </h4>
                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($performer['position']) ?></p>
                                        </div>
                                        <span
                                            class="text-xs bg-<?= $performer['badge_color'] ?>-100 text-<?= $performer['badge_color'] ?>-700 px-2 py-1 rounded-full font-medium">
                                            <?= $performer['badge_text'] ?>
                                        </span>
                                    </div>

                                    <!-- Rating Stars -->
                                    <div class="flex items-center gap-1 mt-2">
                                        <?php
                                        $rating = $performer['avg_rating'];
                                        for ($i = 1; $i <= 5; $i++):
                                            if ($i <= floor($rating)): ?>
                                                <i class="fas fa-star text-amber-400 text-xs"></i>
                                            <?php elseif ($i - 0.5 <= $rating): ?>
                                                <i class="fas fa-star-half-alt text-amber-400 text-xs"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-gray-300 text-xs"></i>
                                            <?php endif;
                                        endfor; ?>
                                        <span class="text-xs text-gray-500 ml-1">(<?= number_format($rating, 1) ?>)</span>
                                        <span class="text-xs text-gray-400 ml-1">• <?= $performer['rating_count'] ?>
                                            ratings</span>
                                    </div>

                                    <!-- Latest Comment -->
                                    <?php if (!empty($performer['latest_comment'])): ?>
                                        <div class="mt-2 p-2 bg-white rounded border border-gray-100">
                                            <p class="text-xs text-gray-600 italic">
                                                "<?= htmlspecialchars($performer['latest_comment']) ?>"</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-xs text-gray-400">
                                                    <?= $performer['latest_mentor'] ?> • <?= $performer['latest_rating_date'] ?>
                                                </p>
                                                <?php if ($performer['perfect_attendance']): ?>
                                                    <span class="text-xs text-green-600">
                                                        <i class="fas fa-check-circle"></i> Perfect Attendance
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($performer['perfect_attendance']): ?>
                                            <p class="text-xs text-green-600 mt-1">
                                                <i class="fas fa-check-circle"></i> Perfect Attendance (30 days)
                                            </p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-star text-3xl mb-2 text-gray-300"></i>
                            <p class="text-sm">No rating data available</p>
                            <p class="text-xs text-gray-400 mt-1">Mentees with ratings will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Mentors -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-tie text-blue-500"></i>
                    Available Mentors
                </h3>
                <div class="space-y-3">
                    <?php if (!empty($availableMentors)): ?>
                        <?php foreach ($availableMentors as $mentor): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">
                                            <?= htmlspecialchars($mentor['full_name']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400"><?= htmlspecialchars($mentor['position']) ?> ·
                                            <?= $mentor['years_exp'] ?> years
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500"><?= $mentor['current_mentees'] ?>/5 mentees</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 text-center py-2">No available mentors at this time</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="assignMentorModal" class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Assign Mentor</h3>
                <button onclick="closeModal('assignMentorModal')" class="btn-primary">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form class="space-y-4" action="/assign-mentor" method="POST"> <input type="hidden" name="csrf_token"
                    value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <!-- Select Mentee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Mentee <span
                            class="text-red-500">*</span></label>
                    <select name="mentee_id" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                        <option value="">Choose employee...</option>
                        <?php if (!empty($mentorMentees)): ?>
                            <?php foreach ($mentorMentees as $mentee): ?>
                                <option value="<?= $mentee['id'] ?>">
                                    <?= htmlspecialchars($mentee['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($mentorMentees)): ?>
                        <p class="text-xs text-amber-600 mt-1">No eligible mentees found</p>
                    <?php endif; ?>
                </div>

                <!-- Select Mentor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Mentor <span
                            class="text-red-500">*</span></label>
                    <select name="mentor_id" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                        <option value="">Choose mentor...</option>
                        <?php if (!empty($mentorMentors)): ?>
                            <?php foreach ($mentorMentors as $mentor): ?>
                                <option value="<?= $mentor['id'] ?>">
                                    <?= htmlspecialchars($mentor['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($mentorMentors)): ?>
                        <p class="text-xs text-amber-600 mt-1">No eligible mentors found</p>
                    <?php endif; ?>
                </div>

                <!-- Program Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Duration <span
                            class="text-red-500">*</span></label>
                    <select name="duration" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                        <option value="">Select duration...</option>
                        <?php foreach ($mentorDurations as $value => $label): ?>
                            <option value="<?= $value ?>">
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Goals & Objectives -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Goals & Objectives <span
                            class="text-red-500">*</span></label>
                    <textarea name="goals" rows="3" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400"
                        placeholder="Define mentorship goals..."></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeModal('assignMentorModal')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary" <?= (empty($mentorMentees) || empty($mentorMentors)) ? 'disabled' : '' ?>>
                        <i class="fas fa-user-plus"></i>
                        Assign Mentor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Give Recognition Modal -->
<div id="giveRecognitionModal" class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recognize Top Performer</h3>
                <button onclick="closeModal('giveRecognitionModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="/give-recognition" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <!-- Select Employee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Employee <span
                            class="text-red-500">*</span></label>
                    <select name="employee_id" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                        <option value="">Choose employee...</option>
                        <?php if (!empty($recognitionEmployees)): ?>
                            <?php foreach ($recognitionEmployees as $emp): ?>
                                <option value="<?= $emp['id'] ?>" data-perfect-attendance="<?= $emp['perfect_attendance'] ?>"
                                    data-eligible-eom="<?= $emp['eligible_for_eom'] ?>">
                                    <?= htmlspecialchars($emp['dropdown_display']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Recognition Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recognition Type <span
                            class="text-red-500">*</span></label>
                    <select name="recognition_type" id="recognitionType" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                        <option value="">Select recognition type...</option>
                        <?php foreach ($recognitionTypes as $type => $details): ?>
                            <option value="<?= $details['value'] ?>" data-color="<?= $details['color'] ?>"
                                data-icon="<?= $details['icon'] ?>">
                                <?= htmlspecialchars($type) ?> -
                                <?= htmlspecialchars($details['description']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Eligibility Notice (shows dynamically) -->
                <div id="eligibilityNotice" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span id="eligibilityMessage"></span>
                </div>

                <!-- Performance Highlight -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Performance Highlight <span
                            class="text-red-500">*</span></label>
                    <textarea name="performance_highlight" rows="3" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400"
                        placeholder="Describe why this employee deserves recognition..."></textarea>
                </div>

                <!-- Recognition Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recognition Date</label>
                    <input type="date" name="recognition_date" value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                    <p class="text-xs text-gray-400 mt-1">Defaults to today</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeModal('giveRecognitionModal')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-award"></i>
                        Give Recognition
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Show eligibility notice based on selected employee and recognition type
    document.addEventListener('DOMContentLoaded', function () {
        const employeeSelect = document.querySelector('select[name="employee_id"]');
        const recognitionType = document.getElementById('recognitionType');
        const eligibilityNotice = document.getElementById('eligibilityNotice');
        const eligibilityMessage = document.getElementById('eligibilityMessage');

        function checkEligibility() {
            const selectedEmp = employeeSelect.options[employeeSelect.selectedIndex];
            const selectedType = recognitionType.value;

            if (!selectedEmp.value || !selectedType) {
                eligibilityNotice.classList.add('hidden');
                return;
            }

            const perfectAttendance = selectedEmp.dataset.perfectAttendance === '1';
            const eligibleEom = selectedEmp.dataset.eligibleEom === '1';

            let message = '';
            let showNotice = false;

            if (selectedType === 'Perfect Attendance' && !perfectAttendance) {
                message = 'This employee does not have perfect attendance in the last 30 days.';
                showNotice = true;
            } else if (selectedType === 'Employee of the Month' && !eligibleEom) {
                message = 'This employee has been employed for less than 3 months and may not be eligible for Employee of the Month.';
                showNotice = true;
            } else if (selectedType === 'Perfect Attendance' && perfectAttendance) {
                message = '✓ This employee has perfect attendance!';
                showNotice = true;
            } else if (selectedType === 'Employee of the Month' && eligibleEom) {
                message = '✓ This employee is eligible for Employee of the Month.';
                showNotice = true;
            }

            if (showNotice) {
                eligibilityMessage.textContent = message;
                eligibilityNotice.classList.remove('hidden');
            } else {
                eligibilityNotice.classList.add('hidden');
            }
        }

        employeeSelect.addEventListener('change', checkEligibility);
        recognitionType.addEventListener('change', checkEligibility);
    });
    function applyRecognitionFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', 'recognition');

        const filter = document.querySelector('select[name="recognition_filter"]')?.value;
        const sort = document.querySelector('select[name="recognition_sort"]')?.value;
        const search = document.querySelector('input[name="recognition_search"]')?.value;

        if (filter) url.searchParams.set('recognition_filter', filter);
        else url.searchParams.delete('recognition_filter');

        if (sort) url.searchParams.set('recognition_sort', sort);
        else url.searchParams.delete('recognition_sort');

        if (search) url.searchParams.set('recognition_search', search);
        else url.searchParams.delete('recognition_search');

        window.location.href = url.toString();
    }

</script>