<!-- View All Tasks Modal -->
<div id="viewAllTasksModal"
    class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-lg w-full mx-4 p-6 shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-regular fa-list-check mr-2 text-primary"></i>all tasks
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="viewAllTasksModal">
                <i class="fa-regular fa-circle-xmark fa-xl"></i>
            </button>
        </div>
        <p class="text-gray-500 text-sm mb-4">complete task list</p>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#e1eaf1] text-primary text-xs font-medium px-2.5 py-1 rounded-md">
                        <i class="fa-regular fa-circle-check mr-1"></i>todo
                    </span>
                    <div>
                        <p class="text-sm font-medium">Complete benefits enrollment</p>
                        <p class="text-xs text-gray-400">due May 1 · high</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#f0e7fc] text-[#5940a0] text-xs font-medium px-2.5 py-1 rounded-md">
                        <i class="fa-regular fa-clock mr-1"></i>review
                    </span>
                    <div>
                        <p class="text-sm font-medium">Q2 goal setting (self-assessment)</p>
                        <p class="text-xs text-gray-400">due May 10 · medium</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">in
                    progress</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#dbeafe] text-primary-hover text-xs font-medium px-2.5 py-1 rounded-md">
                        <i class="fa-regular fa-file-lines mr-1"></i>training
                    </span>
                    <div>
                        <p class="text-sm font-medium">Security awareness training</p>
                        <p class="text-xs text-gray-400">due May 5 · mandatory</p>
                    </div>
                </div>
                <span class="text-green-700 bg-green-50 px-3 py-1 text-xs font-medium rounded-md">completed</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-md">
                        <i class="fa-regular fa-calendar mr-1"></i>hr
                    </span>
                    <div>
                        <p class="text-sm font-medium">Update emergency contact</p>
                        <p class="text-xs text-gray-400">due Apr 30 · low</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#e1eaf1] text-primary text-xs font-medium px-2.5 py-1 rounded-md">
                        <i class="fa-regular fa-circle-check mr-1"></i>todo
                    </span>
                    <div>
                        <p class="text-sm font-medium">Review Q1 performance feedback</p>
                        <p class="text-xs text-gray-400">due May 15 · medium</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#f0e7fc] text-[#5940a0] text-xs font-medium px-2.5 py-1 rounded-md">
                        <i class="fa-regular fa-clock mr-1"></i>review
                    </span>
                    <div>
                        <p class="text-sm font-medium">Complete compliance training</p>
                        <p class="text-xs text-gray-400">due May 20 · high</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">in
                    progress</span>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button
                class="close-modal bg-white text-primary px-4 py-2 rounded-md text-sm font-medium hover:bg-[#d9e2ed]"
                data-modal="viewAllTasksModal">
                Close
            </button>
        </div>
    </div>
</div>