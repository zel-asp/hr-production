document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.side-tab');
    const contents = document.querySelectorAll('.tab-content');


    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab');
    const activeModal = params.get('modal');

    if (activeTab) {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));

        const selectedTab = document.querySelector(`[data-tab="${activeTab}"]`);
        const selectedContent = document.getElementById(activeTab + '-content');

        if (selectedTab && selectedContent) {
            selectedTab.classList.add('active');
            selectedContent.classList.add('active');
        }
    }

    if (activeModal) {
        openModal(activeModal);
    }

    if (activeTab || activeModal) {
        history.replaceState({}, document.title, "/main");
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            contents.forEach(content => content.classList.remove('active'));

            const tabName = this.getAttribute('data-tab');
            const targetContent = document.getElementById(tabName + '-content');

            if (targetContent) {
                targetContent.classList.add('active');
            }

            closeMobileSidebar();
        });
    });
    checkMobileView();
    window.addEventListener('resize', checkMobileView);
});

// Mobile sidebar functions
function toggleMobileSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.toggle('mobile-open'); set

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

// Check mobile view and adjust
function checkMobileView() {
    const mainContent = document.getElementById('mainContent');

    if (window.innerWidth <= 768) {
        closeMobileSidebar();
    } else {
        const sidebar = document.getElementById('mainSidebar');
        sidebar.classList.remove('mobile-open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Modal functions
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

// Close modal when clicking outside
window.addEventListener('click', function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

function openEditJobModal(id, position, location, shift, salary) {

    document.getElementById('editJobId').value = id;
    document.getElementById('editJobTitle').textContent = 'Edit Job: ' + position;
    document.getElementById('editJobPosition').value = position;
    document.getElementById('editJobLocation').value = location;
    document.getElementById('editJobShift').value = shift;
    document.getElementById('editJobSalary').value = salary;

    openModal('editJobModal');
}