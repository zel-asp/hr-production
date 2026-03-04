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

                <div>
                    <label class="block text-sm font-medium mb-1">Employee</label>
                    <select name="employee_id" id="comp_employee"
                        class="w-full p-2 border border-gray-300 rounded-lg text-sm" required
                        onchange="loadEmployeeSalary()">
                        <option value="">Select employee</option>
                        <?php foreach ($compensationEmployees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" data-hourly="<?= $emp['hourly_rate'] ?>">
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
                    <button type="submit" class="btn-primary">Submit
                        Review</button>
                </div>
            </form>
        </div>
    </div>
</div>