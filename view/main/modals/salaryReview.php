<!-- Salary Review Modal -->
<div id="salaryReviewModal" class="modal fixed inset-0 bg-gray-800/40 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl max-w-2xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">New Salary Review</h3>
            <button onclick="closeModal('salaryReviewModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <form action="/add-compensation" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="proposed_hourly_rate" value="<?= $review['proposed_hourly_rate'] ?>"
                    id="edit_proposed_hourly_rate_<?= $review['id'] ?>">
                <!-- Keep only ONE hidden field for the raw hourly rate value -->
                <input type="hidden" name="proposed_hourly_rate" value="" id="proposed_hourly_rate">
                <div>
                    <label class="block text-sm font-medium mb-1">Employee</label>
                    <select name="employee_id" id="comp_employee"
                        class="w-full p-2 border border-gray-300 rounded-lg text-sm" required
                        onchange="loadEmployeeSalary()">
                        <option value="">Select employee</option>
                        <?php foreach ($compensationEmployees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" data-hourly="
                            <?= $emp['hourly_rate'] ?>">
                                <?= htmlspecialchars($emp['full_name']) ?> -
                                <?= htmlspecialchars($emp['position']) ?>
                                (
                                <?= formatMonthly($emp['hourly_rate']) ?>/mo)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Review Date</label>
                    <input type="date" name="review_date" class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                        value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Current Monthly Salary</label>
                        <input type="text" id="current_salary_display"
                            class="w-full p-2 border border-gray-300 rounded-lg text-sm bg-gray-100" readonly>
                        <input type="hidden" name="current_salary" id="current_salary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Proposed Monthly Salary</label>
                        <input type="number" name="proposed_salary" id="proposed_salary"
                            class="w-full p-2 border border-gray-300 rounded-lg text-sm" step="0.01" min="0" required
                            oninput="calculateIncrease()">
                    </div>
                </div>

                <!-- New Hourly Rate Field - NO name attribute on display field -->
                <div>
                    <label class="block text-sm font-medium mb-1">Proposed Hourly Rate</label>
                    <div class="flex gap-2">
                        <input type="text" id="hourly_rate_display"
                            class="flex-1 p-2 border border-gray-300 rounded-lg text-sm bg-gray-100" readonly
                            placeholder="Hourly rate will be calculated">
                        <!-- Optional: Keep the edit button if you want manual editing -->
                        <button type="button" onclick="toggleHourlyEdit()"
                            class="px-3 py-2 bg-gray-200 rounded-lg text-sm hover:bg-gray-300"
                            title="Toggle manual edit">
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Calculated based on proposed salary (8 hours/day, 22
                        days/month)</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Increase Amount</label>
                        <input type="text" id="increase_amount"
                            class="w-full p-2 border border-gray-300 rounded-lg text-sm bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Increase Percentage</label>
                        <input type="text" id="increase_percentage"
                            class="w-full p-2 border border-gray-300 rounded-lg text-sm bg-gray-100" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Effective Date</label>
                    <input type="date" name="effective_date"
                        class="w-full p-2 border border-gray-300 rounded-lg text-sm" min="<?= date('Y-m-d') ?>"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Reason for Increase</label>
                    <select name="review_type" class="w-full p-2 border border-gray-300 rounded-lg text-sm" required>
                        <option value="">Select reason</option>
                        <option value="annual">Annual Review</option>
                        <option value="promotion">Promotion</option>
                        <option value="market">Market Adjustment</option>
                        <option value="merit">Merit Increase</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Comments</label>
                    <textarea name="finance_notes" class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                        rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg text-sm"
                        onclick="closeModal('salaryReviewModal')">Cancel</button>
                    <button type="submit" class="btn-primary" name="submitReview">Submit Review</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteReviewModal"
    class="modal fixed inset-0 bg-gray-900/20 flex items-center justify-center hidden modal-enter z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl max-w-md w-full mx-4 shadow-xl">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-medium text-gray-900">Delete Review</h3>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-2">Are you sure you want to delete this compensation review for <span
                    id="deleteEmployeeName" class="font-medium text-gray-900"></span>?</p>
            <p class="text-xs text-red-600">This action cannot be undone.</p>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <button type="button" onclick="closeModal('deleteReviewModal')"
                class="px-4 py-2 text-xs text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <form method="POST" action="/add-compensation" class="inline">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="review_id" id="deleteReviewId" value="">
                <button type="submit" name="delete" value="delete"
                    class="px-4 py-2 text-xs text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Delete Review
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(reviewId, employeeName) {
        document.getElementById('deleteReviewId').value = reviewId;
        document.getElementById('deleteEmployeeName').textContent = employeeName;
        openModal('deleteReviewModal');
    }
</script>