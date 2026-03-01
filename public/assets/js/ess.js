(function () {
    const body = document.body;

    // ========================
    // MODAL FUNCTIONS
    // ========================
    function closeAllModals() {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => modal.classList.add('hidden'));
        body.classList.remove('modal-open');
    }

    // Expose openModal globally
    window.openModal = function (id) {
        closeAllModals();
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            body.classList.add('modal-open');

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('modal', id);
            window.history.replaceState({}, '', url);
        }
    };

    // Expose closeModal globally
    window.closeModal = function (id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
        if (!document.querySelector('[id$="Modal"]:not(.hidden)')) {
            body.classList.remove('modal-open');

            // Remove modal from URL
            const url = new URL(window.location);
            url.searchParams.delete('modal');
            window.history.replaceState({}, '', url);
        }
    };

    // Expose switchTab globally (optional, but good for consistency)
    window.switchTab = function (tabName) {
        const requestsPanel = document.getElementById('requestsPanel');
        const tasksPanel = document.getElementById('tasksPanel');
        const requestsBtn = document.getElementById('tabRequestsBtn');
        const tasksBtn = document.getElementById('tabTasksBtn');

        if (!requestsPanel || !tasksPanel || !requestsBtn || !tasksBtn) return;

        // Hide all panels
        requestsPanel.classList.add('hidden');
        tasksPanel.classList.add('hidden');

        // Reset button classes
        requestsBtn.classList.remove('tab-active');
        requestsBtn.classList.add('tab-inactive');
        tasksBtn.classList.remove('tab-active');
        tasksBtn.classList.add('tab-inactive');

        // Show selected panel
        if (tabName === 'tasks') {
            tasksPanel.classList.remove('hidden');
            tasksBtn.classList.remove('tab-inactive');
            tasksBtn.classList.add('tab-active');
        } else {
            requestsPanel.classList.remove('hidden');
            requestsBtn.classList.remove('tab-inactive');
            requestsBtn.classList.add('tab-active');
        }

        // Update URL - preserve existing parameters
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);

        // Preserve page parameter if it exists
        const currentPage = url.searchParams.get('page');
        if (currentPage) {
            url.searchParams.set('page', currentPage);
        }

        // Preserve modal parameter if it exists
        const currentModal = url.searchParams.get('modal');
        if (currentModal) {
            url.searchParams.set('modal', currentModal);
        }

        window.history.replaceState({}, '', url);
    };

    // ========================
    // INITIALIZE
    // ========================
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        const modal = urlParams.get('modal');

        console.log('DOM Loaded - URL params:', { tab, modal }); // Debug log

        // Set initial tab
        window.switchTab(tab === 'tasks' ? 'tasks' : 'requests');

        // Open modal if specified
        if (modal) {
            console.log('Attempting to open modal:', modal); // Debug log
            setTimeout(() => {
                const modalEl = document.getElementById(modal);
                if (modalEl) {
                    console.log('Modal found, opening...'); // Debug log
                    modalEl.classList.remove('hidden');
                    body.classList.add('modal-open');
                } else {
                    console.log('Modal not found:', modal); // Debug log
                }
            }, 200);
        }

        // ========================
        // EVENT LISTENERS
        // ========================

        // Modal triggers - use the global functions
        document.getElementById('openProfileModalBtn')?.addEventListener('click', () => window.openModal('profileModal'));
        document.getElementById('openLeaveModalBtn')?.addEventListener('click', () => window.openModal('leaveModal'));
        document.getElementById('openAttendanceModalBtn')?.addEventListener('click', () => window.openModal('attendanceModal'));
        document.getElementById('openPayslipModalBtn')?.addEventListener('click', () => window.openModal('payslipModal'));
        document.getElementById('openSettingsModalBtn')?.addEventListener('click', () => window.openModal('settingsModal'));

        // View all triggers
        document.getElementById('viewAllRequestsBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            window.openModal('viewAllRequestsModal');
        });

        document.getElementById('viewAllTasksBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            window.openModal('viewAllTasksModal');
        });

        // Tab buttons
        document.getElementById('tabRequestsBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            window.switchTab('requests');
        });

        document.getElementById('tabTasksBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            window.switchTab('tasks');
        });

        // Close modal buttons
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                window.closeModal(this.getAttribute('data-modal'));
            });
        });

        // Close modal when clicking outside
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) window.closeModal(this.id);
            });
        });
    });
})();