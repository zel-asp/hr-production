document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.side-tab');
    const contents = document.querySelectorAll('.tab-content');

    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'recruitment'; // fallback tab
    const activeModal = params.get('modal');

    // Remove all active classes first
    tabs.forEach(t => t.classList.remove('active'));
    contents.forEach(c => c.classList.remove('active'));

    // Activate tab based on URL param or fallback
    const selectedTab = document.querySelector(`[data-tab="${activeTab}"]`);
    const selectedContent = document.getElementById(activeTab + '-content');

    if (selectedTab && selectedContent) {
        selectedTab.classList.add('active');
        selectedContent.classList.add('active');
    }

    // Open modal if param exists
    if (activeModal) openModal(activeModal);

    // TAB CLICK HANDLER
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            tab.classList.add('active');
            const tabName = tab.dataset.tab;
            const targetContent = document.getElementById(tabName + '-content');
            if (targetContent) targetContent.classList.add('active');

            // Optional: update URL without reload
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tabName);
            history.replaceState({}, '', newUrl);

            closeMobileSidebar();
        });
    });

    // FILTER CHIPS
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const filter = chip.dataset.filter;
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');

            document.querySelectorAll('.applicant-row').forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // UPDATE STATUS BUTTONS
    document.querySelectorAll('.update-status-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const hourly_rate = btn.dataset.rate;
            const csrf = btn.dataset.csrf;
            const row = document.querySelector(`.applicant-row[data-id="${id}"]`);
            const select = row.querySelector('.status-select');
            const status = select.value;

            // Get dates based on status
            let startDate = null;
            let interviewDate = null;

            if (status === 'Hired') {
                const startDateInput = row.querySelector('.start-date-input');
                if (startDateInput) {
                    startDate = startDateInput.value;
                    if (!startDate) {
                        alert('Please select a start date for hired applicant');
                        return;
                    }
                }
            } else if (status === 'Interview') {
                const interviewDateInput = row.querySelector('.interview-date-input');
                if (interviewDateInput) {
                    interviewDate = interviewDateInput.value;
                    if (!interviewDate) {
                        alert('Please select an interview date');
                        return;
                    }
                }
            }

            try {
                const res = await fetch('/updateApplicantStatus', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id,
                        status,
                        start_date: startDate,
                        interview_date: interviewDate,
                        csrf_token: csrf,
                        hourly_rate: hourly_rate
                    })
                });

                const text = await res.text();
                console.log("Server response:", text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error("Invalid JSON returned from server.");
                }

                if (!res.ok) {
                    throw new Error(data.message || 'Server error');
                }

                if (data.success) {
                    row.dataset.status = status.toLowerCase();
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update status');
                }

            } catch (err) {
                console.error("Update error:", err);
                alert(err.message);
            }
        });
    });

    // Show/hide start date input when status changes
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('status-select')) {
            const id = e.target.dataset.id;
            const row = document.querySelector(`.applicant-row[data-id="${id}"]`);
            const startDateContainer = row.querySelector('.start-date-container');
            const interviewDateContainer = row.querySelector('.interview-date-container');

            // Hide both containers first
            startDateContainer.classList.add('hidden');
            interviewDateContainer.classList.add('hidden');

            // Show appropriate container based on status
            if (e.target.value === 'Hired') {
                startDateContainer.classList.remove('hidden');
            } else if (e.target.value === 'Interview') {
                interviewDateContainer.classList.remove('hidden');
            }
        }
    });

    // Check mobile view initially and on resize
    checkMobileView();
    window.addEventListener('resize', checkMobileView);
});

