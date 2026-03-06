<?php if (!empty($_SESSION['success']) || !empty($_SESSION['error'])): ?>
    <div class="fixed top-6 right-6 z-50 space-y-3 max-w-sm w-full pointer-events-none" role="alert" aria-live="polite"
        id="notification-container">

        <!-- Success Messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <?php foreach ($_SESSION['success'] as $index => $msg): ?>
                <div class="relative pointer-events-auto group animate-slide-in-right" role="alert"
                    aria-label="Success notification" data-notification="success-<?= $index ?>">
                    <!-- Main Notification Card -->
                    <div
                        class="relative overflow-hidden bg-white rounded-2xl shadow-lg border-l-4 border-emerald-500 transform transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                        <!-- linear Accent -->
                        <div class="absolute inset-0 bg-linear-to-r from-emerald-50/50 to-transparent"></div>

                        <!-- Content Container -->
                        <div class="relative p-4">
                            <div class="flex items-start gap-3">
                                <!-- Animated Success Icon -->
                                <div class="shrink-0 relative">
                                    <div class="absolute inset-0 bg-emerald-100 rounded-xl animate-ping opacity-20"></div>
                                    <div
                                        class="relative w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-check-circle text-white text-xl"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Success</span>
                                        <span class="w-1 h-1 bg-emerald-300 rounded-full"></span>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            <span>Just now</span>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-800 leading-relaxed">
                                        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>

                                <!-- Close button with tooltip -->
                                <div class="relative group/btn">
                                    <button type="button" onclick="this.closest('[data-notification]').remove()"
                                        class="relative w-8 h-8 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                                        aria-label="Dismiss notification">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <!-- Tooltip -->
                                    <div
                                        class="absolute -top-8 right-0 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-200 pointer-events-none">
                                        <div class="bg-gray-800 text-white text-xs py-1 px-2 rounded whitespace-nowrap">
                                            Dismiss
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="absolute bottom-0 left-0 h-1 bg-emerald-100 w-full">
                            <div class="h-full bg-emerald-500 rounded-r-full animate-shrink-width"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($_SESSION['error'])): ?>
            <?php foreach ($_SESSION['error'] as $index => $msg): ?>
                <div class="relative pointer-events-auto group animate-slide-in-right" role="alert" aria-label="Error notification"
                    data-notification="error-<?= $index ?>">
                    <!-- Main Notification Card -->
                    <div
                        class="relative overflow-hidden bg-white rounded-2xl shadow-lg border-l-4 border-rose-500 transform transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                        <!-- linear Accent -->
                        <div class="absolute inset-0 bg-linear-to-r from-rose-50/50 to-transparent"></div>

                        <!-- Content Container -->
                        <div class="relative p-4">
                            <div class="flex items-start gap-3">
                                <!-- Animated Error Icon -->
                                <div class="shrink-0 relative">
                                    <div class="absolute inset-0 bg-rose-100 rounded-xl animate-ping opacity-20"></div>
                                    <div
                                        class="relative w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-exclamation-circle text-white text-xl"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Error</span>
                                        <span class="w-1 h-1 bg-rose-300 rounded-full"></span>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            <span>Just now</span>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-800 leading-relaxed">
                                        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>

                                <!-- Close button with tooltip -->
                                <div class="relative group/btn">
                                    <button type="button" onclick="this.closest('[data-notification]').remove()"
                                        class="relative w-8 h-8 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-rose-300"
                                        aria-label="Dismiss notification">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <!-- Tooltip -->
                                    <div
                                        class="absolute -top-8 right-0 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-200 pointer-events-none">
                                        <div class="bg-gray-800 text-white text-xs py-1 px-2 rounded whitespace-nowrap">
                                            Dismiss
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="absolute bottom-0 left-0 h-1 bg-rose-100 w-full">
                            <div class="h-full bg-rose-500 rounded-r-full animate-shrink-width"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <style>
        /* Slide in from right animation */
        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(30px) translateY(-10px);
            }

            70% {
                transform: translateX(-5px) translateY(0);
            }

            100% {
                opacity: 1;
                transform: translateX(0) translateY(0);
            }
        }

        .animate-slide-in-right {
            animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Progress bar shrink animation */
        @keyframes shrinkWidth {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .animate-shrink-width {
            animation: shrinkWidth 5s linear forwards;
        }

        /* Pause animation on hover */
        .group:hover .animate-shrink-width {
            animation-play-state: paused;
        }

        /* Subtle shake animation for errors (optional) */
        .group[aria-label="Error notification"]:hover {
            animation: subtleShake 0.3s ease-in-out;
        }

        @keyframes subtleShake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-2px);
            }

            75% {
                transform: translateX(2px);
            }
        }

        /* Focus styles for accessibility */
        .group button:focus-visible {
            outline: 2px solid currentColor;
            outline-offset: 2px;
        }

        /* Smooth transitions */
        .group {
            will-change: transform;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            #notification-container {
                left: 1rem;
                right: 1rem;
                max-width: none;
            }

            .group .text-sm {
                font-size: 0.875rem;
            }
        }
    </style>

    <script>
        (function () {
            // Auto-dismiss notifications after 5 seconds
            document.addEventListener('DOMContentLoaded', function () {
                const notifications = document.querySelectorAll('[data-notification]');

                notifications.forEach(function (notification) {
                    // Add fade out animation
                    const style = document.createElement('style');
                    style.textContent = `
                        @keyframes fadeOut {
                            0% {
                                opacity: 1;
                                transform: scale(1);
                            }
                            100% {
                                opacity: 0;
                                transform: scale(0.9) translateY(-10px);
                            }
                        }
                    `;
                    document.head.appendChild(style);

                    // Set timeout to dismiss
                    const timeoutId = setTimeout(function () {
                        if (notification && notification.parentNode) {
                            notification.style.animation = 'fadeOut 0.3s ease forwards';
                            setTimeout(function () {
                                if (notification && notification.parentNode) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 5000);

                    // Clear timeout if notification is manually dismissed
                    const closeButton = notification.querySelector('button');
                    if (closeButton) {
                        closeButton.addEventListener('click', function () {
                            clearTimeout(timeoutId);
                        });
                    }

                    // Pause timeout on hover
                    notification.addEventListener('mouseenter', function () {
                        clearTimeout(timeoutId);
                    });

                    notification.addEventListener('mouseleave', function () {
                        // Restart timeout on mouse leave
                        const newTimeoutId = setTimeout(function () {
                            if (notification && notification.parentNode) {
                                notification.style.animation = 'fadeOut 0.3s ease forwards';
                                setTimeout(function () {
                                    if (notification && notification.parentNode) {
                                        notification.remove();
                                    }
                                }, 300);
                            }
                        }, 5000);

                        // Store new timeout
                        notification.dataset.timeoutId = newTimeoutId;
                    });
                });
            });

            // Add keyboard support (ESC to close latest notification)
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const notifications = document.querySelectorAll('[data-notification]');
                    const lastNotification = notifications[notifications.length - 1];
                    if (lastNotification) {
                        lastNotification.remove();
                    }
                }
            });
        })();
    </script>
<?php endif; ?>