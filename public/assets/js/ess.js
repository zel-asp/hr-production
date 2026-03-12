(function () {
    const body = document.body;

    // ========================
    // GLOBAL FUNCTIONS (accessible to inline HTML)
    // ========================

    // Sidebar toggle function - IMPROVED VERSION
    window.toggleSidebar = function () {
        console.log('Toggle sidebar called'); // For debugging
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (sidebar && overlay) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');

            // Prevent body scrolling when sidebar is open on mobile
            if (window.innerWidth < 1024) {
                if (sidebar.classList.contains('open')) {
                    body.style.overflow = 'hidden';
                } else {
                    body.style.overflow = '';
                }
            }
        } else {
            console.error('Sidebar or overlay element not found');
        }
    };

    // Function to close sidebar
    window.closeSidebar = function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (sidebar && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            body.style.overflow = '';
        }
    };

    // ========================
    // MODAL FUNCTIONS
    // ========================
    function closeAllModals() {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => modal.classList.add('hidden'));
        body.classList.remove('modal-open');
    }

    // Expose openModal globally
    window.openModal = function (modalId) {
        closeAllModals();
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('modal', modalId);
            window.history.replaceState({}, '', url);
        }
    };

    // Expose closeModal globally
    window.closeModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (!document.querySelector('[id$="Modal"]:not(.hidden)')) {
                body.classList.remove('modal-open');
                document.body.style.overflow = '';

                // Remove modal from URL
                const url = new URL(window.location);
                url.searchParams.delete('modal');
                window.history.replaceState({}, '', url);
            }
        }
    };

    // Close modal when clicking outside
    window.closeModalOnOutsideClick = function (event, modalId) {
        if (event.target === event.currentTarget) {
            closeModal(modalId);
        }
    };

    // ========================
    // TAB FUNCTIONS
    // ========================
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
        requestsBtn.classList.remove('border-primary', 'text-primary');
        requestsBtn.classList.add('text-gray-500');
        tasksBtn.classList.remove('border-primary', 'text-primary');
        tasksBtn.classList.add('text-gray-500');

        // Show selected panel
        if (tabName === 'tasks') {
            tasksPanel.classList.remove('hidden');
            tasksBtn.classList.add('border-primary', 'text-primary');
            tasksBtn.classList.remove('text-gray-500');
        } else {
            requestsPanel.classList.remove('hidden');
            requestsBtn.classList.add('border-primary', 'text-primary');
            requestsBtn.classList.remove('text-gray-500');
        }

        // Store the dashboard tab preference in localStorage
        localStorage.setItem('dashboardSubTab', tabName);

        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('subtab', tabName);
        window.history.replaceState({}, '', url);
    };


    // ========================
    // NOTIFICATION FUNCTION
    // ========================
    function showNotification(message, type = 'success') {
        // Check if notification container exists, if not create it
        let notificationContainer = document.getElementById('notificationContainer');

        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notificationContainer';
            notificationContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(notificationContainer);
        }

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `transform transition-all duration-300 translate-x-0 opacity-100 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 ${type === 'success' ? 'bg-green-50 text-green-800 border-l-4 border-green-500' : 'bg-red-50 text-red-800 border-l-4 border-red-500'
            }`;

        notification.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check text-green-500' : 'fa-circle-exclamation text-red-500'}"></i>
            <span class="text-sm font-medium">${message}</span>
            <button class="ml-auto text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
                <i class="fa-solid fa-times"></i>
            </button>
        `;

        // Add to container
        notificationContainer.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // ========================
    // PAYSLIP DOWNLOAD FUNCTIONALITY
    // ========================

    // Handle payslip download as CSV
    document.addEventListener('click', function (e) {
        const downloadBtn = e.target.closest('.download-payslip');
        if (downloadBtn) {
            e.preventDefault();

            const payslipId = downloadBtn.dataset.id;
            const period = downloadBtn.dataset.period;
            const employeeName = downloadBtn.dataset.employee;

            if (!payslipId) return;

            // Show loading state
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Generating...';
            downloadBtn.classList.add('opacity-50', 'cursor-wait');

            // Redirect to download the CSV file
            window.location.href = `/generate-payslip?id=${payslipId}`;

            // Restore button state after a short delay
            setTimeout(() => {
                downloadBtn.innerHTML = originalText;
                downloadBtn.classList.remove('opacity-50', 'cursor-wait');
            }, 2000);
        }
    });

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM fully loaded and parsed'); // Debugging

        // ========================
        // SIDEBAR SETUP
        // ========================

        // Method 1: Attach click event to mobile menu button (by ID)
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenuBtn) {
            // Remove any existing onclick attribute to prevent double-firing
            mobileMenuBtn.removeAttribute('onclick');

            // Add click event listener
            mobileMenuBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Mobile menu button clicked'); // Debugging
                window.toggleSidebar();
            });
            console.log('Mobile menu button listener attached successfully');
        } else {
            console.warn('Mobile menu button not found, checking for alternative...');

            // Method 2: Try to find button by its children (if ID doesn't work)
            const possibleButtons = document.querySelectorAll('.lg\\:hidden button, .mobile-menu-btn, button[onclick*="toggleSidebar"]');
            possibleButtons.forEach(btn => {
                btn.removeAttribute('onclick');
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.toggleSidebar();
                });
            });

            if (possibleButtons.length > 0) {
                console.log('Found and attached to', possibleButtons.length, 'alternative button(s)');
            }
        }

        // Close sidebar when clicking on overlay
        const overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.addEventListener('click', function () {
                window.closeSidebar();
            });
        }

        // CLICK OUTSIDE TO HIDE SIDEBAR - Enhanced version
        // Close sidebar when clicking on main content
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.addEventListener('click', function (e) {
                // Only close if sidebar is open and we're on mobile
                const sidebar = document.getElementById('sidebar');
                if (window.innerWidth < 1024 && sidebar && sidebar.classList.contains('open')) {
                    // Don't close if clicking on the sidebar itself
                    if (!sidebar.contains(e.target)) {
                        window.closeSidebar();
                    }
                }
            });
        }

        // Close sidebar when clicking anywhere on the page except the sidebar and hamburger button
        document.addEventListener('click', function (e) {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');

            if (window.innerWidth < 1024 && sidebar && sidebar.classList.contains('open')) {
                // Check if click target is not the sidebar and not the hamburger button
                if (!sidebar.contains(e.target) && !mobileMenuBtn?.contains(e.target)) {
                    window.closeSidebar();
                }
            }
        });

        // Prevent sidebar from closing when clicking inside the sidebar
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.addEventListener('click', function (e) {
                e.stopPropagation(); // This prevents clicks inside sidebar from bubbling to document
            });
        }

        // Handle window resize - close sidebar on desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                window.closeSidebar();
            }
        });

        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        const modal = urlParams.get('modal');
        const subtab = urlParams.get('subtab') || localStorage.getItem('dashboardSubTab') || 'requests';

        console.log('URL params:', { tab, modal, subtab });

        // Setup dashboard tabs if we're on the dashboard
        if (tab === 'dashboard' || !tab) {
            const requestsBtn = document.getElementById('tabRequestsBtn');
            const tasksBtn = document.getElementById('tabTasksBtn');

            if (requestsBtn) {
                requestsBtn.removeAttribute('onclick');
                requestsBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.switchTab('requests');
                });
            }

            if (tasksBtn) {
                tasksBtn.removeAttribute('onclick');
                tasksBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.switchTab('tasks');
                });
            }

            // Restore the last used dashboard tab
            window.switchTab(subtab);
        }

        // Open modal if specified in URL
        if (modal) {
            console.log('Attempting to open modal:', modal);
            setTimeout(() => {
                window.openModal(modal);
            }, 200);
        }

        // ========================
        // MODAL TRIGGERS
        // ========================

        // Modal triggers - with null checks
        const openProfileModalBtn = document.getElementById('openProfileModalBtn');
        if (openProfileModalBtn) {
            openProfileModalBtn.addEventListener('click', () => window.openModal('profileModal'));
        }

        const openLeaveModalBtn = document.getElementById('openLeaveModalBtn');
        if (openLeaveModalBtn) {
            openLeaveModalBtn.addEventListener('click', () => window.openModal('leaveModal'));
        }

        const openAttendanceModalBtn = document.getElementById('openAttendanceModalBtn');
        if (openAttendanceModalBtn) {
            openAttendanceModalBtn.addEventListener('click', () => window.openModal('attendanceModal'));
        }

        const openPayslipModalBtn = document.getElementById('openPayslipModalBtn');
        if (openPayslipModalBtn) {
            openPayslipModalBtn.addEventListener('click', () => window.openModal('payslipModal'));
        }

        const openSettingsModalBtn = document.getElementById('openSettingsModalBtn');
        if (openSettingsModalBtn) {
            openSettingsModalBtn.addEventListener('click', () => window.openModal('settingsModal'));
        }

        // Setup modal close buttons
        document.querySelectorAll('.close-modal, [data-close-modal]').forEach(button => {
            button.addEventListener('click', function () {
                const modal = this.closest('[id$="Modal"]');
                if (modal) {
                    window.closeModal(modal.id);
                }
            });
        });

        // Setup modal backdrop clicks
        document.querySelectorAll('.modal-backdrop, [data-modal-backdrop], [id$="Modal"]').forEach(backdrop => {
            backdrop.addEventListener('click', function (e) {
                if (e.target === this) {
                    window.closeModal(this.id);
                }
            });
        });

        console.log('Event listeners setup complete'); // Debugging
    });

    // Also try to initialize on load for older browsers
    window.addEventListener('load', function () {
        console.log('Window fully loaded');
        // Double-check that mobile menu button has click handler
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenuBtn) {
            console.log('Mobile menu button exists on load');
        }
    });

})();