function switchToHR() {
    const container = document.getElementById('splitContainer');
    container.classList.remove('employee-mode');
    container.classList.add('hr-mode');

    // Update mobile buttons
    document.getElementById('mobileEmployeeBtn').classList.remove('active');
    document.getElementById('mobileHRBtn').classList.add('active');

    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('mode', 'hr');
    history.replaceState({}, '', url);
}

function switchToEmployee() {
    const container = document.getElementById('splitContainer');
    container.classList.remove('hr-mode');
    container.classList.add('employee-mode');

    // Update mobile buttons
    document.getElementById('mobileHRBtn').classList.remove('active');
    document.getElementById('mobileEmployeeBtn').classList.add('active');

    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('mode', 'employee');
    history.replaceState({}, '', url);
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const mode = urlParams.get('mode');

    if (mode === 'hr') {
        switchToHR();
    } else {
        switchToEmployee();
    }
});