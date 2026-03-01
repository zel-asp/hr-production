<div class="tab-content" id="shift-content">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Shift & Schedule Management</h2>
            <p class="text-sm text-gray-500 mt-1">Create and manage employee work schedules</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModal('bulkScheduleModal')"
                class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-upload"></i>
                Bulk Upload
            </button>
            <button onclick="openModal('createScheduleModal')"
                class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                Create Schedule
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Total Employees</span>
                <span class="bg-[#e1eaf1] text-primary p-2 rounded-md">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-800 mt-2">48</p>
            <p class="text-xs text-gray-400 mt-1">32 scheduled this week</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Open Shifts</span>
                <span class="bg-amber-50 text-amber-600 p-2 rounded-md">
                    <i class="fa-solid fa-calendar-plus"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-800 mt-2">12</p>
            <p class="text-xs text-gray-400 mt-1">Needs coverage</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Swap Requests</span>
                <span class="bg-blue-50 text-blue-600 p-2 rounded-md">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-800 mt-2">5</p>
            <p class="text-xs text-gray-400 mt-1">3 pending approval</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Time Off</span>
                <span class="bg-green-50 text-green-600 p-2 rounded-md">
                    <i class="fa-solid fa-umbrella-beach"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-800 mt-2">8</p>
            <p class="text-xs text-gray-400 mt-1">Next 7 days</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter by:</span>
                <select class="border border-gray-200 rounded-md px-3 py-1.5 text-sm bg-white">
                    <option>All Departments</option>
                    <option>Management</option>
                    <option>Restaurant</option>
                    <option>Hotel</option>
                </select>
                <select class="border border-gray-200 rounded-md px-3 py-1.5 text-sm bg-white">
                    <option>All Shifts</option>
                    <option>Morning (7am-3pm)</option>
                    <option>Afternoon (3pm-11pm)</option>
                    <option>Graveyard (11pm-7am)</option>
                </select>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <div class="flex items-center gap-1 text-xs">
                    <span class="w-3 h-3 bg-blue-100 rounded"></span>
                    <span class="text-gray-500">Morning</span>
                </div>
                <div class="flex items-center gap-1 text-xs">
                    <span class="w-3 h-3 bg-amber-100 rounded"></span>
                    <span class="text-gray-500">Afternoon</span>
                </div>
                <div class="flex items-center gap-1 text-xs">
                    <span class="w-3 h-3 bg-purple-100 rounded"></span>
                    <span class="text-gray-500">Graveyard</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Week Navigation -->
    <div class="bg-white border border-gray-200 rounded-md p-4 shadow-sm mb-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <h3 class="text-base font-semibold text-gray-800">Weekly Schedule</h3>
                <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-medium">
                    March 10 - March 16, 2025
                </span>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center border border-gray-200 rounded-md overflow-hidden">
                    <button class="px-3 py-1.5 bg-white hover:bg-gray-50 text-sm border-r">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="px-4 py-1.5 bg-primary text-white text-sm font-medium">
                        Today
                    </button>
                    <button class="px-3 py-1.5 bg-white hover:bg-gray-50 text-sm border-l">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <div class="flex items-center gap-1 border-l pl-3">
                    <button class="p-1.5 bg-primary text-white rounded-md" title="Week view">
                        <i class="fa-solid fa-calendar-week"></i>
                    </button>
                    <button class="p-1.5 hover:bg-gray-100 rounded-md text-gray-400" title="Month view">
                        <i class="fa-solid fa-calendar-days"></i>
                    </button>
                    <button class="p-1.5 hover:bg-gray-100 rounded-md text-gray-400" title="List view">
                        <i class="fa-solid fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-3 mb-8">
        <?php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dates = ['10', '11', '12', '13', '14', '15', '16'];
        $month = 'Mar';

        foreach ($days as $index => $day):
            $isToday = ($index == 2);
            ?>
            <div class="bg-white border border-gray-200 rounded-md shadow-sm overflow-hidden">
                <div class="px-3 py-2 <?= $isToday ? 'bg-primary text-white' : 'bg-gray-50' ?> border-b">
                    <p class="text-xs font-medium"><?= $day ?></p>
                    <p class="text-lg font-semibold leading-tight"><?= $month ?>     <?= $dates[$index] ?></p>
                </div>
                <div class="p-2 min-h-70 space-y-2">
                    <!-- Morning Shift -->
                    <div onclick="openModal('viewShiftModal')"
                        class="p-2 bg-blue-50 rounded-md border-l-2 border-blue-500 text-xs hover:shadow-md transition cursor-pointer group relative">
                        <div class="flex justify-between items-start">
                            <span class="font-medium text-blue-700">Morning</span>
                            <span class="text-[10px] text-gray-400">7am-3pm</span>
                        </div>
                        <p class="text-gray-600 mt-1 truncate">Grace Lee, John Smith, M. Garcia</p>
                        <div class="absolute right-1 top-1 hidden group-hover:flex gap-1">
                            <button onclick="event.stopPropagation(); openModal('editShiftModal')"
                                class="w-5 h-5 bg-white rounded shadow-sm text-gray-400 hover:text-primary"><i
                                    class="fa-solid fa-pen text-[10px]"></i></button>
                        </div>
                    </div>

                    <!-- Afternoon Shift -->
                    <div onclick="openModal('viewShiftModal')"
                        class="p-2 bg-amber-50 rounded-md border-l-2 border-amber-500 text-xs hover:shadow-md transition cursor-pointer group relative">
                        <div class="flex justify-between items-start">
                            <span class="font-medium text-amber-700">Afternoon</span>
                            <span class="text-[10px] text-gray-400">3pm-11pm</span>
                        </div>
                        <p class="text-gray-600 mt-1 truncate">James Davis, Sarah Chen</p>
                        <div class="absolute right-1 top-1 hidden group-hover:flex gap-1">
                            <button onclick="event.stopPropagation(); openModal('editShiftModal')"
                                class="w-5 h-5 bg-white rounded shadow-sm text-gray-400 hover:text-primary"><i
                                    class="fa-solid fa-pen text-[10px]"></i></button>
                        </div>
                    </div>

                    <!-- Graveyard Shift -->
                    <div onclick="openModal('viewShiftModal')"
                        class="p-2 bg-purple-50 rounded-md border-l-2 border-purple-500 text-xs hover:shadow-md transition cursor-pointer group relative">
                        <div class="flex justify-between items-start">
                            <span class="font-medium text-purple-700">Graveyard</span>
                            <span class="text-[10px] text-gray-400">11pm-7am</span>
                        </div>
                        <p class="text-gray-600 mt-1 truncate">Mike Rivera</p>
                        <div class="absolute right-1 top-1 hidden group-hover:flex gap-1">
                            <button onclick="event.stopPropagation(); openModal('editShiftModal')"
                                class="w-5 h-5 bg-white rounded shadow-sm text-gray-400 hover:text-primary"><i
                                    class="fa-solid fa-pen text-[10px]"></i></button>
                        </div>
                    </div>

                    <!-- Add Shift Button -->
                    <button onclick="openModal('createScheduleModal')"
                        class="w-full p-1 border border-dashed border-gray-200 rounded-md text-gray-400 hover:text-primary hover:border-primary text-xs flex items-center justify-center gap-1 transition mt-2">
                        <i class="fa-solid fa-plus"></i> Add Shift
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Shift Swap Requests -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 bg-primary rounded-full"></span>
                    Shift Swap Requests
                </h3>
                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">3 pending</span>
            </div>

            <div class="space-y-3">
                <!-- Request 1 -->
                <div
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg group hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#f0e7fc] text-[#5940a0] flex items-center justify-center">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Maria Garcia → James Davis</p>
                            <p class="text-xs text-gray-500">Wed, Mar 20 • Morning to Afternoon</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="openModal('approveSwapModal')"
                                class="w-7 h-7 bg-green-100 text-green-600 rounded-md hover:bg-green-200 transition">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button class="w-7 h-7 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full">Pending</span>
                    </div>
                </div>

                <!-- Request 2 -->
                <div
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg group hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">John Smith → Lisa Wong</p>
                            <p class="text-xs text-gray-500">Thu, Mar 21 • Afternoon to Morning</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Approved</span>
                    </div>
                </div>
            </div>

            <button onclick="openModal('allSwapRequestsModal')"
                class="text-sm text-primary hover:underline mt-4 inline-flex items-center gap-1">
                View all requests <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>

    </div>
</div>