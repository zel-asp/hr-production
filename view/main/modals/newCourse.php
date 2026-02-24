<div id="newCourseModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Add New Course</h3>
            <button onclick="closeModal('newCourseModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Course Name</label>
                <input type="text" class="profile-input">
            </div>
            <div>
                <label class="block text-sm font-medium">Category</label>
                <select class="profile-input">
                    <option>Food Safety</option>
                    <option>Service Excellence</option>
                    <option>Leadership</option>
                    <option>Technical Skills</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Duration (hours)</label>
                <input type="number" class="profile-input">
            </div>
            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea class="profile-input" rows="3"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('newCourseModal')">Cancel</button>
                <button type="submit" class="btn-primary">Add Course</button>
            </div>
        </form>
    </div>
</div>