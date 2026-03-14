// attendance.js 

// Get config from global scope
const config = window.attendanceConfig || {};
let currentAttendanceId = config.currentAttendanceId;
let currentStatus = config.currentStatus || 'clocked_out';
let pauseTotal = config.pauseTotal || 0;
let elapsedSeconds = config.elapsedSeconds || 0;

// DOM Elements
let timerInterval = null;
let hoursEl, minutesEl, secondsEl, overtimeIndicator, overtimeHoursEl;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Get DOM elements
    hoursEl = document.getElementById('hours');
    minutesEl = document.getElementById('minutes');
    secondsEl = document.getElementById('seconds');
    overtimeIndicator = document.getElementById('overtimeIndicator');
    overtimeHoursEl = document.getElementById('overtimeHours');

    console.log('Timer initialized with status:', currentStatus, 'seconds:', elapsedSeconds);

    // Ensure elapsedSeconds is a valid number and not negative
    elapsedSeconds = Math.max(0, parseInt(elapsedSeconds) || 0);

    // Update display immediately with the current elapsed seconds
    updateTimerDisplay(elapsedSeconds);

    // Start timer if clocked in
    if (currentStatus === 'clocked_in') {
        console.log('Starting timer from', elapsedSeconds, 'seconds');
        startTimer(elapsedSeconds);
    } else if (currentStatus === 'paused') {
        console.log('Paused state, displaying', elapsedSeconds, 'seconds');
        updateTimerDisplay(elapsedSeconds);
    }

    // Set shift start time if available
    if (config.shiftStartTime) {
        // Parse the date string and ensure it's treated as local time
        const shiftStart = new Date(config.shiftStartTime + ' UTC+8');
        const shiftStartEl = document.getElementById('shiftStartTime');
        if (shiftStartEl) {
            shiftStartEl.textContent = 'Started at ' + shiftStart.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }
    }

    // Disable clock in button if no schedule or day off
    disableClockInIfNoSchedule();
});

// NEW: Function to disable clock in button if no schedule or day off
function disableClockInIfNoSchedule() {
    const clockInBtn = document.getElementById('clockInBtn');
    if (!clockInBtn) return;

    if (config.hasSchedule === false || config.isDayOff === true) {
        clockInBtn.disabled = true;
        clockInBtn.classList.add('opacity-50', 'cursor-not-allowed');
        clockInBtn.title = config.hasSchedule === false
            ? 'You have no assigned schedule. Please contact administrator.'
            : 'Today is your day off. Contact administrator if you need to work today.';
    }
}

// NEW: Function to check if employee has schedule before clocking in
function checkScheduleBeforeClockIn() {
    // Check if employee has a schedule
    if (config.hasSchedule === false) {
        showNotification('You cannot clock in because you have no assigned schedule. Please contact your administrator.', 'error');
        return false;
    }

    // Also check if it's a day off (no shift for today)
    if (config.isDayOff === true) {
        showNotification('You cannot clock in today as it is your day off. Please contact your administrator if you need to work today.', 'error');
        return false;
    }

    return true;
}

// Override the handleAttendance to check schedule first
const originalHandleAttendance = window.handleAttendance;
window.handleAttendance = function (action) {
    // Only check for clock_in action
    if (action === 'clock_in') {
        if (!checkScheduleBeforeClockIn()) {
            return; // Stop if schedule check fails
        }
    }

    // Call the original function
    if (originalHandleAttendance) {
        originalHandleAttendance(action);
    }
};

