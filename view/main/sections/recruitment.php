<div class="tab-content" id="recruitment-content">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Recruitment Management</h2>
            <p class="text-gray-600 mt-1">Create job postings and attract candidates for hotel & restaurant positions
            </p>
        </div>
        <div class="flex gap-3">
            <!-- Add js-loading-btn to both buttons -->
            <button class="js-loading-btn btn-primary" onclick="window.location.href='?tab=recruitment&modal=req'">
                <i class="fas fa-building mr-2"></i>Requisitions
            </button>
            <button class="js-loading-btn btn-primary" onclick="openModal('newJobModal')">
                <i class="fas fa-plus mr-2"></i>New Job Posting
            </button>
        </div>
    </div>

    <!-- Job Postings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php if (!empty($jobPostings)): ?>
            <?php foreach ($jobPostings as $job): ?>
                <div
                    class="group bg-white rounded-xl border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200">
                    <!-- Card Header with subtle accent -->
                    <div class="p-5 pb-3 border-b border-gray-100">
                        <div class="flex justify-between items-start">
                            <h3 class="font-medium text-gray-900 truncate pr-4"><?= htmlspecialchars($job['position']) ?></h3>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700">
                                <span class="w-1 h-1 bg-emerald-500 rounded-full mr-1.5"></span>
                                Active
                            </span>
                        </div>
                    </div>

                    <!-- Job Details with refined icon treatment -->
                    <div class="p-5 space-y-4">
                        <div class="flex items-center text-sm">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center mr-3">
                                <i class="fas fa-utensils text-indigo-500 text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium"><?= htmlspecialchars($job['department']) ?></span>
                        </div>

                        <div class="flex items-center text-sm">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-amber-500 text-xs"></i>
                            </div>
                            <span class="text-gray-600"><?= htmlspecialchars($job['location']) ?></span>
                        </div>

                        <?php
                        $shiftMap = [
                            1 => 'Morning',
                            2 => 'Evening',
                            3 => 'Graveyard'
                        ];
                        $shiftText = $shiftMap[$job['shift']] ?? 'Unknown';
                        $shiftColors = [
                            1 => 'bg-sky-50 text-sky-600',
                            2 => 'bg-purple-50 text-purple-600',
                            3 => 'bg-slate-50 text-slate-600'
                        ];
                        $shiftColor = $shiftColors[$job['shift']] ?? 'bg-gray-50 text-gray-600';
                        ?>

                        <div class="flex items-center text-sm">
                            <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-gray-500 text-xs"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-xs font-medium <?= $shiftColor ?>">
                                <?= htmlspecialchars($shiftText) ?>
                            </span>
                        </div>

                        <div class="flex items-center text-sm">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center mr-3">
                                <i class="fas fa-dollar-sign text-emerald-500 text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-900"><?= htmlspecialchars($job['salary']) ?></span>
                            <span class="text-xs text-gray-400 ml-1">/year</span>
                        </div>
                    </div>

                    <!-- Action buttons with loading handler class added -->
                    <div class="flex border-t border-gray-100">
                        <button onclick="openEditJobModal(
                            <?= $job['id'] ?>,
                            '<?= htmlspecialchars($job['position'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($job['location'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($job['shift'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($job['salary'], ENT_QUOTES) ?>'
                        )"
                            class="js-loading-btn flex-1 px-3 py-3 text-xs font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50/50 transition-colors duration-200">
                            <i class="fas fa-edit mr-1.5 text-gray-400 group-hover:text-indigo-400"></i>
                            Edit
                        </button>

                        <form method="POST" action="/delete-job" class="flex-1"
                            onsubmit="return confirm('Are you sure you want to delete this job posting? This action cannot be undone.');">
                            <input type="hidden" value="DELETE" name="__method">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                            <!-- Added js-loading-btn class here -->
                            <button type="submit" name="delete-jobBtn"
                                class="js-loading-btn w-full px-3 py-3 text-xs font-medium text-gray-600 hover:text-rose-600 hover:bg-rose-50/50 transition-colors duration-200 border-l border-gray-100">
                                <i class="fas fa-trash mr-1.5 text-gray-400 group-hover:text-rose-400"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full">
                <div
                    class="text-center py-16 bg-linear-to-b from-gray-50 to-white rounded-xl border-2 border-dashed border-gray-200">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-briefcase text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">No job postings yet</p>
                    <p class="text-sm text-gray-400 mb-4">Get started by creating your first position</p>
                    <!-- Added js-loading-btn class -->
                    <button onclick="openModal('newJobModal')"
                        class="js-loading-btn inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Job Posting
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Applicants Section with improved card design -->
    <div class="mt-8 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Applicants</h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                        Last 30 days
                    </span>
                </div>
                <span class="text-xs font-medium text-gray-500">
                    <?= count($recentApplicants) ?> total
                </span>
            </div>
        </div>

        <div class="p-6">
            <?php if (!empty($recentApplicants)): ?>
                <div class="space-y-3">
                    <?php foreach ($recentApplicants as $applicant): ?>
                        <div
                            class="group flex items-center justify-between p-4 bg-gray-50/50 hover:bg-gray-100/80 rounded-xl transition-all duration-200">
                            <div class="flex items-center gap-4 min-w-0 flex-1">
                                <div class="relative">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-sm font-medium shadow-sm">
                                        <?= strtoupper(substr($applicant['full_name'], 0, 1)) ?>
                                    </div>
                                    <span
                                        class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        <?= htmlspecialchars($applicant['full_name']) ?>
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                            <?= htmlspecialchars($applicant['position']) ?>
                                        </span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-400">
                                            <?= htmlspecialchars(date('M d', strtotime($applicant['created_at']))) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Added js-loading-btn class -->
                            <button onclick="openModal('recruitment-applicantModal<?= $applicant['id'] ?>')"
                                class="js-loading-btn ml-2 px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-sm transition-all duration-200">
                                <i class="fas fa-eye mr-1.5 text-gray-400"></i>
                                Review
                            </button>
                        </div>

                        <!-- Applicant Modal - refined design -->
                        <div id="recruitment-applicantModal<?= $applicant['id'] ?>"
                            class="modal fixed inset-0 bg-black/20 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                            <div
                                class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 animate-in fade-in zoom-in duration-200">
                                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="text-base font-semibold text-gray-900">Applicant Profile</h3>
                                    <button onclick="closeModal('recruitment-applicantModal<?= $applicant['id'] ?>')"
                                        class="js-loading-btn w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="p-6">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl font-medium shadow-sm">
                                            <?= strtoupper(substr($applicant['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                <?= htmlspecialchars($applicant['full_name']) ?>
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                                    <?= htmlspecialchars($applicant['position']) ?>
                                                </span>
                                                <span class="text-xs text-gray-400">Applied
                                                    <?= htmlspecialchars(date('M d, Y', strtotime($applicant['created_at']))) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="p-4 bg-gray-50 rounded-xl">
                                                <p class="text-xs text-gray-400 mb-1">Email</p>
                                                <p class="text-sm font-medium text-gray-900 break-words">
                                                    <?= htmlspecialchars($applicant['email']) ?>
                                                </p>
                                            </div>
                                            <div class="p-4 bg-gray-50 rounded-xl">
                                                <p class="text-xs text-gray-400 mb-1">Phone</p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($applicant['phone']) ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="p-4 bg-gray-50 rounded-xl">
                                            <p class="text-xs text-gray-400 mb-1">Experience</p>
                                            <p class="text-sm text-gray-900"><?= htmlspecialchars($applicant['experience']) ?>
                                            </p>
                                        </div>

                                        <div class="p-4 bg-gray-50 rounded-xl">
                                            <p class="text-xs text-gray-400 mb-1">Education</p>
                                            <p class="text-sm text-gray-900"><?= htmlspecialchars($applicant['education']) ?>
                                            </p>
                                        </div>

                                        <div class="p-4 bg-gray-50 rounded-xl">
                                            <p class="text-xs text-gray-400 mb-2">Skills</p>
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach (explode(',', $applicant['skills']) as $skill): ?>
                                                    <span
                                                        class="px-2 py-1 text-xs bg-white rounded-md border border-gray-200 text-gray-600">
                                                        <?= trim(htmlspecialchars($skill)) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <?php if (!empty($applicant['resume_path'])): ?>
                                            <a href="<?= htmlspecialchars($applicant['resume_path']) ?>" target="_blank"
                                                class="js-loading-btn flex items-center gap-3 p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors group">
                                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-pdf text-indigo-500"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">Resume.pdf</p>
                                                    <p class="text-xs text-indigo-600">Click to view</p>
                                                </div>
                                                <i class="fas fa-external-link-alt text-indigo-400 group-hover:text-indigo-600"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex justify-end gap-3">
                                    <button onclick="closeModal('recruitment-applicantModal<?= $applicant['id'] ?>')"
                                        class="js-loading-btn px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                        Close
                                    </button>
                                    <!-- Added js-loading-btn class -->
                                    <button
                                        class="js-loading-btn px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-download mr-2"></i>
                                        Download CV
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">No applicants yet</p>
                    <p class="text-sm text-gray-400">When candidates apply, they'll appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Other Department Job Requisitions Modal -->
<div id="otherDeptRequisitionsModal"
    class="modal fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Job Requisitions from Other Departments</h3>
                <p class="text-sm text-gray-500 mt-1">Review and approve hiring requests from other departments</p>
            </div>
            <!-- Added js-loading-btn class -->
            <button onclick="closeModal('otherDeptRequisitionsModal')"
                class="js-loading-btn text-gray-400 hover:text-gray-600 text-xl font-light transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Filter/Search Bar -->
            <form method="GET" action="" class="flex flex-wrap gap-4 mb-6 js-loading-form">
                <input type="hidden" name="tab" value="recruitment">
                <input type="hidden" name="modal" value="req">

                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="requisition_search" placeholder="Search requisitions..."
                            value="<?= htmlspecialchars($requisitionSearch) ?>"
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <select name="requisition_dept" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Departments</option>
                    <?php foreach ($requisitionDepartments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept['department']) ?>"
                            <?= $requisitionDeptFilter == $dept['department'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['department']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="requisition_priority" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Priorities</option>
                    <option value="high" <?= $requisitionPriorityFilter == 'high' ? 'selected' : '' ?>>High</option>
                    <option value="medium" <?= $requisitionPriorityFilter == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="low" <?= $requisitionPriorityFilter == 'low' ? 'selected' : '' ?>>Low</option>
                </select>

                <!-- Added submit button with loading class -->
                <button type="submit"
                    class="js-loading-btn px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>

                <?php if (!empty($requisitionSearch) || !empty($requisitionDeptFilter) || !empty($requisitionPriorityFilter)): ?>
                    <a href="?tab=recruitment&modal=req"
                        class="js-loading-btn px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>

            <!-- Requisitions List -->
            <div class="space-y-4" id="requisitionResults">
                <?php if (!empty($requisitions)): ?>
                    <?php foreach ($requisitions as $req):
                        $icon = getRequisitionIcon($req['department']);
                        ?>
                        <div
                            class="border border-gray-200 rounded-lg p-4 hover:border-blue-200 hover:shadow-sm transition-all duration-200">
                            <div class="flex flex-wrap gap-4 items-start justify-between">
                                <div class="flex-1 min-w-[200px]">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            class="w-10 h-10 bg-<?= $icon['color'] ?>-100 rounded-lg flex items-center justify-center">
                                            <i class="fas <?= $icon['icon'] ?> text-<?= $icon['color'] ?>-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($req['job_title']) ?>
                                            </h4>
                                            <p class="text-sm text-gray-500"><?= htmlspecialchars($req['department']) ?>
                                                Department • <?= htmlspecialchars($req['requested_by']) ?></p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 ml-13">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-users text-gray-400 w-4"></i>
                                            <?= $req['positions'] ?> position<?= $req['positions'] > 1 ? 's' : '' ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-calendar text-gray-400 w-4"></i>
                                            Needed by: <?= $req['formatted_needed_by'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium border <?= $req['priority_class'] ?>">
                                        <?= ucfirst($req['priority']) ?> Priority
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium border <?= $req['status_class'] ?>">
                                        <?= ucfirst($req['status']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 justify-between items-center">
                                <p class="text-sm text-gray-600"><span class="font-medium">Justification:</span>
                                    <?= htmlspecialchars($req['justification']) ?></p>
                                <div class="flex gap-2">
                                    <?php if ($req['status'] == 'pending'): ?>
                                        <form method="POST" action="/update-requisition-status" class="inline"
                                            onsubmit="return confirm('Approve this requisition?')">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="requisition_id" value="<?= $req['id'] ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <input type="hidden" name="__method" value="PATCH">
                                            <!-- Added js-loading-btn class -->
                                            <button type="submit"
                                                class="js-loading-btn px-3 py-1.5 text-sm bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200 border border-green-200">
                                                <i class="fas fa-check mr-1"></i>Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="/update-requisition-status" class="inline"
                                            onsubmit="return confirm('Decline this requisition?')">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="requisition_id" value="<?= $req['id'] ?>">
                                            <input type="hidden" name="status" value="declined">
                                            <input type="hidden" name="__method" value="PATCH">
                                            <!-- Added js-loading-btn class -->
                                            <button type="submit"
                                                class="js-loading-btn px-3 py-1.5 text-sm bg-white text-gray-600 rounded-lg hover:bg-gray-50 transition-colors duration-200 border border-gray-200">
                                                <i class="fas fa-times mr-1"></i>Decline
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 px-3 py-1.5">No actions needed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-3 text-gray-300"></i>
                        <p class="text-lg font-medium">No requisitions found</p>
                        <p class="text-sm">No job requisitions match your filters</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($requisitionTotalPages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing <span
                            class="font-medium"><?= min(1 + ($requisitionPage - 1) * $requisitionPerPage, $requisitionTotalCount) ?>-<?= min($requisitionPage * $requisitionPerPage, $requisitionTotalCount) ?></span>
                        of <span class="font-medium"><?= $requisitionTotalCount ?></span> requisitions
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($requisitionPage > 1): ?>
                            <a href="?tab=recruitment&modal=req&requisition_page=<?= $requisitionPage - 1 ?>&requisition_dept=<?= urlencode($requisitionDeptFilter) ?>&requisition_priority=<?= urlencode($requisitionPriorityFilter) ?>&requisition_search=<?= urlencode($requisitionSearch) ?>"
                                class="js-loading-btn w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        <?php else: ?>
                            <button
                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= min(5, $requisitionTotalPages); $i++): ?>
                            <a href="?tab=recruitment&modal=req&requisition_page=<?= $i ?>&requisition_dept=<?= urlencode($requisitionDeptFilter) ?>&requisition_priority=<?= urlencode($requisitionPriorityFilter) ?>&requisition_search=<?= urlencode($requisitionSearch) ?>"
                                class="js-loading-btn w-8 h-8 flex items-center justify-center text-sm rounded-lg <?= $i == $requisitionPage ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?> transition-colors">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($requisitionPage < $requisitionTotalPages): ?>
                            <a href="?tab=recruitment&modal=req&requisition_page=<?= $requisitionPage + 1 ?>&requisition_dept=<?= urlencode($requisitionDeptFilter) ?>&requisition_priority=<?= urlencode($requisitionPriorityFilter) ?>&requisition_search=<?= urlencode($requisitionSearch) ?>"
                                class="js-loading-btn w-8 h-8 flex items-center justify-center text-sm rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
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

            <!-- Summary Section -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex flex-wrap gap-6 justify-between items-center">
                    <div class="flex gap-6">
                        <div>
                            <span class="text-sm text-gray-500">Total Requisitions</span>
                            <p class="text-2xl font-semibold text-gray-800"><?= $requisitionTotal ?></p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Pending</span>
                            <p class="text-2xl font-semibold text-yellow-600"><?= $requisitionPending ?></p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Approved</span>
                            <p class="text-2xl font-semibold text-green-600"><?= $requisitionApproved ?></p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Declined</span>
                            <p class="text-2xl font-semibold text-red-600"><?= $requisitionDeclined ?></p>
                        </div>
                    </div>
                    <!-- Added export button with loading class -->
                    <button onclick="exportRequisitions()"
                        class="js-loading-btn px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-100 px-6 py-4 flex justify-end">
            <!-- Added js-loading-btn class -->
            <button onclick="closeRequisitionModal()"
                class="js-loading-btn px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function exportRequisitions() {
        const button = event.target.closest('button');
        showButtonLoading(button);

        const url = new URL(window.location.href);
        url.pathname = '/export-requisitions';
        url.searchParams.set('dept', '<?= $requisitionDeptFilter ?>');
        url.searchParams.set('priority', '<?= $requisitionPriorityFilter ?>');
        url.searchParams.set('status', '<?= $requisitionStatusFilter ?>');
        url.searchParams.set('search', '<?= $requisitionSearch ?>');

        // The page will redirect, so loading will stay until redirect
        window.location.href = url.toString();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get("modal") === "req") {
            openModal("otherDeptRequisitionsModal");
        }
    });

    function closeRequisitionModal() {
        const url = new URL(window.location);
        url.searchParams.delete("modal");
        window.history.replaceState({}, "", url);
        closeModal("otherDeptRequisitionsModal");
    }
</script>