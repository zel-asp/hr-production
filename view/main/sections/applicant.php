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
            <div class="filter-chip" data-filter="contractSigning">Contract Signing</div>
            <div class="filter-chip" data-filter="hired">Hired</div>
        </div>

        <!-- Applicants Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 pl-4 pr-6 text-sm font-medium text-gray-600 whitespace-nowrap">Name
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-600 whitespace-nowrap">Age</th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-600 whitespace-nowrap">Gender</th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-600 whitespace-nowrap">Position
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-600 whitespace-nowrap">Applied Date
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-600 whitespace-nowrap">Resume</th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-600 whitespace-nowrap">Status</th>
                        <th class="text-left py-3 pl-6 pr-4 text-sm font-medium text-gray-600 whitespace-nowrap">Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applicants)): ?>
                        <?php foreach ($applicants as $applicant): ?>
                            <?php
                            $id = (int) $applicant['id'];
                            $fullName = htmlspecialchars($applicant['full_name']);
                            $age = htmlspecialchars($applicant['age']);
                            $gender = htmlspecialchars($applicant['gender']);
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
                                case 'contract':
                                    $bgColorClass = 'bg-yellow-50 hover:bg-yellow-100';
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
                                case 'contract':
                                    $statusBadgeClass = 'border-yellow-200 text-yellow-800';
                                    $statusIcon = 'fa-solid fa-pencil';
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
                                <td class="py-4 pl-4 pr-6">
                                    <div class="flex items-center gap-3 min-w-50">
                                        <div
                                            class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm -shrink-0">
                                            <i class="fa-solid fa-user text-gray-500 text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-sm font-medium truncate block"><?= $fullName ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm font-medium whitespace-nowrap"><?= $age ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm font-medium whitespace-nowrap"><?= $gender ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="min-w-37">
                                        <span class="text-sm font-medium flex items-center gap-1.5">
                                            <i class="fas fa-briefcase text-gray-500 -shrink-0"></i>
                                            <span class="truncate"><?= $position ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="min-w-30">
                                        <span class="text-sm text-gray-600 flex items-center gap-1.5 whitespace-nowrap">
                                            <i class="fa-regular fa-calendar text-gray-400"></i>
                                            <?= $created ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if (!empty($resume)): ?>
                                        <a href="<?= $resume ?>" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-primary hover:bg-primary/5 rounded-md text-sm transition border border-gray-200 whitespace-nowrap">
                                            <i class="fas fa-file-pdf text-red-500"></i>
                                            <span>View Resume</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 flex items-center gap-1.5 whitespace-nowrap">
                                            <i class="fa-regular fa-file"></i>
                                            No Resume
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="min-w-45">
                                        <!-- Status Select Dropdown -->
                                        <select name="status"
                                            class="status-select w-full px-3 py-1.5 rounded-md text-sm font-medium border <?= $statusBadgeClass ?> bg-white"
                                            data-id="<?= $id ?>">
                                            <?php
                                            $statuses = ['New', 'Review', 'Interview', 'Rejected', 'Contract', 'Hired'];
                                            foreach ($statuses as $s):
                                                $selectedStatus = strtolower($s);
                                                $isSelected = ($selectedStatus === $status);
                                                ?>
                                                <option value="<?= $s ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                    <?= $s ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <!-- Start Date Input (for Hired) -->
                                        <div class="start-date-container mt-2 <?= $status === 'hired' ? '' : 'hidden' ?>"
                                            data-id="<?= $id ?>">
                                            <label class="block text-xs text-gray-600 mb-1">Start Date</label>
                                            <input type="date" name="start_date"
                                                class="start-date-input w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary"
                                                value="<?= htmlspecialchars($applicant['start_date'] ?? '') ?>"
                                                data-id="<?= $id ?>" min="<?= date('Y-m-d') ?>">
                                        </div>

                                        <!-- Interview Date Input -->
                                        <div class="interview-date-container mt-2 <?= $status === 'interview' ? '' : 'hidden' ?>"
                                            data-id="<?= $id ?>">
                                            <label class="block text-xs text-gray-600 mb-1">Interview Date</label>
                                            <input type="date" name="interview_date"
                                                class="interview-date-input w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary"
                                                value="<?= htmlspecialchars($applicant['interview_date'] ?? '') ?>"
                                                data-id="<?= $id ?>" min="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 pl-6 pr-4">
                                    <div class="flex items-center gap-2 min-w-60">
                                        <!-- View Button -->
                                        <button type="button" onclick="openModal('applicantModal<?= $id ?>')"
                                            class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-blue-600 hover:bg-blue-50 rounded-md text-sm font-medium transition border border-gray-200 whitespace-nowrap">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span>View</span>
                                        </button>

                                        <?php if ($status !== 'new'):
                                            // Prepare email content based on status
                                            $emailSubject = '';
                                            $emailBody = '';

                                            switch ($status) {
                                                case 'review':
                                                    $emailSubject = "Your Application is Under Review - " . $position;
                                                    $emailBody = "Dear " . $fullName . ",\n\nThank you for applying for the " . $position . " position. We are pleased to inform you that your application has been received and is now under review.\n\nOur HR team will carefully evaluate your qualifications and get back to you within 3-5 business days regarding next steps.\n\nThank you for your interest in joining our team!\n\nBest regards,\nHR Department";
                                                    break;

                                                case 'interview':
                                                    $interviewDateFormatted = !empty($applicant['interview_date'])
                                                        ? date('F j, Y', strtotime($applicant['interview_date']))
                                                        : 'to be scheduled';
                                                    $interviewTime = !empty($applicant['interview_time'])
                                                        ? date('g:i A', strtotime($applicant['interview_time']))
                                                        : '10:00 AM';

                                                    $emailSubject = "Interview Invitation - " . $position;
                                                    $emailBody = "Dear " . $fullName . ",\n\nWe were impressed by your application and would like to invite you for an interview to discuss the " . $position . " position further.\n\nYour interview has been scheduled for:\nDate: " . $interviewDateFormatted . "\nTime: " . $interviewTime . "\n\nPlease let us know if you need to reschedule by replying to this email.\n\nPlease bring the following documents:\n- Updated resume/CV\n- Portfolio (if applicable)\n- Valid ID\n\nInterview Location: Our HR Office (2nd Floor, Main Building)\n\nWe look forward to meeting you!\n\nBest regards,\nHR Department";
                                                    break;

                                                case 'hired':
                                                    $emailSubject = "Congratulations! You're Hired - " . $position;
                                                    $emailBody = "Dear " . $fullName . ",\n\nCongratulations! We are delighted to offer you the position of " . $position . " at our company!\n\nYour expected start date is: " . date('F j, Y', strtotime($applicant['start_date'] ?? '')) . "\n\nOur HR team will reach out to you shortly with the employment contract and next steps.\n\nWelcome to the team!\n\nBest regards,\nHR Department";
                                                    break;

                                                case 'contract':
                                                    $contractDateFormatted = !empty($applicant['contract_signing_date'])
                                                        ? date('F j, Y', strtotime($applicant['contract_signing_date']))
                                                        : 'to be scheduled';

                                                    $emailSubject = "Contract Signing Invitation - " . $position;
                                                    $emailBody = "Dear " . $fullName . ",\n\nCongratulations! You have progressed to the Contract Signing stage for the " . $position . " position.\n\nYour contract signing is scheduled for:\nDate: " . $contractDateFormatted . "\n\nPlease bring the necessary documents and valid ID.\n\nIf you have any questions or need to reschedule, please contact our HR team.\n\nWe look forward to welcoming you officially!\n\nBest regards,\nHR Department";
                                                    break;

                                                case 'rejected':
                                                    $emailSubject = "Update on Your Application - " . $position;
                                                    $emailBody = "Dear " . $fullName . ",\n\nThank you for applying for the " . $position . " position.\n\nAfter careful consideration, we regret to inform you that we have decided to move forward with other candidates whose qualifications more closely match our current needs.\n\nWe appreciate your interest in joining our team and wish you success in your job search.\n\nBest regards,\nHR Department";
                                                    break;

                                                default:
                                                    $emailSubject = "Application Update - " . $position;
                                                    $emailBody = "Dear " . $fullName . ",\n\nWe would like to inform you that your application status has been updated to: " . ucfirst($status) . ".\n\nOur team will contact you soon with more information.\n\nBest regards,\nHR Department";
                                            }
                                            ?>
                                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($email) ?>&su=<?= urlencode($emailSubject) ?>&body=<?= urlencode($emailBody) ?>"
                                                target="_blank"
                                                class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-indigo-600 hover:bg-indigo-50 rounded-md text-sm font-medium transition border border-gray-200 whitespace-nowrap"
                                                title="Send email via Gmail">
                                                <i class="fas fa-envelope text-xs"></i>
                                                <span>Email</span>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Update Status Button -->
                                        <button type="button"
                                            class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-yellow-600 hover:bg-yellow-50 rounded-md text-sm font-medium transition border border-gray-200 whitespace-nowrap update-status-btn"
                                            data-id="<?= $id ?>" data-csrf="<?= $_SESSION['csrf_token'] ?>">
                                            <i class="fas fa-sync-alt text-xs"></i>
                                            <span>Update</span>
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="/deleteApplicant" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this applicant?');"
                                            class="inline-block">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="hidden" value="DELETE" name="__method">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit"
                                                class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-red-600 hover:bg-red-50 rounded-md text-sm font-medium transition border border-gray-200 whitespace-nowrap">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-500">
                                <i class="fa-solid fa-users text-4xl mb-3 text-gray-300"></i>
                                <p class="text-lg font-medium">No applicants found.</p>
                                <p class="text-sm">Click "Add Applicant" to create a new application</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hired Employees Section that has no record in the schedule_contract table -->
    <div class="mt-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Ready for Contract Signing</h2>
                <p class="text-gray-600 mt-1">List of applicants with Contract status ready for onboarding</p>
            </div>
            <div class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full border border-blue-200">
                <i class="fas fa-users mr-1"></i> <?= $totalContractReady ?> applicants
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Filters -->
            <div
                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-800" id="contract">Contract Candidates</h3>
                <form method="GET" class="flex items-center gap-3">
                    <input type="hidden" name="tab" value="applicant">

                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="contract_search" placeholder="Search applicants..."
                            value="<?= htmlspecialchars($contractReadySearch) ?>"
                            class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 w-64">
                    </div>

                    <select name="contract_dept" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                        <option value="">All Departments</option>
                        <?php foreach ($contractReadyDepartments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department']) ?>"
                                <?= $contractReadyDept == $dept['department'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['department']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($contractReadySearch) || !empty($contractReadyDept)): ?>
                        <a href="/main?tab=applicant"
                            class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50">
                            <th
                                class="text-left py-3 pl-6 pr-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Applicant</th>
                            <th
                                class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Position</th>
                            <th
                                class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Department</th>
                            <th
                                class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Rate/Hour</th>
                            <th
                                class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Applied Date</th>
                            <th
                                class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Contract Status</th>
                            <th
                                class="text-left py-3 pl-4 pr-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contractReadyApplicants)): ?>
                            <?php foreach ($contractReadyApplicants as $candidate): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-4 pl-6 pr-4">
                                        <div class="flex items-center gap-3 min-w-[200px]">
                                            <div
                                                class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-sm font-semibold text-green-700">
                                                    <?= $candidate['initials'] ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">
                                                    <?= htmlspecialchars($candidate['full_name']) ?>
                                                </p>
                                                <p class=" text-xs text-gray-500">
                                                    <?= htmlspecialchars($candidate['email']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-sm text-gray-600 whitespace-nowrap">
                                            <?= htmlspecialchars($candidate['position']) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class=" text-sm text-gray-600 whitespace-nowrap">
                                            <?= htmlspecialchars($candidate['department'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-sm font-medium text-gray-800 whitespace-nowrap">
                                            <?= $candidate['rate_per_hour'] ? '₱' . number_format($candidate['rate_per_hour'], 2) : '—' ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-sm text-gray-600 whitespace-nowrap">
                                            <?= date('M d, Y', strtotime($candidate['created_at'])) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <?php if ($candidate['contract_status'] == 'scheduled'): ?>
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $candidate['status_class'] ?> whitespace-nowrap">
                                                    <i class="fas fa-check-circle text-[10px] mr-1.5"></i>
                                                    <?= $candidate['status_text'] ?>
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    <?= $candidate['formatted_contract_date'] ?? '' ?>
                                                    <?= $candidate['formatted_contract_time'] ? 'at ' .
                                                        $candidate['formatted_contract_time'] : '' ?>
                                                </span>
                                            </div>
                                        <?php elseif ($candidate['contract_status'] == 'done'): ?>
                                            <div class="flex flex-col gap-1">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $candidate['status_class'] ?>
                                whitespace-nowrap">
                                                    <i class="fas fa-exclamation-circle text-[10px] mr-1.5"></i>
                                                    <?= $candidate['status_text'] ?>
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Was:

                                                    <?= $candidate['formatted_contract_date'] ?? '' ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
            <?= $candidate['status_class'] ?> whitespace-nowrap">
                                                <i class="fas fa-clock text-[10px] mr-1.5"></i>
                                                <?= $candidate['status_text'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class=" py-4 pl-4 pr-6">
                                        <div class="flex items-center gap-2">
                                            <?php if ($candidate['contract_status'] == 'scheduled'): ?>
                                                <!-- Scheduled - Show info and option to cancel -->
                                                <button onclick="openModal('scheduleContractModal<?= $candidate['id'] ?>')" class=" p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50
                    rounded-lg transition-colors" title="Reschedule contract">
                                                    <i class="fas fa-calendar"></i>
                                                </button>
                                                <form action="/cancel-contract" method="POST" class="inline"
                                                    onsubmit="return confirm('Cancel this contract?')">
                                                    <input type="hidden" name="__method" value="DELETE">
                                                    <input type="hidden" name="csrf_token" value="
                        <?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="contract_id"
                                                        value="<?= $candidate['contract_id'] ?>">
                                                    <button type="submit"
                                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Cancel contract">
                                                        <i class="fas fa-xmark"></i>
                                                    </button>
                                                </form>
                                            <?php elseif ($candidate['contract_status'] == 'done'): ?>
                                                <!-- Expired - Show reschedule option -->
                                                <form action="/hire-applicant" method="POST" class="inline">
                                                    <input type="hidden" name="__method" value="PATCH">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="contract_id"
                                                        value="<?= $candidate['contract_id'] ?>">
                                                    <button type="submit" class=" p-2 text-green-500 hover:text-green-700 hover:bg-green-50
                            rounded-lg transition-colors" title="confirm">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="/cancel-contract" method="POST" class="inline"
                                                    onsubmit="return confirm('Cancel this contract?')">
                                                    <input type="hidden" name="__method" value="DELETE">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="contract_id"
                                                        value="<?= $candidate['contract_id'] ?>">
                                                    <button type="submit"
                                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Cancel contract">
                                                        <i class="fas fa-xmark"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <!-- Not scheduled - Show schedule button -->
                                                <button onclick="openModal('scheduleContractModal<?= $candidate['id'] ?>')"
                                                    class="p-2 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Schedule contract signing">
                                                    <i class="fas fa-calendar-check">
                                                    </i>
                                                </button>
                                            <?php endif; ?>

                                            <!-- View Resume Button -->
                                            <?php if (!empty($candidate['resume_path'])): ?>
                                                <a href="<?= htmlspecialchars($candidate['resume_path']) ?>" target="_blank" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg
                transition-colors" title="View resume">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Schedule Contract Modal for this applicant -->
                                <div id="scheduleContractModal<?= $candidate['id'] ?>"
                                    class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
                                        <div class=" p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-semibold text-gray-800">Schedule Contract Signing</h3>
                                                <button onclick="closeModal('scheduleContractModal<?= $candidate['id'] ?>')"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <form method="POST" action="/schedule-contract" class="space-y-4">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <input type="hidden" name="applicant_id" value="<?= $candidate['id'] ?>">
                                                <input type="hidden" name="employee_name"
                                                    value="<?= htmlspecialchars($candidate['full_name']) ?>">

                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1">Applicant</label>
                                                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg
            text-sm text-gray-700">
                                                        <?= htmlspecialchars($candidate['full_name']) ?>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                                    <input type="text" name="position"
                                                        value="<?= htmlspecialchars($candidate['position']) ?>"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                        readonly>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate
                                                        (₱)</label>
                                                    <input type="number" name="hourly_rate" step="0.01" min="0"
                                                        value="<?= htmlspecialchars($candidate['rate_per_hour'] ?? '0.00') ?>"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                        placeholder="e.g., 250.00" required>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contract Signing
                                                        Date <span class="text-red-400">*</span></label>
                                                    <input type="date" name="contract_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
            focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" min="<?= date('Y-m-d') ?>"
                                                        required>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time
                                                        (Optional)</label>
                                                    <input type="time" name="contract_time"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                        value="10:00">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                                    <input type="text" name="contract_location"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                        placeholder="e.g., HR Office, 2nd Floor" value="HR Office">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes
                                                        (Optional)</label>
                                                    <textarea name="contract_notes" rows="2"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                        placeholder="Any additional instructions..."></textarea>
                                                </div>

                                                <div class="flex justify-end gap-2 pt-4">
                                                    <button type="button" onclick="closeModal('scheduleContractModal
                            <?= $candidate['id'] ?>')" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg
                            hover:bg-gray-200 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors flex items-center gap-2">
                                                        <i class="fas fa-calendar-check"></i>
                                                        Schedule Signing
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan=" 7" class="py-8 text-center text-gray-500">
                                    <i class="fas fa-file-contract text-4xl mb-2 text-gray-300"></i>
                                    <p>No applicants with Contract status found</p>
                                    <?php if (
                                        !empty($contractReadySearch) ||
                                        !empty($contractReadyDept)
                                    ): ?>
                                        <a href="?tab=applicant"
                                            class="text-sm text-blue-500 hover:underline mt-2 inline-block">
                                            Clear filters
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalContractReadyPages > 1): ?>
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium">
                            <?= min(1 + ($contractReadyPage - 1) * $contractReadyPerPage, $totalContractReady) ?>-
                            <?= min($contractReadyPage * $contractReadyPerPage, $totalContractReady) ?>
                        </span>
                        of <span class="font-medium">
                            <?= $totalContractReady ?>
                        </span> applicants
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($contractReadyPage > 1): ?>
                            <a href="?tab=applicant&contract_page=
                                <?= $contractReadyPage - 1 ?>&contract_search=
                                <?= urlencode($contractReadySearch) ?>&contract_dept=
                                <?= urlencode($contractReadyDept) ?>" class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border
                                border-gray-200 text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= min(5, $totalContractReadyPages); $i++): ?>
                            <a href="/main?tab=applicant&contract_page=
                                <?= $i ?>&contract_search=
                                <?= urlencode($contractReadySearch) ?>&contract_dept=
                                <?= urlencode($contractReadyDept) ?>"
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg <?= $i == $contractReadyPage ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">

                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($contractReadyPage < $totalContractReadyPages): ?>
                            <a href="/main?tab=applicant&contract_page=<?= $contractReadyPage + 1 ?>&contract_search=
                        <?= urlencode($contractReadySearch) ?>&contract_dept=
                        <?= urlencode($contractReadyDept) ?>" class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border
                        border-gray-200 text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Schedule Contract Modals -->
<?php if (!empty($contractReadyApplicants)): ?>
    <?php foreach ($contractReadyApplicants as $candidate): ?>
        <div id="scheduleContractModal<?= $candidate['id'] ?>"
            class=" modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Schedule Contract Signing</h3>
                        <button onclick="closeModal('scheduleContractModal<?= $candidate['id'] ?>')"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form method="POST" action="/schedule-contract" class="space-y-4">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="applicant_id" value="<?= $candidate['id'] ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($candidate['full_name']) ?>">
                        <input type="hidden" name="position" value="<?= $candidate['position'] ?>">

                        <!-- Employee Information Display (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                                <?= htmlspecialchars($candidate['full_name']) ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                                <?= htmlspecialchars($candidate['position']) ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                                <?= htmlspecialchars($candidate['department'] ?? 'Not Assigned') ?>
                            </div>
                        </div>

                        <!-- Rate Information (If you want to update applicants table) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate (₱)</label>
                            <input type="number" name="rate_per_hour" step="0.01" min="0"
                                value="<?= htmlspecialchars($candidate['rate_per_hour']) ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="e.g., 250.00">
                            <p class="text-xs text-gray-500 mt-1">This will update the applicant's rate</p>
                        </div>

                        <!-- Contract Details -->
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Contract Signing Details</h4>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Contract Signing Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="contract_date" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 border
                    border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500
                    focus:border-transparent" min="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Contract Signing Time
                                    </label>
                                    <input type="time" name="contract_time" value="10:00"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Location <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="contract_location" value="HR Office - 2nd Floor"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="e.g., HR Office, 2nd Floor" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Additional Notes
                                    </label>
                                    <textarea name="contract_notes" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Any special instructions or reminders for the employee...">Please bring valid IDs and your latest resume.</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Contract Terms Summary (Optional Display) -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h4 class="text-xs font-medium text-blue-800 uppercase tracking-wider mb-2">Contract Summary</h4>
                            <div class="space-y-1 text-sm text-blue-700">
                                <p><span class="font-medium">Position:</span>
                                    <?= htmlspecialchars($candidate['position']) ?>
                                </p>
                                <p><span class="font-medium">Rate:</span>
                                    ₱
                                    <?= number_format($candidate['rate_per_hour'] ?? 0, 2) ?>/hour
                                </p>
                                <p class="text-xs mt-2">The employee will be notified via email with these details.</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeModal('scheduleContractModal<?= $candidate['id'] ?>')"
                                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors flex items-center gap-2">
                                <i class="fas fa-calendar-check"></i>
                                Schedule Contract Signing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>