function handleAttendance(action) {
    const csrfToken = config.csrfToken;

    // Show loading state
    const buttons = document.querySelectorAll('#attendanceButtons button');
    buttons.forEach(btn => btn.disabled = true);

    console.log('Sending request:', { action, attendance_id: currentAttendanceId });

    fetch('/attendance/handle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            attendance_id: currentAttendanceId,
            csrf_token: csrfToken
        })
    })
        .then(async response => {
            const text = await response.text();
            console.log('Raw response:', text);

            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', text.substring(0, 200));
                throw new Error('Server returned invalid JSON response');
            }
        })
        .then(data => {
            if (data.success) {
                // Update all tracking variables
                currentAttendanceId = data.attendance_id;
                currentStatus = data.status;
                pauseTotal = data.pause_total || 0;

                // Also update the config object for future reference
                config.currentAttendanceId = data.attendance_id;
                config.currentStatus = data.status;
                config.pauseTotal = data.pause_total || 0;

                // Update UI with the elapsed seconds from server
                updateUIForStatus(data.status, data.elapsed_seconds || 0);

                showNotification(data.message, 'success');

                // If clocked in, update shift start time
                if (action === 'clock_in') {
                    const now = new Date();
                    const shiftStartEl = document.getElementById('shiftStartTime');
                    if (shiftStartEl) {
                        shiftStartEl.textContent = 'Started at ' + now.toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                    }
                }
            } else {
                // If server says we're already clocked in, update our local state
                if (data.attendance_id && data.status) {
                    console.log('Updating local state from server response:', data);
                    currentAttendanceId = data.attendance_id;
                    currentStatus = data.status;
                    config.currentAttendanceId = data.attendance_id;
                    config.currentStatus = data.status;

                    // Force a page refresh to get correct elapsed time
                    showNotification('Refreshing to sync attendance state...', 'info');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred', 'error');
        })
        .finally(() => {
            buttons.forEach(btn => btn.disabled = false);
        });
}

function updateUIForStatus(status, elapsedSeconds) {
    // Hide all buttons first
    const clockInBtn = document.getElementById('clockInBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const resumeBtn = document.getElementById('resumeBtn');
    const clockOutBtn = document.getElementById('clockOutBtn');
    const statusEl = document.getElementById('shiftStatus');
    const shiftInfo = document.getElementById('shiftInfo');

    if (!clockInBtn || !pauseBtn || !resumeBtn || !clockOutBtn || !statusEl || !shiftInfo) {
        console.error('Required DOM elements not found');
        return;
    }

    // Hide all buttons
    clockInBtn.classList.add('hidden');
    pauseBtn.classList.add('hidden');
    resumeBtn.classList.add('hidden');
    clockOutBtn.classList.add('hidden');

    // Show relevant buttons
    if (status === 'clocked_out') {
        clockInBtn.classList.remove('hidden');
        // Reapply schedule check for clock in button
        disableClockInIfNoSchedule();
        updateTimerDisplay(0);
        statusEl.textContent = 'READY TO CLOCK IN';
        if (overtimeIndicator) overtimeIndicator.classList.add('hidden');
        shiftInfo.classList.add('hidden');
        stopTimer();
    } else {
        clockOutBtn.classList.remove('hidden');
        shiftInfo.classList.remove('hidden');

        if (status === 'clocked_in') {
            pauseBtn.classList.remove('hidden');
            statusEl.textContent = 'CURRENT SHIFT';
            startTimer(elapsedSeconds);
        } else if (status === 'paused') {
            resumeBtn.classList.remove('hidden');
            statusEl.textContent = 'PAUSED';
            stopTimer();
            updateTimerDisplay(elapsedSeconds);
        }
    }
}

function startTimer(initialSeconds = 0) {
    // Stop any existing timer
    stopTimer();

    // Ensure initial seconds is a number and not negative
    let seconds = Math.max(0, parseInt(initialSeconds) || 0);

    // Update display immediately
    updateTimerDisplay(seconds);

    console.log('Timer started at:', seconds, 'seconds');

    // Set up interval for real-time updates
    timerInterval = setInterval(() => {
        seconds++;
        updateTimerDisplay(seconds);
    }, 1000);
}

function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
        console.log('Timer stopped');
    }
}

function updateTimerDisplay(seconds) {
    // Ensure DOM elements exist
    if (!hoursEl || !minutesEl || !secondsEl) {
        hoursEl = document.getElementById('hours');
        minutesEl = document.getElementById('minutes');
        secondsEl = document.getElementById('seconds');
        overtimeIndicator = document.getElementById('overtimeIndicator');
        overtimeHoursEl = document.getElementById('overtimeHours');

        // If still not found, exit
        if (!hoursEl || !minutesEl || !secondsEl) return;
    }

    // Ensure seconds is a number
    seconds = parseInt(seconds) || 0;

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    // Update display with padding
    hoursEl.textContent = String(hours).padStart(2, '0');
    minutesEl.textContent = String(minutes).padStart(2, '0');
    secondsEl.textContent = String(secs).padStart(2, '0');

    // Check for overtime (after 8 hours = 28800 seconds)
    if (seconds >= 28800 && overtimeIndicator && overtimeHoursEl) {
        const overtime = Math.floor((seconds - 28800) / 3600);
        overtimeIndicator.classList.remove('hidden');
        overtimeHoursEl.textContent = overtime;
    } else if (overtimeIndicator) {
        overtimeIndicator.classList.add('hidden');
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-sm ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        } z-50 animate-fade-in shadow-lg`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add CSS for notifications dynamically
if (!document.getElementById('attendance-notification-styles')) {
    const style = document.createElement('style');
    style.id = 'attendance-notification-styles';
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
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    `;
    document.head.appendChild(style);
}

// Make functions globally available
window.handleAttendance = handleAttendance;