<?php if (!empty($applicants)): ?>
    <?php foreach ($applicants as $applicant): ?>
        <div id="applicantModal<?= $applicant['id'] ?>" class="modal">
            <div class="modal-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">
                        <?= htmlspecialchars($applicant['full_name']) ?> - Applicant Details
                    </h3>
                    <button onclick="closeModal('applicantModal<?= $applicant['id'] ?>')"
                        class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Position</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($applicant['position']) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Applied Date</p>
                            <p class="font-medium">
                                <?= htmlspecialchars(date('M d, Y', strtotime($applicant['created_at']))) ?>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($applicant['email']) ?>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($applicant['phone']) ?>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Experience</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($applicant['experience']) ?>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Education</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($applicant['education']) ?>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Skills</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($applicant['skills']) ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($applicant['resume_path'])): ?>
                        <div class="border-t pt-4">
                            <h4 class="font-medium mb-2">Resume</h4>
                            <a href="<?= htmlspecialchars($applicant['resume_path']) ?>" target="_blank"
                                class="w-full text-left border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition duration-200 flex items-center gap-3">
                                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                <div>
                                    <p class="font-medium">
                                        <?= basename($applicant['resume_path']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500">Uploaded
                                        <?= date('M d, Y', strtotime($applicant['created_at'])) ?>
                                    </p>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button class="px-4 py-2 bg-gray-200 rounded-lg"
                        onclick="closeModal('applicantModal<?= $applicant['id'] ?>')">Close</button>
                    <button class="btn-success" onclick="scheduleInterview(<?= $applicant['id'] ?>)">Schedule Interview</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>