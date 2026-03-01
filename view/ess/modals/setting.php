<!-- Settings Modal -->
<div id="settingsModal" class="fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden modal-enter z-50">
    <div class="bg-white rounded-md max-w-lg w-full mx-4 p-6 shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-sliders mr-2 text-primary"></i>settings
            </h3>
            <button class="close-modal text-gray-400 hover:text-gray-600" data-modal="settingsModal">
                <i class="fa-solid fa-circle-xmark fa-xl"></i>
            </button>
        </div>
        <p class="text-gray-500 text-sm mb-4">profile & preferences</p>
        <div class="mb-6 border-b pb-4">
            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-solid fa-user-pen text-primary"></i>update profile
            </h4>
            <div class="space-y-3">
                <input type="text" placeholder="Full name" value="Alex Chen" class="profile-input w-full">
                <input type="email" placeholder="Email" value="a.chen@company.com" class="profile-input w-full">
                <input type="tel" placeholder="Phone" value="+1 (555) 238-1192" class="profile-input w-full">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" placeholder="Department" value="Product Engineering" class="profile-input">
                    <input type="text" placeholder="Manager" value="Sarah V." class="profile-input">
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button
                class="close-modal bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300"
                data-modal="settingsModal">
                Cancel
            </button>
            <button id="saveSettingsBtn"
                class="bg-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-hover">
                <i class="fa-solid fa-floppy-disk mr-1"></i>save
            </button>
        </div>
    </div>
</div>