// MOBILE SIDEBAR
function toggleMobileSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.toggle('mobile-open');
    if (sidebar.classList.contains('mobile-open')) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function checkMobileView() {
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (window.innerWidth <= 768) {
        closeMobileSidebar();
    } else {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// MODALS
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal on clicking outside
window.addEventListener('click', event => {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Add this to your main.js file
document.addEventListener('DOMContentLoaded', () => {
    // Check for success message in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('task_added') === 'success') {
        showNotification('Task added successfully!', 'success');

        // Clean up URL
        const newUrl = window.location.pathname + '?tab=onboarding';
        history.replaceState({}, '', newUrl);
    }
});

// Notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } z-50 animate-slideIn`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// ONBOARDING DEPARTMENT FILTER
document.addEventListener('DOMContentLoaded', function () {
    initializeDepartmentFilter();

    // Reinitialize filter when switching to onboarding tab
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.target.id === 'onboarding-content' &&
                mutation.target.classList.contains('active')) {
                setTimeout(initializeDepartmentFilter, 100);
            }
        });
    });

    const onboardingContent = document.getElementById('onboarding-content');
    if (onboardingContent) {
        observer.observe(onboardingContent, { attributes: true, attributeFilter: ['class'] });
    }
});

function initializeDepartmentFilter() {
    const filterSelect = document.getElementById('departmentFilter');
    if (!filterSelect) return;

    // Remove existing listeners
    filterSelect.removeEventListener('change', handleDepartmentFilter);

    // Add new listener
    filterSelect.addEventListener('change', handleDepartmentFilter);

    // Check for saved filter
    const savedFilter = sessionStorage.getItem('onboardingDepartmentFilter');
    const urlParams = new URLSearchParams(window.location.search);
    const urlFilter = urlParams.get('dept');

    const activeFilter = urlFilter || savedFilter || 'all';

    if (activeFilter !== 'all') {
        filterSelect.value = activeFilter;
        applyDepartmentFilter(activeFilter);
    }
}

function handleDepartmentFilter(e) {
    const selectedDept = e.target.value;

    sessionStorage.setItem('onboardingDepartmentFilter', selectedDept);

    // Update URL
    const url = new URL(window.location);
    if (selectedDept && selectedDept !== 'all') {
        url.searchParams.set('dept', selectedDept);
    } else {
        url.searchParams.delete('dept');
    }
    history.replaceState({}, '', url);

    applyDepartmentFilter(selectedDept);
}

function applyDepartmentFilter(department) {
    const cards = document.querySelectorAll('#onboardingCardsContainer .onboarding-card');
    const filterBar = document.getElementById('filterStatusBar');
    const filterLabel = document.getElementById('activeFilterLabel');
    let visibleCount = 0;

    if (!cards.length) return;

    cards.forEach(card => {
        const cardDept = (card.dataset.department || '').toLowerCase();
        const filterDept = department.toLowerCase();

        if (department === 'all' || cardDept === filterDept) {
            card.style.display = 'block';
            visibleCount++;

            card.style.animation = 'none';
            card.offsetHeight;
            card.style.animation = 'fadeIn 0.3s ease-in-out';
        } else {
            card.style.display = 'none';
        }
    });

    if (department !== 'all' && visibleCount > 0) {
        filterBar.classList.remove('hidden');
        filterLabel.textContent = department;
    } else {
        filterBar.classList.add('hidden');
    }

    updateNoResultsMessage(visibleCount, department);
}

function updateNoResultsMessage(visibleCount, department) {
    const container = document.getElementById('onboardingCardsContainer');
    let noResultsMsg = document.getElementById('noFilterResults');

    if (visibleCount === 0 && department !== 'all') {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'noFilterResults';
            noResultsMsg.className = 'text-center py-8 text-gray-500 bg-gray-50 rounded-lg';
            container.appendChild(noResultsMsg);
        }

        noResultsMsg.innerHTML = `
            <i class="fas fa-filter text-gray-400 text-4xl mb-3"></i>
            <p class="text-lg font-medium text-gray-600">No employees found</p>
            <p class="text-sm text-gray-500 mt-1">No employees in the ${department} department</p>
            <button onclick="resetDepartmentFilter()" class="mt-3 text-sm text-primary hover:underline">
                <i class="fas fa-times mr-1"></i>Clear filter
            </button>
        `;
        noResultsMsg.style.display = 'block';
    } else {
        if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }
}

