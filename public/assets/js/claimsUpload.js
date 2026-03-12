import supabaseClient from '/public/assets/js/supabase.js';

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on the claims page by looking for required elements
    const form = document.querySelector("form");
    const receiptInput = document.getElementById("receipt");
    const receiptUrlInput = document.getElementById("receipt_url");
    const filePreview = document.getElementById("filePreview");
    const fileNameSpan = document.getElementById("fileName");

    // If any of the required elements don't exist, exit silently
    if (!form || !receiptInput || !receiptUrlInput || !filePreview || !fileNameSpan) {
        console.log('Claims upload form not found - skipping initialization');
        return;
    }

    console.log('Claims upload initialized');

    let uploaded = false;

    // Upload when file selected
    receiptInput.addEventListener("change", async function () {
        const file = receiptInput.files[0];
        if (!file) return;

        // Check file size
        if (file.size > 5 * 1024 * 1024) {
            alert("File must be less than 5MB");
            receiptInput.value = "";
            return;
        }

        // Show loading state
        const originalText = form.querySelector('button[type="submit"]')?.innerHTML;
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            submitBtn.disabled = true;
        }

        const fileExt = file.name.split('.').pop();
        const fileName = `claim_${Date.now()}.${fileExt}`;

        try {
            // Upload to Supabase
            const { error } = await supabaseClient.storage
                .from("claims")
                .upload(fileName, file);

            if (error) {
                throw new Error(error.message);
            }

            // Get public URL
            const { data } = supabaseClient.storage
                .from("claims")
                .getPublicUrl(fileName);

            receiptUrlInput.value = data.publicUrl;

            // Show file preview
            filePreview.classList.remove("hidden");
            fileNameSpan.textContent = file.name;

            uploaded = true;

            // Update submit button
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Upload Complete';
                submitBtn.classList.remove('bg-primary', 'hover:bg-gray-900');
                submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');

                // Re-enable after 2 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalText || 'Submit Claim';
                    submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    submitBtn.classList.add('bg-primary', 'hover:bg-gray-900');
                    submitBtn.disabled = false;
                }, 2000);
            }

        } catch (error) {
            alert("Upload failed: " + error.message);
            if (submitBtn) {
                submitBtn.innerHTML = originalText || 'Submit Claim';
                submitBtn.disabled = false;
            }
        }
    });

    // Prevent submit if no upload
    form.addEventListener("submit", function (e) {
        if (!uploaded) {
            e.preventDefault();
            alert("Please upload receipt first.");

            // Highlight the upload area
            receiptInput.closest('.border-2')?.classList.add('border-red-500', 'bg-red-50');
            setTimeout(() => {
                receiptInput.closest('.border-2')?.classList.remove('border-red-500', 'bg-red-50');
            }, 3000);
        }
    });

    // Optional: Add drag and drop visual feedback
    const dropZone = receiptInput.closest('.border-2');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('border-primary', 'bg-blue-50');
        }

        function unhighlight() {
            dropZone.classList.remove('border-primary', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                receiptInput.files = files;
                // Trigger change event manually
                const event = new Event('change', { bubbles: true });
                receiptInput.dispatchEvent(event);
            }
        }
    }
});