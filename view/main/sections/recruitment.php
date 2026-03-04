<div class="tab-content" id="recruitment-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Recruitment Management</h2>
            <p class="text-gray-600 mt-1">Create job postings and attract candidates for hotel & restaurant positions
            </p>
        </div>
        <button class="btn-primary" onclick="openModal('newJobModal')">
            <i class="fas fa-plus mr-2"></i>New Job Posting
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if (!empty($jobPostings)): ?>
            <?php foreach ($jobPostings as $job): ?>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden">
                    <!-- Header with subtle linear -->
                    <div class="bg-linear-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-gray-800 line-clamp-1"><?= htmlspecialchars($job['position']) ?></h3>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                Active
                            </span>
                        </div>
                    </div>

                    <!-- Job details with consistent icon styling -->
                    <div class="p-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                <i class="fas fa-utensils text-gray-600 text-sm"></i>
                            </div>
                            <span class="font-medium"><?= htmlspecialchars($job['department']) ?></span>
                        </div>

                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-gray-600 text-sm"></i>
                            </div>
                            <span><?= htmlspecialchars($job['location']) ?></span>
                        </div>

                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-gray-600 text-sm"></i>
                            </div>
                            <span><?= htmlspecialchars($job['shift']) ?></span>
                        </div>

                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                <i class="fas fa-dollar-sign text-gray-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($job['salary']) ?></span>
                        </div>
                    </div>

                    <!-- Action buttons with consistent styling -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                        <button onclick="openEditJobModal(
                        <?= $job['id'] ?>,
                        '<?= htmlspecialchars($job['position'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($job['location'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($job['shift'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($job['salary'], ENT_QUOTES) ?>'
                    )"
                            class="flex-1 inline-flex items-center justify-center p-1 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-400 transition-colors duration-200">
                            <i class="fas fa-edit mr-2 text-gray-500"></i>
                            Edit
                        </button>

                        <form method="POST" action="/delete-job" class="flex-1"
                            onsubmit="return confirm('Are you sure you want to delete this job posting? This action cannot be undone.');">
                            <input type="hidden" value="DELETE" name="__method">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                            <button type="submit" name="delete-jobBtn"
                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors duration-200">
                                <i class="fas fa-trash mr-2 text-gray-500"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full">
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-briefcase text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 text-lg mb-2">No job postings found</p>
                    <p class="text-gray-500 text-sm">Get started by creating your first job posting.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Applicants Preview (limited to 5) -->
    <div class="card p-6 mt-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Recent Applicants</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                <?= count($recentApplicants) ?> total
            </span>
        </div>

        <div class="space-y-3">
            <?php if (!empty($recentApplicants)): ?>
                <?php foreach ($recentApplicants as $applicant): ?>
                    <!-- Applicant preview -->
                    <div
                        class="group flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all duration-200">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div
                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium shrink-0">
                                <?= strtoupper(substr($applicant['full_name'], 0, 1)) ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 truncate"><?= htmlspecialchars($applicant['full_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    Applied for: <?= htmlspecialchars($applicant['position']) ?> •
                                    <?= htmlspecialchars(date('M d, Y', strtotime($applicant['created_at']))) ?>
                                </p>
                            </div>
                        </div>

                        <button onclick="openModal('recruitment-applicantModal<?= $applicant['id'] ?>')"
                            class="ml-2 text-sm text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors duration-200 shrink-0">
                            <i class="fas fa-eye mr-1 text-xs"></i>View
                        </button>
                    </div>

                    <!-- Modal for this applicant -->
                    <div id="recruitment-applicantModal<?= $applicant['id'] ?>"
                        class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
                        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                            <!-- Modal Header -->
                            <div
                                class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800">Applicant Details</h3>
                                <button onclick="closeModal('recruitment-applicantModal<?= $applicant['id'] ?>')"
                                    class="text-gray-400 hover:text-gray-600 text-xl font-light transition-colors duration-200">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6">
                                <!-- Applicant Header -->
                                <div class="flex items-center gap-4 mb-6">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xl font-medium">
                                        <?= strtoupper(substr($applicant['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-semibold text-gray-800">
                                            <?= htmlspecialchars($applicant['full_name']) ?>
                                        </h4>
                                        <p class="text-gray-500 text-sm"><?= htmlspecialchars($applicant['position']) ?></p>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Email</p>
                                        <p class="text-gray-800 font-medium break-words">
                                            <?= htmlspecialchars($applicant['email']) ?>
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Phone</p>
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($applicant['phone']) ?></p>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Applied Date</p>
                                        <p class="text-gray-800 font-medium">
                                            <?= htmlspecialchars(date('M d, Y', strtotime($applicant['created_at']))) ?>
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Experience</p>
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($applicant['experience']) ?>
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Education</p>
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($applicant['education']) ?>
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Skills</p>
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($applicant['skills']) ?></p>
                                    </div>
                                </div>

                                <!-- Resume Section -->
                                <?php if (!empty($applicant['resume_path'])): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-6">
                                        <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                            Resume
                                        </h4>
                                        <a href="<?= htmlspecialchars($applicant['resume_path']) ?>" target="_blank"
                                            class="flex items-center gap-3 p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors duration-200 border border-gray-200">
                                            <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-800 text-sm truncate">
                                                    <?= basename($applicant['resume_path']) ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    Uploaded <?= date('M d, Y', strtotime($applicant['created_at'])) ?>
                                                </p>
                                            </div>
                                            <i class="fas fa-external-link-alt text-gray-400 text-sm"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Modal Footer Actions -->
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button onclick="closeModal('recruitment-applicantModal<?= $applicant['id'] ?>')"
                                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                        Close
                                    </button>
                                    <button onclick="scheduleInterview(<?= $applicant['id'] ?>)" class="btn-primary">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Schedule Interview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 text-sm mb-1">No recent applicants</p>
                    <p class="text-gray-400 text-xs">When candidates apply, they'll appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>