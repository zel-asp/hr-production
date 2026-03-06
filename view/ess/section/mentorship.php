<!-- Header Section -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-800">My Mentees</h2>
        <p class="text-gray-500 text-sm mt-1">View and rate your assigned mentees</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Total Mentees</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $mentorTotalMentees ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-500"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Assigned to you</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Rated This Month</p>
                <p
                    class="text-2xl font-bold <?= $mentorRatedThisMonth > 0 ? 'text-green-600' : 'text-gray-400' ?> mt-1">
                    <?= $mentorRatedThisMonth ?>
                </p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-star text-green-500"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Out of <?= $mentorTotalMentees ?> mentees</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Average Rating</p>
                <p class="text-2xl font-bold text-amber-600 mt-1"><?= number_format($mentorAverageRating, 1) ?></p>
            </div>
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-amber-500"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Across all mentees</p>
    </div>
</div>

<!-- Your Mentees List -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-semibold text-gray-800">Your Mentees</h3>
    </div>

    <div class="p-6">
        <div class="space-y-3">
            <?php if (!empty($mentorMentees)): ?>
                <?php foreach ($mentorMentees as $mentee): ?>
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100 hover:border-gray-200 transition">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 <?= $mentee['gradient_class'] ?> rounded-full flex items-center justify-center text-white font-semibold">
                                    <?= $mentee['initials'] ?>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800"><?= htmlspecialchars($mentee['mentee_name']) ?></h4>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($mentee['mentee_position']) ?> ·
                                        <?= $mentee['months_employed'] ?> months
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full 
                                            <?= $mentee['status_badge'] == 'Ready for Promotion' ? 'bg-green-100 text-green-700' :
                                                ($mentee['status_badge'] == 'Probationary' ? 'bg-amber-100 text-amber-700' :
                                                    'bg-blue-100 text-blue-700') ?>">
                                            <?= $mentee['status_badge'] ?>
                                        </span>
                                        <span class="text-xs text-gray-400">Started:
                                            <?= date('M Y', strtotime($mentee['hired_date'])) ?></span>
                                    </div>
                                    <?php if ($mentee['latest_comment']): ?>
                                        <p class="text-xs text-gray-500 mt-1 italic">
                                            "<?= htmlspecialchars(substr($mentee['latest_comment'], 0, 50)) ?><?= strlen($mentee['latest_comment']) > 50 ? '...' : '' ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= round($mentee['avg_rating'])): ?>
                                        <i class="fas fa-star text-amber-400 text-sm"></i>
                                    <?php elseif ($i - 0.5 <= $mentee['avg_rating']): ?>
                                        <i class="fas fa-star-half-alt text-amber-400 text-sm"></i>
                                    <?php else: ?>
                                        <i class="far fa-star text-gray-300 text-sm"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span
                                    class="text-sm font-medium text-gray-700 ml-1"><?= number_format($mentee['avg_rating'], 1) ?></span>
                            </div>
                            <button
                                onclick="openRatingModal(<?= $mentee['mentee_id'] ?>, '<?= htmlspecialchars($mentee['mentee_name']) ?>')"
                                class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                Rate
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-medium">No mentees assigned</p>
                    <p class="text-sm">You don't have any mentees yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Rating Section -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-semibold text-gray-800">Rate Your Mentee</h3>
    </div>

    <div class="p-6">
        <form class="space-y-5" method="POST" action="/mentor-rating    ">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Select Mentee Dropdown -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Mentee <span
                        class="text-red-400">*</span></label>
                <select name="mentee_id" id="menteeSelect"
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                    required>
                    <option value="" disabled selected>Choose a mentee to rate</option>
                    <?php foreach ($mentorMenteesDropdown as $mentee): ?>
                        <option value="<?= $mentee['id'] ?>">
                            <?= htmlspecialchars($mentee['display_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Rating Stars -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating <span
                        class="text-red-400">*</span></label>
                <div class="flex items-center gap-2">
                    <div class="flex gap-1 text-2xl">
                        <i class="far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition"
                            onclick="setRating(1)" data-star="1"></i>
                        <i class="far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition"
                            onclick="setRating(2)" data-star="2"></i>
                        <i class="far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition"
                            onclick="setRating(3)" data-star="3"></i>
                        <i class="far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition"
                            onclick="setRating(4)" data-star="4"></i>
                        <i class="far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition"
                            onclick="setRating(5)" data-star="5"></i>
                    </div>
                    <span class="text-sm text-gray-400 ml-2" id="ratingValue">0/5</span>
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="" required>
            </div>

            <!-- Comment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comment <span
                        class="text-red-400">*</span></label>
                <textarea name="comment" rows="4" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                    placeholder="Write your feedback about the mentee's performance..."></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Recent Ratings History -->
<?php if (!empty($mentorRecentRatings)): ?>
    <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">Recent Ratings Given</h3>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                <?php foreach ($mentorRecentRatings as $rating): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center font-medium text-amber-700">
                                <?= $rating['initials'] ?>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($rating['mentee_name']) ?></p>
                                <p class="text-xs text-gray-400">
                                    Rated
                                    <?= $rating['days_ago'] == 0 ? 'today' : ($rating['days_ago'] == 1 ? 'yesterday' : $rating['days_ago'] . ' days ago') ?>
                                </p>
                                <?php if ($rating['comment']): ?>
                                    <p class="text-xs text-gray-500 mt-1">
                                        "<?= htmlspecialchars(substr($rating['comment'], 0, 60)) ?><?= strlen($rating['comment']) > 60 ? '...' : '' ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $rating['rating']): ?>
                                    <i class="fas fa-star text-amber-400 text-xs"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-gray-300 text-xs"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="text-xs text-gray-500 ml-1"><?= number_format($rating['rating'], 1) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    let currentRating = 0;

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('ratingInput').value = rating;
        document.getElementById('ratingValue').textContent = rating + '/5';

        const stars = document.querySelectorAll('.flex.gap-1.text-2xl i');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.className = 'fas fa-star text-amber-400 cursor-pointer transition';
            } else {
                star.className = 'far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition';
            }
        });
    }

    function openRatingModal(menteeId, menteeName) {
        // Select the mentee in the dropdown
        const select = document.getElementById('menteeSelect');
        for (let option of select.options) {
            if (option.value == menteeId) {
                option.selected = true;
                break;
            }
        }
        // Scroll to rating section
        document.querySelector('.bg-white.rounded-xl.border.border-gray-100.shadow-sm.overflow-hidden:last-child').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // Initialize star hover effect
    document.querySelectorAll('.flex.gap-1.text-2xl i').forEach(star => {
        star.addEventListener('mouseover', function () {
            const rating = this.dataset.star;
            const stars = document.querySelectorAll('.flex.gap-1.text-2xl i');
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.className = 'fas fa-star text-amber-400 cursor-pointer transition';
                } else {
                    s.className = 'far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition';
                }
            });
        });

        star.addEventListener('mouseout', function () {
            if (currentRating === 0) {
                const stars = document.querySelectorAll('.flex.gap-1.text-2xl i');
                stars.forEach(s => {
                    s.className = 'far fa-star text-gray-300 hover:text-amber-400 cursor-pointer transition';
                });
            } else {
                setRating(currentRating);
            }
        });
    });
</script>