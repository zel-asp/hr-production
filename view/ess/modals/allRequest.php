<div id="viewAllRequestsModal"
    class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-lg w-full mx-4 p-6 shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-regular fa-list-alt mr-2 text-primary"></i>all requests
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="viewAllRequestsModal">
                <i class="fa-regular fa-circle-xmark fa-xl"></i>
            </button>
        </div>
        <p class="text-gray-500 text-sm mb-4">complete request history</p>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span
                        class="bg-[#dbeafe] text-primary-hover text-xs font-medium px-2.5 py-1 rounded-md">annual</span>
                    <div>
                        <p class="text-sm font-medium">Vacation · May 10–15</p>
                        <p class="text-xs text-gray-400">submitted 2d ago</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#f0e7fc] text-[#5940a0] text-xs font-medium px-2.5 py-1 rounded-md">sick</span>
                    <div>
                        <p class="text-sm font-medium">Apr 22 (1 day)</p>
                        <p class="text-xs text-gray-400">approved · Apr 23</p>
                    </div>
                </div>
                <span class="text-green-700 bg-green-50 px-3 py-1 text-xs font-medium rounded-md">approved</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-md">remote</span>
                    <div>
                        <p class="text-sm font-medium">WFH May 2 & 3</p>
                        <p class="text-xs text-gray-400">pending review</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span
                        class="bg-[#dbeafe] text-primary-hover text-xs font-medium px-2.5 py-1 rounded-md">annual</span>
                    <div>
                        <p class="text-sm font-medium">Vacation · Jun 5-7</p>
                        <p class="text-xs text-gray-400">submitted 1d ago</p>
                    </div>
                </div>
                <span class="text-amber-700 bg-amber-50 px-3 py-1 text-xs font-medium rounded-md">pending</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-[#f2f5f9] rounded-md">
                <div class="flex items-center gap-3">
                    <span class="bg-[#f0e7fc] text-[#5940a0] text-xs font-medium px-2.5 py-1 rounded-md">sick</span>
                    <div>
                        <p class="text-sm font-medium">Mar 15 (1 day)</p>
                        <p class="text-xs text-gray-400">approved · Mar 16</p>
                    </div>
                </div>
                <span class="text-green-700 bg-green-50 px-3 py-1 text-xs font-medium rounded-md">approved</span>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button
                class="close-modal bg-white text-primary px-4 py-2 rounded-md text-sm font-medium hover:bg-[#d9e2ed]"
                data-modal="viewAllRequestsModal">
                Close
            </button>
        </div>
    </div>
</div>