<!-- Header Section -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-800">Employee Documents Upload</h2>
        <p class="text-gray-500 text-sm mt-1">Upload required employee documents and clearances</p>
    </div>
</div>

<!-- Document Upload Forms Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- NBI Clearance Upload Form -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-red-50 to-white">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-fingerprint text-red-500"></i>
                NBI Clearance
            </h3>
        </div>
        <div class="p-6">
            <form action="/upload-document" method="POST" class="document-upload-form space-y-4"
                data-bucket="nbi-clearance" data-document-type="nbi_clearance">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="document_type" value="nbi_clearance">
                <input type="hidden" name="public_url" class="public-url-input"
                    value="<?= htmlspecialchars($employeeInfo['nbi_clearance'] ?? '') ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition <?= !empty($employeeInfo['nbi_clearance']) ? 'border-green-300 bg-green-50' : '' ?>">
                        <input type="file" name="document_file" class="hidden file-input" id="nbi_file"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <div onclick="document.getElementById('nbi_file').click()" class="cursor-pointer">
                            <i
                                class="fas fa-cloud-upload-alt text-2xl <?= !empty($employeeInfo['nbi_clearance']) ? 'text-green-500' : 'text-gray-300' ?> mb-2"></i>
                            <p
                                class="text-sm <?= !empty($employeeInfo['nbi_clearance']) ? 'text-green-600' : 'text-gray-500' ?> file-label">
                                <?= !empty($employeeInfo['nbi_clearance']) ? '✓ File already uploaded. Click to replace.' : 'Click to browse' ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 <?= !empty($employeeInfo['nbi_clearance']) ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-gray-900' ?> text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-upload mr-1"></i>
                    <?= !empty($employeeInfo['nbi_clearance']) ? 'Update NBI Clearance' : 'Upload NBI Clearance' ?>
                </button>
            </form>

            <!-- Display uploaded file if exists -->
            <?php if (!empty($employeeInfo['nbi_clearance'])): ?>
                <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-xs text-green-700 font-medium">Uploaded</span>
                    </div>
                    <a href="<?= htmlspecialchars($employeeInfo['nbi_clearance']) ?>" target="_blank"
                        class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 border border-blue-200 transition flex items-center gap-1">
                        <i class="fas fa-eye"></i>
                        View File
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Medical Result Upload Form -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-teal-50 to-white">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-notes-medical text-teal-500"></i>
                Medical Result
            </h3>
        </div>
        <div class="p-6">
            <form action="/upload-document" method="POST" class="document-upload-form space-y-4" data-bucket="medical"
                data-document-type="medical_result">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="document_type" value="medical_result">
                <input type="hidden" name="public_url" class="public-url-input"
                    value="<?= htmlspecialchars($employeeInfo['medical_result'] ?? '') ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition <?= !empty($employeeInfo['medical_result']) ? 'border-green-300 bg-green-50' : '' ?>">
                        <input type="file" name="document_file" class="hidden file-input" id="medical_file"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <div onclick="document.getElementById('medical_file').click()" class="cursor-pointer">
                            <i
                                class="fas fa-cloud-upload-alt text-2xl <?= !empty($employeeInfo['medical_result']) ? 'text-green-500' : 'text-gray-300' ?> mb-2"></i>
                            <p
                                class="text-sm <?= !empty($employeeInfo['medical_result']) ? 'text-green-600' : 'text-gray-500' ?> file-label">
                                <?= !empty($employeeInfo['medical_result']) ? '✓ File already uploaded. Click to replace.' : 'Click to browse' ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 <?= !empty($employeeInfo['medical_result']) ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-gray-900' ?> text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-upload mr-1"></i>
                    <?= !empty($employeeInfo['medical_result']) ? 'Update Medical Result' : 'Upload Medical Result' ?>
                </button>
            </form>

            <?php if (!empty($employeeInfo['medical_result'])): ?>
                <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-xs text-green-700 font-medium">Uploaded</span>
                    </div>
                    <a href="<?= htmlspecialchars($employeeInfo['medical_result']) ?>" target="_blank"
                        class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 border border-blue-200 transition flex items-center gap-1">
                        <i class="fas fa-eye"></i>
                        View File
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Resume Upload Form -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-teal-50 to-white">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-notes-medical text-teal-500"></i>
                Resume
            </h3>
        </div>
        <div class="p-6">
            <form action="/upload-document" method="POST" class="document-upload-form space-y-4" data-bucket="resumes"
                data-document-type="resume">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="document_type" value="resume">
                <input type="hidden" name="public_url" class="public-url-input"
                    value="<?= htmlspecialchars($employeeInfo['resume'] ?? '') ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition <?= !empty($employeeInfo['resume']) ? 'border-green-300 bg-green-50' : '' ?>">
                        <input type="file" name="document_file" class="hidden file-input" id="resume_file"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <div onclick="document.getElementById('resume_file').click()" class="cursor-pointer">
                            <i
                                class="fas fa-cloud-upload-alt text-2xl <?= !empty($employeeInfo['resume']) ? 'text-green-500' : 'text-gray-300' ?> mb-2"></i>
                            <p
                                class="text-sm <?= !empty($employeeInfo['resume']) ? 'text-green-600' : 'text-gray-500' ?> file-label">
                                <?= !empty($employeeInfo['resume']) ? '✓ File already uploaded. Click to replace.' : 'Click to browse' ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 <?= !empty($employeeInfo['resume']) ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-gray-900' ?> text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-upload mr-1"></i>
                    <?= !empty($employeeInfo['resume']) ? 'Update Medical Result' : 'Upload Medical Result' ?>
                </button>
            </form>

            <?php if (!empty($employeeInfo['resume'])): ?>
                <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-xs text-green-700 font-medium">Uploaded</span>
                    </div>
                    <a href="<?= htmlspecialchars($employeeInfo['resume']) ?>" target="_blank"
                        class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 border border-blue-200 transition flex items-center gap-1">
                        <i class="fas fa-eye"></i>
                        View File
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Birth Certificate Upload Form -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-green-50 to-white">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-id-card text-green-500"></i>
                Birth Certificate
            </h3>
        </div>
        <div class="p-6">
            <form action="/upload-document" method="POST" class="document-upload-form space-y-4"
                data-bucket="birth-certificate" data-document-type="birth_certificate">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="document_type" value="birth_certificate">
                <input type="hidden" name="public_url" class="public-url-input"
                    value="<?= htmlspecialchars($employeeInfo['birth_certificate'] ?? '') ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition <?= !empty($employeeInfo['birth_certificate']) ? 'border-green-300 bg-green-50' : '' ?>">
                        <input type="file" name="document_file" class="hidden file-input" id="birth_file"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <div onclick="document.getElementById('birth_file').click()" class="cursor-pointer">
                            <i
                                class="fas fa-cloud-upload-alt text-2xl <?= !empty($employeeInfo['birth_certificate']) ? 'text-green-500' : 'text-gray-300' ?> mb-2"></i>
                            <p
                                class="text-sm <?= !empty($employeeInfo['birth_certificate']) ? 'text-green-600' : 'text-gray-500' ?> file-label">
                                <?= !empty($employeeInfo['birth_certificate']) ? '✓ File already uploaded. Click to replace.' : 'Click to browse' ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 <?= !empty($employeeInfo['birth_certificate']) ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-gray-900' ?> text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-upload mr-1"></i>
                    <?= !empty($employeeInfo['birth_certificate']) ? 'Update Birth Certificate' : 'Upload Birth Certificate' ?>
                </button>
            </form>

            <?php if (!empty($employeeInfo['birth_certificate'])): ?>
                <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-xs text-green-700 font-medium">Uploaded</span>
                    </div>
                    <a href="<?= htmlspecialchars($employeeInfo['birth_certificate']) ?>" target="_blank"
                        class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 border border-blue-200 transition flex items-center gap-1">
                        <i class="fas fa-eye"></i>
                        View File
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upload History -->
<div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Recent Uploads</h3>
        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
            <?= count($employeeDocuments) ?> document<?= count($employeeDocuments) != 1 ? 's' : '' ?>
        </span>
    </div>

    <div class="p-6">
        <?php if (!empty($employeeDocuments)): ?>
            <div class="space-y-3">
                <?php foreach ($employeeDocuments as $doc):
                    $color = getDocumentColor($doc['type']);
                    ?>
                    <div
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-<?= $color ?>-100 rounded-lg flex items-center justify-center">
                                <i class="fas <?= $doc['icon'] ?> text-<?= $color ?>-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800"><?= $doc['label'] ?></p>
                                <p class="text-xs text-gray-400">
                                    Uploaded <?= date('M j, Y', strtotime($doc['uploaded_at'])) ?>
                                    <?php if (date('Y-m-d') == date('Y-m-d', strtotime($doc['uploaded_at']))): ?>
                                        <span class="ml-2 text-green-600">(Today)</span>
                                    <?php elseif (date('Y-m-d', strtotime('-1 day')) == date('Y-m-d', strtotime($doc['uploaded_at']))): ?>
                                        <span class="ml-2 text-blue-600">(Yesterday)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <a href="<?= htmlspecialchars($doc['url']) ?>" target="_blank"
                            class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 border border-blue-200 transition flex items-center gap-1">
                            <i class="fas fa-external-link-alt text-xs"></i>
                            View
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-cloud-upload-alt text-4xl mb-3 text-gray-300"></i>
                <p class="text-lg font-medium">No documents uploaded yet</p>
                <p class="text-sm text-gray-400">Upload your documents above to see them here</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Progress Modal (Optional but recommended) -->
