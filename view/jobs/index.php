<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HotelJobs · Complete Application</title>
        <!-- Tailwind + Font Awesome -->
        <link rel="stylesheet" href="/assets/css/output.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            .form-transition {
                transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            }

            .form-hidden {
                opacity: 0;
                transform: translateY(20px);
                pointer-events: none;
                display: none;
            }

            .form-visible {
                opacity: 1;
                transform: translateY(0);
                pointer-events: all;
                display: block;
            }

            .apply-btn-active {
                background-color: #e5e7eb;
                border-color: #9ca3af;
            }
        </style>
    </head>

    <body class="bg-gray-100 p-4 md:p-8 antialiased">
        <?php require base_path('view/partials/message.php'); ?>
        <!-- Main Container - Separated from background -->
        <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <!-- Page Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Join our team</h1>
                <p class="text-gray-500">Browse open positions and submit your application</p>
            </div>

            <!-- Job Listings Section -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-5 pb-2 border-b border-gray-100">
                    <i class="fas fa-briefcase text-primary"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Open Positions</h2>
                    <?php if (!empty($jobPostings)): ?>
                        <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                            <?= count($jobPostings) ?> available
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($jobPostings)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        <?php foreach ($jobPostings as $index => $job): ?>
                            <div class="job-card bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
                                data-job-index="<?= $index ?>">
                                <!-- Card Header with subtle linear -->
                                <div class="px-5 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white">
                                    <div class="flex items-start justify-between">
                                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($job['position']) ?></h3>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                            Hiring
                                        </span>
                                    </div>
                                    <p class="text-xs text-primary mt-1"><?= htmlspecialchars($job['department']) ?></p>
                                </div>

                                <!-- Card Body -->
                                <div class="p-5">
                                    <!-- Quick Info Pills -->
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-50 text-gray-600 border border-gray-100">
                                            <i class="fas fa-clock mr-1 text-primary"></i>
                                            <?= htmlspecialchars($job['shift']) ?>
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-50 text-gray-600 border border-gray-100">
                                            <i class="fas fa-map-pin mr-1 text-primary"></i>
                                            <?= htmlspecialchars($job['location']) ?>
                                        </span>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                        <?= !empty($job['description']) ? htmlspecialchars($job['description']) : 'Join our team and grow your career in hospitality.' ?>
                                    </p>

                                    <!-- Salary and Apply -->
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                        <div class="flex items-center">
                                            <span class="text-xs text-primary mr-1">💰</span>
                                            <span
                                                class="text-sm font-medium text-gray-800"><?= htmlspecialchars($job['salary']) ?>/hr</span>
                                        </div>
                                        <button
                                            onclick="showApplicationForm('<?= htmlspecialchars($job['position']) ?>', <?= $index ?>, <?= (float) $job['salary'] ?>)"
                                            class="apply-btn text-sm text-white bg-primary px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-10 text-center">
                        <div
                            class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fas fa-briefcase text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 text-sm mb-1">No open positions at the moment</p>
                        <p class="text-xs text-primary">Please check back later for new opportunities</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Application Form Section (Hidden Initially) -->
            <div id="applySection" class="form-hidden form-transition mt-8">
                <div
                    class="bg-linear-to-r from-gray-50 to-white rounded-xl border border-gray-200 p-6 shadow-lg relative overflow-hidden">
                    <!-- Decorative accent -->

                    <!-- Header with close button -->
                    <div class="flex items-center justify-between mb-5 pb-2 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-signature text-gray-600"></i>
                            <h2 class="text-lg font-semibold text-gray-800">Apply for <span id="selected-position-title"
                                    class="text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md"></span></h2>
                        </div>
                        <button onclick="hideApplicationForm()"
                            class="text-primary hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition-colors duration-200"
                            title="Close form">
                            <i class="fas fa-times fa-lg"></i>
                        </button>
                    </div>

                    <!-- Selected position badge -->
                    <div
                        class="mb-4 inline-flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-full border border-gray-200">
                        <i class="fas fa-briefcase text-gray-500 text-xs"></i>
                        <span class="text-xs font-medium text-gray-700">You're applying for:</span>
                        <span id="selected-position-display" class="text-xs font-semibold text-gray-900"></span>
                    </div>

                    <form method="POST" action="/submitApplication" class="space-y-4" id="applicationForm">
                        <input type="hidden" name="position" id="selected-position" value="">
                        <input type="hidden" name="ratePerHour" id="rate" value="">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Full name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="full_name" placeholder="Jamie Smith" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Email <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="email" placeholder="you@example.com" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Phone <span class="text-red-400">*</span>
                                </label>
                                <input type="tel" name="phone" placeholder="(555) 123-4567" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Gender <span class="text-red-400">*</span>
                                </label>
                                <select name="gender" id="gender" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                                    <option value="" disabled selected>Select your gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Age -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Age <span class="text-red-400">*</span>
                                </label>
                                <input type="number" name="age" placeholder="18" min="18" max="100" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                            </div>

                            <!-- Experience -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Experience <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="experience" placeholder="e.g. 3 years in restaurant" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                            </div>

                            <!-- Education -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Education <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="education" placeholder="e.g. Culinary arts diploma" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm">
                            </div>
                        </div>

                        <!-- Skills -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                Skills <span class="text-red-400">*</span>
                            </label>
                            <textarea name="skills" rows="2" placeholder="e.g. POS systems, multilingual, food safety"
                                required
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm"></textarea>
                        </div>

                        <!-- Resume -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                Resume (optional)
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="file" name="resume" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-200 file:text-gray-600 hover:file:bg-gray-300 border border-gray-200 rounded-lg">
                                <input type="hidden" name="resume_url" id="resume_url">
                            </div>
                            <p class="text-xs text-primary mt-1">PDF, DOC, or image (max 5MB)</p>
                        </div>

                        <!-- Cover Note -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                                Cover note (optional)
                            </label>
                            <textarea name="cover_note" rows="2" placeholder="Anything else you'd like us to know..."
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-primary transition-all duration-200 text-sm"></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="hideApplicationForm()"
                                class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 text-sm font-medium text-white bg-primary rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="mt-6 text-center text-xs text-primary">
                <p>All applications are reviewed within 3-5 business days. We'll contact you for next steps.</p>
            </div>
        </div>

        <script>
            function showApplicationForm(position, index, rate) {
                // Set the position values
                document.getElementById('selected-position').value = position;
                document.getElementById('selected-position-title').textContent = position;
                document.getElementById('selected-position-display').textContent = position;
                document.getElementById('selected-position-display').textContent = position;
                document.getElementById('rate').value = rate;

                console.log(rate);


                // Show the form with animation
                const formSection = document.getElementById('applySection');
                formSection.classList.remove('form-hidden');
                formSection.classList.add('form-visible');

                // Smooth scroll to form
                setTimeout(() => {
                    formSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);

                // Highlight the selected job card (optional)
                document.querySelectorAll('.job-card').forEach(card => {
                    card.classList.remove('ring-2', 'ring-primary');
                });
                const selectedCard = document.querySelector(`.job-card[data-job-index="${index}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('ring-2', 'ring-primary');
                }
            }

            function hideApplicationForm() {
                const formSection = document.getElementById('applySection');
                formSection.classList.remove('form-visible');
                formSection.classList.add('form-hidden');

                // Remove highlight from job cards
                document.querySelectorAll('.job-card').forEach(card => {
                    card.classList.remove('ring-2', 'ring-primary');
                });
            }
        </script>
    </body>
    <script src="/assets/js/resumeUpload.js" type="module"></script>

</html>