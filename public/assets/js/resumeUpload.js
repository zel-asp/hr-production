import supabaseClient from '/assets/js/supabase.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('applicationForm');
    if (!form) return;

    const fileInput = document.querySelector('input[name="resume"]');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const file = fileInput.files[0];

        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert("File must be under 5MB");
                return;
            }

            const fileExt = file.name.split('.').pop();
            const fileName = `${Date.now()}-${Math.random().toString(36).substring(2)}.${fileExt}`;

            const { error } = await supabaseClient.storage
                .from('resumes')
                .upload(fileName, file);

            if (error) {
                alert("Upload failed");
                console.error(error);
                return;
            }

            const { data } = supabaseClient.storage
                .from('resumes')
                .getPublicUrl(fileName);

            document.getElementById('resume_url').value = data.publicUrl;
        }

        form.submit();
    });
});