function resetDepartmentFilter() {
    const filterSelect = document.getElementById('departmentFilter');
    if (filterSelect) {
        filterSelect.value = 'all';
        handleDepartmentFilter({ target: filterSelect });
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .hidden {
        display: none;
    }
`;
document.head.appendChild(style);

function updateEmployeeDetails(select) {
    const selected = select.options[select.selectedIndex];

    if (selected.value) {
        // Update form fields
        document.getElementById('empId').value = selected.dataset.employeeId;
        document.getElementById('dept').value = selected.dataset.dept;
        document.getElementById('empName').value = selected.dataset.name;
        document.getElementById('empPosition').value = selected.dataset.position;
        document.getElementById('empEmail').value = selected.dataset.email;
        document.getElementById('empStartDate').value = selected.dataset.start;
    } else {
        document.getElementById('empId').value = '';
        document.getElementById('empName').value = '';
        document.getElementById('empPosition').value = '';
        document.getElementById('empEmail').value = '';
        document.getElementById('empStartDate').value = '';
    }
}

function updateOverallScore(employeeId) {
    let total = 0;
    for (let i = 1; i <= 5; i++) {
        const select = document.getElementById(`criteria${i}_${employeeId}`);
        total += parseInt(select.value);
    }
    const average = (total / 5).toFixed(1);
    document.getElementById(`overallScore_${employeeId}`).textContent = average;
    document.getElementById(`overall_score_input_${employeeId}`).value = average;

    let interpretation = '';
    if (average >= 4.5) interpretation = 'Outstanding';
    else if (average >= 3.5) interpretation = 'Exceeds Expectations';
    else if (average >= 2.5) interpretation = 'Meets Expectations';
    else if (average >= 1.5) interpretation = 'Developing';
    else interpretation = 'Needs Improvement';

    document.getElementById(`scoreInterpretation_${employeeId}`).textContent = interpretation;
    document.getElementById(`interpretation_input_${employeeId}`).value = interpretation;
}

// EDIT JOB MODAL HELPER
function openEditJobModal(id, position, location, shift, salary) {
    document.getElementById('editJobId').value = id;
    document.getElementById('editJobTitle').textContent = 'Edit Job: ' + position;
    document.getElementById('editJobPosition').value = position;
    document.getElementById('editJobLocation').value = location;
    document.getElementById('editJobShift').value = shift;
    document.getElementById('editJobSalary').value = salary;

    openModal('editJobModal');
}


//training
function toggleProviderFields() {
    const trainingType = document.getElementById('trainingType').value;
    const providerDropdown = document.getElementById('providerDropdown');
    const providerSelect = document.querySelector('select[name="provider_id"]');

    // Show only if 'external' is selected
    if (trainingType === 'external') {
        providerDropdown.classList.remove('hidden');
        providerSelect.setAttribute('required', 'required');
    } else {
        providerDropdown.classList.add('hidden');
        providerSelect.removeAttribute('required');
        // Optionally clear the value when hidden
        providerSelect.value = '';
    }
}

// Initialize on page load in case a default value is set
document.addEventListener('DOMContentLoaded', () => toggleProviderFields());


//competency
function showCompetencyName() {
    const employeeSelect = document.getElementById('employeeSelect');
    const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];

    const competencyDisplay = document.getElementById('competencyNameDisplay');
    const competencyIdInput = document.getElementById('competencyIdInput');

    if (selectedOption && selectedOption.value) {
        // Get competency name from the selected option's data attribute
        const competencyName = selectedOption.dataset.competencyName;
        const competencyId = selectedOption.dataset.competencyId;

        // Show the competency name
        competencyDisplay.value = competencyName;
        competencyIdInput.value = competencyId;
    } else {
        // Clear if no employee selected
        competencyDisplay.value = '';
        competencyIdInput.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    showCompetencyName();
});

function applyHcmFilter() {
    const url = new URL(window.location.href);
    const status = document.querySelector('select[name="hcm_status"]').value;
    const department = document.querySelector('select[name="hcm_department"]').value;
    const role = document.querySelector('select[name="hcm_role"]').value;

    url.searchParams.set('tab', 'hcm');
    url.searchParams.set('hcm_page', '1');

    if (status) {
        url.searchParams.set('hcm_status', status);
    } else {
        url.searchParams.delete('hcm_status');
    }

    if (department) {
        url.searchParams.set('hcm_department', department);
    } else {
        url.searchParams.delete('hcm_department');
    }

    if (role) {
        url.searchParams.set('hcm_role', role);
    } else {
        url.searchParams.delete('hcm_role');
    }

    // Preserve search term
    const search = document.querySelector('input[name="hcm_search"]').value;
    if (search) {
        url.searchParams.set('hcm_search', search);
    } else {
        url.searchParams.delete('hcm_search');
    }

    // Always move to employee list section
    url.hash = 'employeeList';

    window.location.href = url.toString();
}

// ============================================
// LOADING EFFECT HANDLER - ADD THIS SECTION
// ============================================
class FormLoadingHandler {
    constructor(options = {}) {
        this.options = {
            loadingText: 'Processing...',
            loadingClass: 'btn-loading',
            spinnerIcon: 'fa-spinner fa-spin',
            buttonSelector: 'button[type="submit"], .update-status-btn, .js-loading-btn',
            ...options
        };

        this.init();
    }

    init() {
        // Handle all form submissions (both AJAX and regular)
        document.querySelectorAll('form').forEach(form => {
            this.setupFormLoading(form);
        });

        // Handle status update buttons (your existing buttons)
        document.querySelectorAll('.update-status-btn').forEach(btn => {
            this.setupButtonLoading(btn);
        });

        // Handle any buttons with js-loading-btn class
        document.querySelectorAll('.js-loading-btn').forEach(btn => {
            this.setupButtonLoading(btn);
        });
    }

    setupFormLoading(form) {
        form.addEventListener('submit', (e) => {
            const submitButton = form.querySelector('button[type="submit"]');

            // Check if this is an AJAX form (has data-ajax attribute)
            const isAjax = form.hasAttribute('data-ajax') ||
                form.getAttribute('data-ajax') === 'true';

            if (submitButton && !submitButton.disabled) {
                this.setLoading(submitButton, true);
            }

            // Prevent double submission
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }

            form.dataset.submitting = 'true';

            // For non-AJAX forms (regular POST that reloads page)
            // We don't need to worry about clearing loading state
            // because the page will reload
            if (!isAjax) {
                // The form will submit and page will reload
                // Loading state will persist until reload
                console.log('Regular form submission - page will reload');
                return;
            }

            // For AJAX forms, we need to handle the response
            if (isAjax) {
                e.preventDefault(); // Prevent default submission
                this.handleAjaxSubmit(form, submitButton);
            }
        });
    }

    handleAjaxSubmit(form, submitButton) {
        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method || 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Handle success
                    if (form.dataset.successRedirect) {
                        window.location.href = form.dataset.successRedirect;
                    } else {
                        // Show success message and reset form
                        this.showNotification('Success!', 'success');
                        form.reset();
                    }
                } else {
                    // Handle error
                    this.showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('An error occurred', 'error');
            })
            .finally(() => {
                // Clear loading state
                if (submitButton) {
                    this.setLoading(submitButton, false);
                }
                form.dataset.submitting = 'false';
            });
    }

    setupButtonLoading(button) {
        button.addEventListener('click', (e) => {
            // Check if this button is for AJAX or regular action
            const isAjax = button.hasAttribute('data-ajax') ||
                button.classList.contains('update-status-btn'); // Your status buttons are AJAX

            if (button.disabled || button.classList.contains(this.options.loadingClass)) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }

            // Don't show loading for buttons that trigger page reload
            // unless they're specifically marked for AJAX
            if (button.closest('form') && !isAjax) {
                // Let the form handler deal with it
                return;
            }

            // For AJAX buttons, show loading
            if (isAjax) {
                this.setLoading(button, true);

                // If it's an update-status-btn, it already has its own fetch
                // We need to track when it completes
                if (button.classList.contains('update-status-btn')) {
                    this.trackAsyncOperation(button);
                }
            }
        });
    }

    trackAsyncOperation(button) {
        // Watch for when the button might be re-enabled
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'disabled' || mutation.attributeName === 'class') {
                    if (!button.disabled && !button.classList.contains(this.options.loadingClass)) {
                        // Button was re-enabled naturally, clear loading
                        this.setLoading(button, false);
                        observer.disconnect();
                    }
                }
            });
        });

        observer.observe(button, { attributes: true });

        // Safety timeout
        setTimeout(() => {
            if (button.classList.contains(this.options.loadingClass)) {
                this.setLoading(button, false);
            }
            observer.disconnect();
        }, 10000);
    }

    setLoading(element, isLoading) {
        if (isLoading) {
            // Store original content if not already stored
            if (!element.dataset.originalHtml) {
                element.dataset.originalHtml = element.innerHTML;
            }

            element.classList.add(this.options.loadingClass);
            element.disabled = true;

            // Handle different button types
            if (element.classList.contains('update-status-btn')) {
                element.innerHTML = `<i class="fas ${this.options.spinnerIcon} mr-2"></i>Updating...`;
            } else {
                // Check if button already has an icon
                const icon = element.querySelector('i.fa, i.fas, i.far');
                if (icon) {
                    icon.dataset.originalClass = icon.className;
                    icon.className = `fas ${this.options.spinnerIcon}`;
                } else {
                    element.innerHTML = `<i class="fas ${this.options.spinnerIcon} mr-2"></i>${this.options.loadingText}`;
                }
            }
        } else {
            element.classList.remove(this.options.loadingClass);
            element.disabled = false;

            // Restore original content
            if (element.dataset.originalHtml) {
                element.innerHTML = element.dataset.originalHtml;
                delete element.dataset.originalHtml;
            }

            // Restore original icon if it existed
            const icon = element.querySelector('i.fa, i.fas, i.far');
            if (icon && icon.dataset.originalClass) {
                icon.className = icon.dataset.originalClass;
                delete icon.dataset.originalClass;
            }
        }
    }

    showNotification(message, type = 'success') {
        // Use your existing notification function
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50 animate-slideIn`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    }

    // Static method to manually set loading state
    static setButtonLoading(button, isLoading) {
        const handler = new FormLoadingHandler();
        handler.setLoading(button, isLoading);
    }
}

