<div class="tab-content" id="applicant-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Applicant Management</h2>
            <p class="text-gray-600 mt-1">Track and manage job applicants through the hiring pipeline
            </p>
        </div>
    </div>

    <div class="card p-6">
        <!-- Filter Tabs - MODIFIED: Made scrollable on mobile -->
        <div class="filter-chips">
            <div class="filter-chip active" data-filter="all">All</div>
            <div class="filter-chip" data-filter="new">New</div>
            <div class="filter-chip" data-filter="review">Review</div>
            <div class="filter-chip" data-filter="interview">Interview</div>
            <div class="filter-chip" data-filter="rejected">Rejected</div>
            <div class="filter-chip" data-filter="hired">Hired</div>
        </div>

        <!-- Applicants Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Name</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Position</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Applied Date</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Resume</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applicants)): ?>
                        <?php foreach ($applicants as $applicant): ?>
                            <?php
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
                            $created = date('Y-m-d', strtotime($applicant['created_at']));
                            $status = strtolower($applicant['status'] ?? 'New');

                            // Define background color classes based on status
                            $bgColorClass = '';
                            switch ($status) {
                                case 'new':
                                    $bgColorClass = 'bg-blue-50 hover:bg-blue-100';
                                    break;
                                case 'review':
                                    $bgColorClass = 'bg-purple-50 hover:bg-purple-100';
                                    break;
                                case 'interview':
                                    $bgColorClass = 'bg-indigo-50 hover:bg-indigo-100';
                                    break;
                                case 'rejected':
                                    $bgColorClass = 'bg-red-50 hover:bg-red-100';
                                    break;
                                case 'hired':
                                    $bgColorClass = 'bg-green-50 hover:bg-green-100';
                                    break;
                                default:
                                    $bgColorClass = 'bg-gray-50 hover:bg-gray-100';
                            }

                            // Status badge colors (for the select dropdown visual)
                            $statusBadgeClass = '';
                            $statusIcon = '';
                            switch ($status) {
                                case 'new':
                                    $statusBadgeClass = 'border-blue-200 text-blue-800';
                                    $statusIcon = 'fa-solid fa-circle-info';
                                    break;
                                case 'review':
                                    $statusBadgeClass = 'border-purple-200 text-purple-800';
                                    $statusIcon = 'fa-solid fa-magnifying-glass';
                                    break;
                                case 'interview':
                                    $statusBadgeClass = 'border-indigo-200 text-indigo-800';
                                    $statusIcon = 'fa-solid fa-calendar-check';
                                    break;
                                case 'rejected':
                                    $statusBadgeClass = 'border-red-200 text-red-800';
                                    $statusIcon = 'fa-solid fa-circle-xmark';
                                    break;
                                case 'hired':
                                    $statusBadgeClass = 'border-green-200 text-green-800';
                                    $statusIcon = 'fa-solid fa-circle-check';
                                    break;
                                default:
                                    $statusBadgeClass = 'border-gray-200 text-gray-800';
                                    $statusIcon = 'fa-solid fa-circle';
                            }
                            ?>
                            <tr class="border-b border-gray-200 <?= $bgColorClass ?> transition-col duration-200 applicant-row"
                                data-status="<?= $status ?>" data-id="<?= $id ?>">
                                <td class="py-4 px-4 font-medium">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-user text-gray-500 text-xs"></i>
                                        </div>
                                        <span><?= $fullName ?></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-white border border-gray-200">
                                        <i class="fas fa-briefcase mr-1 text-gray-500"></i>
                                        <?= $position ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-sm text-gray-600">
                                        <i class="fa-regular fa-calendar mr-1"></i>
                                        <?= $created ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if (!empty($resume)): ?>
                                        <a href="<?= $resume ?>" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-white text-primary hover:bg-primary/5 rounded-md text-sm transition border border-gray-200">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>View</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">
                                            <i class="fa-regular fa-file mr-1"></i>No Resume
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <!-- Status Select Dropdown -->
                                    <select name="status"
                                        class="status-select px-3 py-1.5 rounded-md text-sm font-medium border <?= $statusBadgeClass ?> bg-white"
                                        data-id="<?= $id ?>" style="min-width: 120px;">
                                        <?php
                                        $statuses = ['New', 'Review', 'Interview', 'Rejected', 'Hired'];
                                        foreach ($statuses as $s):
                                            $selectedStatus = strtolower($s);
                                            $isSelected = ($selectedStatus === $status);
                                            ?>
                                            <option value="<?= $s ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                <?= $s ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <!-- Start Date Input (hidden by default, shown only when Hired is selected) -->
                                    <div class="start-date-container mt-2 <?= $status === 'hired' ? '' : 'hidden' ?>"
                                        data-id="<?= $id ?>">
                                        <label class="block text-xs text-gray-600 mb-1">Start Date</label>
                                        <input type="date" name="start_date"
                                            class="start-date-input w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary"
                                            value="<?= htmlspecialchars($applicant['start_date'] ?? '') ?>"
                                            data-id="<?= $id ?>">
                                    </div>
                                </td>

                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <!-- View Button -->
                                        <button type="button" onclick="openModal('applicantModal<?= $id ?>')"
                                            class="flex items-center justify-center gap-1 px-3 py-2 bg-white text-blue-600 hover:bg-blue-50 rounded-md text-sm font-medium transition border border-gray-200">
                                            <i class="fas fa-eye"></i>
                                            <span class="hidden sm:inline">View</span>
                                        </button>

                                        <!-- Update Status Button (now uses the select value) -->
                                        <button type="button"
                                            class="flex items-center justify-center gap-1 px-3 py-2 bg-white text-yellow-600 hover:bg-yellow-50 rounded-md text-sm font-medium transition border border-gray-200 update-status-btn"
                                            data-id="<?= $id ?>" data-csrf="<?= $_SESSION['csrf_token'] ?>">
                                            <i class="fas fa-sync-alt"></i>
                                            <span class="hidden sm:inline">Update</span>
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="/deleteApplicant" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this applicant?');"
                                            class="inline">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="hidden" value="DELETE" name="__method">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit"
                                                class="flex items-center justify-center gap-1 px-3 py-2 bg-white text-red-600 hover:bg-red-50 rounded-md text-sm font-medium transition border border-gray-200">
                                                <i class="fas fa-trash-alt"></i>
                                                <span class="hidden sm:inline">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-500">
                                <i class="fa-solid fa-users text-4xl mb-3 text-gray-300"></i>
                                <p>No applicants found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>