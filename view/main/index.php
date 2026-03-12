<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <title>Hotel & Restaurant HR </title>
        <link rel="stylesheet" href="/public/assets/css/output.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body>
        <?php require base_path('view/partials/message.php'); ?>


        <!-- Mobile Menu Toggle Button -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileSidebar()">
            <i class="fas fa-bars"></i>
        </button>


        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

        <div class="flex min-h-screen">
            <!-- Sidebar Tabs - MODIFIED: Added all new modules -->
            <div class="w-72 bg-white shadow-lg p-4 sidebar-fixed flex flex-col" id="mainSidebar">
                <div>
                    <div class="flex items-center mb-6">
                        <span class="title-accent"></span>
                        <h1 class="text-xl font-semibold text-gray-800">Hotel & Restaurant HR</h1>
                    </div>

                    <!-- Close button for mobile -->
                    <button class="absolute top-4 right-4 text-gray-500 md:hidden" onclick="closeMobileSidebar()">
                        <i class="fas fa-times text-xl"></i>
                    </button>

                    <!-- navigations -->
                    <div class="space-y-1" id="sideTabs">
                        <!-- RECRUITMENT & TALENT Section -->
                        <div class="sidebar-category">Human Resource 1</div>
                        <div class="side-tab active" data-tab="recruitment">
                            <i class="fas fa-bullhorn"></i>
                            Recruitment
                        </div>
                        <div class="side-tab" data-tab="applicant">
                            <i class="fas fa-users"></i>
                            Applicant
                        </div>
                        <div class="side-tab" data-tab="onboarding">
                            <i class="fas fa-rocket"></i>
                            New Hire Onboarding
                        </div>
                        <div class="side-tab" data-tab="performance">
                            <i class="fas fa-chart-line"></i>
                            Performance
                        </div>
                        <div class="side-tab" data-tab="recognition">
                            <i class="fas fa-star"></i>
                            Social Recognition
                        </div>

                        <!-- PERFORMANCE & DEVELOPMENT Section -->
                        <div class="sidebar-category">Human Resource 2</div>
                        <div class="side-tab" data-tab="competency">
                            <i class="fas fa-clipboard-check"></i>
                            Competency
                        </div>
                        <div class="side-tab" data-tab="learning">
                            <i class="fas fa-graduation-cap"></i>
                            Learning
                        </div>
                        <div class="side-tab" data-tab="training">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Training
                        </div>
                        <div class="side-tab" data-tab="succession">
                            <i class="fas fa-sitemap"></i>
                            Succession Planning
                        </div>

                        <!-- EMPLOYEE SERVICES Section -->
                        <div class="sidebar-category">Human Resource 3</div>
                        <div class="side-tab" data-tab="time">
                            <i class="fas fa-clock"></i>
                            Time and Attendance System
                        </div>
                        <div class="side-tab" data-tab="shift">
                            <i class="fas fa-calendar-alt"></i>
                            Shift and Schedule
                        </div>
                        <div class="side-tab" data-tab="timesheet">
                            <i class="fas fa-table"></i>
                            Timesheet
                        </div>
                        <div class="side-tab" data-tab="leave">
                            <i class="fas fa-umbrella-beach"></i>
                            Leave
                        </div>
                        <div class="side-tab" data-tab="claims">
                            <i class="fas fa-file-invoice"></i>
                            Claims and Reimbursement
                        </div>

                        <!-- CORE HR Section -->
                        <div class="sidebar-category">Human Resource 4</div>
                        <div class="side-tab" data-tab="hcm">
                            <i class="fas fa-database"></i>
                            Core Human Capital
                        </div>
                        <div class="side-tab" data-tab="payroll">
                            <i class="fas fa-calculator"></i>
                            Payroll
                        </div>
                        <div class="side-tab" data-tab="compensation">
                            <i class="fas fa-coins"></i>
                            Compensation Planning
                        </div>
                        <div class="side-tab" data-tab="hmo">
                            <i class="fas fa-notes-medical"></i>
                            HMO & Benefits Administration
                        </div>
                        <div class="side-tab" data-tab="analytics">
                            <i class="fas fa-chart-pie"></i>
                            HR Analytics Dashboard
                        </div>
                    </div>
                </div>

                <!-- Logout Button at Bottom -->
                <div class="mt-auto pt-4 border-t border-gray-200">
                    <form method="POST" action="/logout">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content Area  -->
            <div class="flex-1 p-8 main-content-offset" id="mainContent">
                <!--  RECRUITMENT   -->
                <?php require base_path('view/main/sections/recruitment.php'); ?>

                <!--  APPLICANT   -->
                <?php require base_path('view/main/sections/applicant.php'); ?>

                <!--  NEW HIRE ONBOARDING  -->
                <?php require base_path('view/main/sections/onboarding.php'); ?>

                <!--  PERFORMANCE   -->
                <?php require base_path('view/main/sections/performance.php'); ?>

                <?php require base_path('view/main/sections/recognition.php'); ?>

                <!--  COMPETENCY   -->
                <?php require base_path('view/main/sections/competency.php'); ?>

                <!--  LEARNING   -->
                <?php require base_path('view/main/sections/learning.php'); ?>

                <!--  TRAINING   -->
                <?php require base_path('view/main/sections/training.php'); ?>

                <!--  SUCCESSION PLANNING  -->
                <?php require base_path('view/main/sections/successionPlanning.php'); ?>

                <!--  TIME AND ATTENDANCE  -->
                <?php require base_path('view/main/sections/attendance.php'); ?>

                <!--  SHIFT AND SCHEDULE   -->
                <?php require base_path('view/main/sections/schedule.php'); ?>

                <!--  TIMESHEET   -->
                <?php require base_path('view/main/sections/timesheet.php'); ?>

                <!--  LEAVE   -->
                <?php require base_path('view/main/sections/leave.php'); ?>

                <!--  CLAIMS AND REIMBURSEMENT  -->
                <?php require base_path('view/main/sections/claims.php'); ?>

                <!--  CORE HUMAN CAPITAL   -->
                <?php require base_path('view/main/sections/coreHuman.php'); ?>

                <!--  PAYROLL   -->
                <?php require base_path('view/main/sections/payroll.php'); ?>

                <!--  COMPENSATION PLANNING  -->
                <?php require base_path('view/main/sections/compensation.php'); ?>

                <!--  HR ANALYTICS DASHBOARD  -->
                <?php require base_path('view/main/sections/analytics.php'); ?>

                <!--  HMO & BENEFITS ADMINISTRATION  -->
                <?php require base_path('view/main/sections/benefits.php'); ?>
            </div>
        </div>

        <!-- All Modals remain the same as before -->
        <?php require base_path('view/main/modals/newJob.php'); ?>

        <?php require base_path('view/main/modals/viewShift.php'); ?>

        <?php require base_path('view/main/modals/recentApplicant.php'); ?>

        <!-- Edit Job Modal  -->
        <?php require base_path('view/main/modals/editJob.php'); ?>


        <!-- Applicant Modal   -->
        <?php require base_path('view/main/modals/applicant.php'); ?>


        <!-- Resume Modal  -->
        <?php require base_path('view/main/modals/resume.php'); ?>


        <!-- Add Task Modal -->
        <?php require base_path('view/main/modals/addTask.php'); ?>

        <!-- Update Progress Modal  -->
        <?php require base_path('view/main/modals/updateProgress.php'); ?>

        <!-- Update Progress Modal  -->
        <?php require base_path('view/main/modals/newReview.php'); ?>

        <!-- Competency Modal  -->
        <?php require base_path('view/main/modals/newCompetency.php'); ?>

        <!-- Learning Modal  -->
        <?php require base_path('view/main/modals/newCourse.php'); ?>

        <!-- New Training Modal -->
        <?php require base_path('view/main/modals/training.php'); ?>

        <!-- Manual Time Modal -->
        <?php require base_path('view/main/modals/manualTime.php'); ?>

        <!-- Create Schedule Modal -->
        <?php require base_path('view/main/modals/createSchedule.php'); ?>

        <!-- Salary Review Modal -->
        <?php require base_path('view/main/modals/salaryReview.php'); ?>

        <!-- Enroll Benefit Modal -->
        <?php require base_path('view/main/modals/enrollBenefit.php'); ?>

        <!-- CORE HUMAN CAPITAL -->
        <?php require base_path('view/main/modals/viewEmployeeDetails.php'); ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="/public/assets/js/main.js"></script>
        <script src="/public/assets/js/analytics.js"></script>

    </body>

</html>