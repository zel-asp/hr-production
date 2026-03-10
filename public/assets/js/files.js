import supabaseClient from '/assets/js/supabase.js';

const forms = document.querySelectorAll(".document-upload-form");

forms.forEach(form => {
    const fileInput = form.querySelector(".file-input");
    const hiddenInput = form.querySelector(".public-url-input");
    const submitBtn = form.querySelector(".submit-btn");
    const fileLabel = form.querySelector(".file-label");

    const bucket = form.dataset.bucket;
    const documentType = form.dataset.documentType;

    // Disable submit button initially if no file is selected
    if (!fileInput.files.length) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Enable/disable submit button based on file selection
    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

            // Show filename
            const fileName = this.files[0].name;
            const fileSize = (this.files[0].size / 1024).toFixed(2) + ' KB';
            if (fileLabel) {
                fileLabel.innerHTML = `<span class="text-green-600">✓ ${fileName}</span> <span class="text-xs text-gray-400">(${fileSize})</span>`;
            }
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            if (fileLabel) {
                fileLabel.innerHTML = 'Click to browse';
            }
        }
    });

    // Create a named function for the submit handler
    const handleSubmit = async function (event) {
        event.preventDefault();

        const file = fileInput.files[0];

        if (!file) {
            alert("Please select a file first.");
            return;
        }

        // Limit 5MB
        if (file.size > 5 * 1024 * 1024) {
            alert("File must be less than 5MB.");
            return;
        }

        // Show loading state
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Uploading...';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

        const fileExt = file.name.split('.').pop();
        const fileName = `${documentType}_${Date.now()}.${fileExt}`;

        try {
            // Upload to Supabase
            const { error } = await supabaseClient.storage
                .from(bucket)
                .upload(fileName, file);

            if (error) {
                throw new Error(error.message);
            }

            // Get public URL
            const { data } = supabaseClient.storage
                .from(bucket)
                .getPublicUrl(fileName);

            const publicUrl = data.publicUrl;

            // Put URL into hidden input
            hiddenInput.value = publicUrl;

            console.log("Uploaded file URL:", publicUrl);

            // Show success state
            submitBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Uploaded!';
            submitBtn.classList.remove('bg-primary', 'hover:bg-gray-900');
            submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');

            // Remove this event listener and submit the form
            form.removeEventListener('submit', handleSubmit);

            // Submit the form normally
            form.submit();

        } catch (error) {
            alert("Upload failed: " + error.message);
            // Reset button
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    };

    // Add the event listener
    form.addEventListener('submit', handleSubmit);
});