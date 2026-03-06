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
            <form class="document-upload-form space-y-4" data-bucket="nbi-clearance" data-document-type="nbi_clearance">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition">
                        <input type="file" name="document_file" class="hidden file-input" id="nbi_file"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <div onclick="document.getElementById('nbi_file').click()" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500 file-label">Click to browse</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="nbi_clearance_url" class="public-url-input" value="">

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 bg-primary hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition opacity-50 cursor-not-allowed"
                    disabled>
                    <i class="fas fa-upload mr-1"></i> Upload NBI Clearance
                </button>
            </form>
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
            <form class="document-upload-form space-y-4" data-bucket="medical" data-document-type="medical_result">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition">
                        <input type="file" name="document_file" class="hidden file-input" id="medical_file"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <div onclick="document.getElementById('medical_file').click()" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500 file-label">Click to browse</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="medical_result_url" class="public-url-input" value="">

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 bg-primary hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition opacity-50 cursor-not-allowed"
                    disabled>
                    <i class="fas fa-upload mr-1"></i> Upload Medical Result
                </button>
            </form>
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
            <form class="document-upload-form space-y-4" data-bucket="birth-certificate"
                data-document-type="birth_certificate">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span
                            class="text-red-400">*</span></label>
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-gray-300 transition">
                        <input type="file" name="document_file" class="hidden file-input" id="birth_file"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <div onclick="document.getElementById('birth_file').click()" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500 file-label">Click to browse</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="birth_certificate_url" class="public-url-input" value="">

                <button type="submit"
                    class="submit-btn w-full px-4 py-2.5 bg-primary hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition opacity-50 cursor-not-allowed"
                    disabled>
                    <i class="fas fa-upload mr-1"></i> Upload Birth Certificate
                </button>
            </form>
        </div>
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
                <div class="bg-primary h-2.5 rounded-full" id="uploadProgress" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>
<!-- Upload History -->
<div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-semibold text-gray-800">Recent Uploads</h3>
    </div>
    <div class="p-6">
        <div class="space-y-2">
            <div class="text-sm text-gray-500 text-center py-2">
                No recent uploads
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
            const textElement = parentDiv.querySelector('.text-sm.text-gray-500');
            if (fileName) {
                textElement.textContent = fileName;
                textElement.classList.add('font-medium', 'text-gray-700');
            } else {
                textElement.textContent = 'Click to browse';
                textElement.classList.remove('font-medium', 'text-gray-700');
            }
        });
    });
</script>