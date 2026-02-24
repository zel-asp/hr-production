<div class="tab-content active" id="recruitment-content">
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
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-4 space-y-2">
            <?php foreach ($_SESSION['success'] as $msg): ?>
                <div class="flex items-center justify-between bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded shadow-md"
                    role="alert">
                    <span>
                        <?= htmlspecialchars($msg) ?>
                    </span>
                    <button onclick="this.parentElement.remove();"
                        class="ml-4 font-bold text-green-700 hover:text-green-900">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php if (!empty($jobPostings)): ?>
            <?php foreach ($jobPostings as $job): ?>
                <div class="card p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-lg">
                            <?= htmlspecialchars($job['position']) ?>
                        </h3>
                        <span class="status-badge bg-green-100 text-green-800">Active</span>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <p><i class="fas fa-utensils mr-2 w-5"></i>
                            <?= htmlspecialchars($job['department']) ?>
                        </p>
                        <p><i class="fas fa-map-marker-alt mr-2 w-5"></i>
                            <?= htmlspecialchars($job['location']) ?>
                        </p>
                        <p><i class="fas fa-clock mr-2 w-5"></i>
                            <?= htmlspecialchars($job['shift']) ?>
                        </p>
                        <p><i class="fas fa-dollar-sign mr-2 w-5"></i>
                            <?= htmlspecialchars($job['salary']) ?>
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex gap-2">
                        <button class="text-gray-600 hover:underline text-sm" onclick="openEditJobModal(
                            <?= $job['id'] ?>,
                            '<?= htmlspecialchars($job['position'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($job['location'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($job['shift'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($job['salary'], ENT_QUOTES) ?>'
                        )">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No job postings found.</p>
        <?php endif; ?>
    </div>

    <!-- Recent Applicants Preview -->
    <div class="card p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Recent Applicants</h3>
        <div class="space-y-3">
                <?php if (!empty($applicants)): ?>
                    <?php foreach ($applicants as $applicant): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm">
                                    <?= strtoupper(substr($applicant['full_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-medium">
                                        <?= htmlspecialchars($applicant['full_name']) ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Applied for: <?= htmlspecialchars($applicant['position']) ?> •
                                        <?= htmlspecialchars(date('M d, Y', strtotime($applicant['created_at']))) ?>
                                </p>
                            </div>
                        </div>
                        <button onclick="openModal('applicantModal<?= $applicant['id'] ?>')"
                            class="text-primary hover:underline text-sm">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <p class="text-gray-600">No recent applicants.</p>
                <?php endif; ?>
        </div>
    </div>
</div>