// Add CSS for loading effects
const loadingStyles = document.createElement('style');
loadingStyles.textContent = `
    /* Loading button styles */
    button.btn-loading,
    .update-status-btn.btn-loading,
    .js-loading-btn.btn-loading {
        position: relative;
        cursor: wait !important;
        opacity: 0.7;
        pointer-events: none;
        transition: all 0.2s ease;
    }
    
    /* Pulse animation */
    @keyframes btnPulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
    
    button.btn-loading,
    .update-status-btn.btn-loading {
        animation: btnPulse 1.5s ease-in-out infinite;
    }
    
    /* Disable form during submission */
    form[data-submitting="true"] {
        opacity: 0.8;
        pointer-events: none;
    }
    
    form[data-submitting="true"] button[type="submit"] {
        cursor: wait !important;
    }
    
    /* Spinner rotation */
    .fa-spin {
        animation: fa-spin 2s infinite linear;
    }
    
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Prevent double clicks */
    .btn-loading {
        user-select: none;
    }
`;

document.head.appendChild(loadingStyles);

// Initialize loading handler when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new FormLoadingHandler();
});

// Add helper functions for manual loading control
window.showButtonLoading = function (element) {
    if (element) {
        FormLoadingHandler.setButtonLoading(element, true);
    }
};

window.hideButtonLoading = function (element) {
    if (element) {
        FormLoadingHandler.setButtonLoading(element, false);
    }
};

// Optional: Mark your status update buttons as AJAX
document.addEventListener('DOMContentLoaded', () => {
    // Your existing update-status-btn click handler will still work
    // The loading handler will just add visual feedback
    document.querySelectorAll('.update-status-btn').forEach(btn => {
        btn.setAttribute('data-ajax', 'true');
    });
});
