<!-- Attendance Modal -->
<div id="attendanceModal" class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-md w-full mx-4 p-6 shadow-xl">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-regular fa-calendar-check mr-2 text-primary"></i>attendance
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="attendanceModal">
                <i class="fa-regular fa-circle-xmark fa-xl"></i>
            </button>
        </div>
        <p class="text-gray-500 text-sm mb-3">October summary</p>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#f2f5f9] p-4 rounded-md">
                <span class="text-sm text-gray-500">This week</span>
                <p class="text-xl font-semibold">42h 15m</p>
            </div>
            <div class="bg-[#f2f5f9] p-4 rounded-md">
                <span class="text-sm text-gray-500">Overtime</span>
                <p class="text-xl font-semibold">2h 40m</p>
            </div>
            <div class="bg-[#f2f5f9] p-4 rounded-md">
                <span class="text-sm text-gray-500">Absences</span>
                <p class="text-xl font-semibold">0 days</p>
            </div>
            <div class="bg-[#f2f5f9] p-4 rounded-md">
                <span class="text-sm text-gray-500">Late</span>
                <p class="text-xl font-semibold">1</p>
            </div>
        </div>
        <div class="mt-5 flex justify-end">
            <button
                class="close-modal bg-[#ecf3fa] text-primary px-4 py-2 rounded-md text-sm font-medium hover:bg-[#d9e2ed]"
                data-modal="attendanceModal">
                Close
            </button>
        </div>
    </div>
</div>