<div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-primary mb-4"></i>
            <h3 class="text-lg font-semibold mb-2" id="uploadModalTitle">Uploading...</h3>
            <p class="text-sm text-gray-600 mb-4" id="uploadModalMessage">Please wait while your document is being
                uploaded</p>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-primary h-2.5 rounded-full w-0" id="uploadProgress"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Show filename when selected
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function (e) {
            const fileName = e.target.files[0]?.name;
            const parentDiv = this.closest('.border-2');
            const textElement = parentDiv.querySelector('.text-sm.text-gray-500, .text-sm.text-green-600');
            const iconElement = parentDiv.querySelector('.fas.fa-cloud-upload-alt');

            if (fileName) {
                textElement.textContent = 'Selected: ' + fileName;
                textElement.classList.add('font-medium', 'text-gray-700');
                textElement.classList.remove('text-green-600');
                if (iconElement) {
                    iconElement.classList.remove('text-green-500');
                    iconElement.classList.add('text-blue-500');
                }
            } else {
                // Check if there's an existing file
                const hasExisting = parentDiv.closest('form').querySelector('.public-url-input').value;
                if (hasExisting) {
                    textElement.textContent = '✓ File already uploaded. Click to replace.';
                    textElement.classList.add('text-green-600');
                } else {
                    textElement.textContent = 'Click to browse';
                    textElement.classList.remove('font-medium', 'text-gray-700', 'text-green-600');
                }
                if (iconElement && !hasExisting) {
                    iconElement.classList.remove('text-blue-500', 'text-green-500');
                    iconElement.classList.add('text-gray-300');
                }
            }
        });
    });

</script>