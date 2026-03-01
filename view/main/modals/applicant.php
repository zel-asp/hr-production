<?php if (!empty($applicants)): ?>
    <?php foreach ($applicants as $applicant):
        $id = (int) $applicant['id'];
        $fullName = htmlspecialchars($applicant['full_name']);
        $position = htmlspecialchars($applicant['position']);
        $email = htmlspecialchars($applicant['email']);
        $phone = htmlspecialchars($applicant['phone']);
        $experience = htmlspecialchars($applicant['experience']);
        $education = htmlspecialchars($applicant['education']);
        $skills = htmlspecialchars($applicant['skills']);
        $resume = htmlspecialchars($applicant['resume_path']);
        $coverNote = htmlspecialchars($applicant['cover_note']);
        $created = date('M d, Y', strtotime($applicant['created_at']));
        $status = strtolower($applicant['status'] ?? 'New');

        // Define status badge colors and icons
        $statusBadgeClass = '';
        $statusIcon = '';
        $statusBgColor = '';

        switch ($status) {
            case 'new':
                $statusBadgeClass = 'bg-blue-100 text-blue-800 border-blue-200';
                $statusIcon = 'fa-solid fa-circle-info';
                $statusBgColor = 'bg-blue-50';
                break;
            case 'review':
                $statusBadgeClass = 'bg-purple-100 text-purple-800 border-purple-200';
                $statusIcon = 'fa-solid fa-magnifying-glass';
                $statusBgColor = 'bg-purple-50';
                break;
            case 'interview':
                $statusBadgeClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                $statusIcon = 'fa-solid fa-calendar-check';
                $statusBgColor = 'bg-indigo-50';
                break;
            case 'rejected':
                $statusBadgeClass = 'bg-red-100 text-red-800 border-red-200';
                $statusIcon = 'fa-solid fa-circle-xmark';
                $statusBgColor = 'bg-red-50';
                break;
            case 'hired':
                $statusBadgeClass = 'bg-green-100 text-green-800 border-green-200';
                $statusIcon = 'fa-solid fa-circle-check';
                $statusBgColor = 'bg-green-50';
                break;
            default:
                $statusBadgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                $statusIcon = 'fa-solid fa-circle';
                $statusBgColor = 'bg-gray-50';
        }

        // Define status options array
        $statuses = ['New', 'Review', 'Interview', 'Rejected', 'Hired'];
        ?>
        <!-- Applicant Modal - Modern Design with Status Colors -->
        <div id="applicantModal<?= $id ?>"
            class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
            <div class="bg-white rounded-xl max-w-2xl w-full mx-4 shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fa-solid fa-user mr-2 text-primary"></i>
                        <?= $fullName ?> - Details
                    </h3>
                    <button onclick="closeModal('applicantModal<?= $id ?>')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-circle-xmark fa-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Current Status Display with Background Color -->
                    <div class="mb-6 flex items-center gap-2 p-3 rounded-lg <?= $statusBgColor ?>">
                        <span class="text-sm text-gray-600">Current Status:</span>
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium border <?= $statusBadgeClass ?>">
                            <i class="<?= $statusIcon ?>"></i>
                            <?= ucfirst($status) ?>
                        </span>
                    </div>

                    <!-- Applicant Details Grid -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-gray-500 block text-xs">Email</span>
                            <span class="font-medium"><?= $email ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-gray-500 block text-xs">Phone</span>
                            <span class="font-medium"><?= $phone ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-gray-500 block text-xs">Position</span>
                            <span class="font-medium"><?= $position ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-gray-500 block text-xs">Applied Date</span>
                            <span class="font-medium"><?= $created ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                            <span class="text-gray-500 block text-xs">Experience</span>
                            <span class="font-medium"><?= $experience ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                            <span class="text-gray-500 block text-xs">Education</span>
                            <span class="font-medium"><?= $education ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                            <span class="text-gray-500 block text-xs">Skills</span>
                            <span class="font-medium"><?= $skills ?></span>
                        </div>

                        <?php if (!empty($coverNote)): ?>
                            <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                                <span class="text-gray-500 block text-xs">Cover Note</span>
                                <span class="font-medium"><?= nl2br($coverNote) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($resume)): ?>
                            <div class="col-span-2 mt-2">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <span class="text-gray-500 block text-xs mb-2">Resume</span>
                                    <a href="<?= $resume ?>" target="_blank"
                                        class="flex items-center gap-3 text-primary hover:text-primary-hover transition">
                                        <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                        <div>
                                            <p class="font-medium text-sm"><?= basename($resume) ?></p>
                                            <p class="text-xs text-gray-400">Click to view document</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>


                    <!-- Close Button -->
                    <button onclick="closeModal('applicantModal<?= $id ?>')"
                        class="px-5 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>