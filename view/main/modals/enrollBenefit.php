<div id="enrollBenefitModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Enroll Employee in Benefits</h3>
            <button onclick="closeModal('enrollBenefitModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4" method="POST" action="/benefits/enroll">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label class="block text-sm font-medium mb-1">Employee</label>
                <select class="profile-input w-full p-2 border rounded" name="employee_id" required>
                    <option value="">Select employee</option>
                    <?php if (!empty($employeesForBenefits)): ?>
                        <?php foreach ($employeesForBenefits as $employee): ?>
                            <option value="<?= htmlspecialchars($employee['id']) ?>">
                                <?= htmlspecialchars($employee['full_name']) ?>
                                <?php if (!empty($employee['employee_number'])): ?>
                                    (<?= htmlspecialchars($employee['employee_number']) ?>)
                                <?php endif; ?>
                                - <?= htmlspecialchars($employee['position'] ?? 'No position') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Benefit Type</label>
                <select class="profile-input w-full p-2 border rounded" name="benefit_type" required>
                    <option value="">Select benefit type</option>
                    <option value="HMO - Principal">HMO - Principal</option>
                    <option value="HMO - Principal + 1 Dependent">HMO - Principal + 1 Dependent</option>
                    <option value="HMO - Principal + 2 Dependents">HMO - Principal + 2 Dependents</option>
                    <option value="Dental Insurance">Dental Insurance</option>
                    <option value="Life Insurance">Life Insurance</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Provider</label>
                <select class="profile-input w-full p-2 border rounded" name="provider_id" required>
                    <option value="">Select provider</option>
                    <?php if (!empty($benefitProviders)): ?>
                        <?php foreach ($benefitProviders as $provider): ?>
                            <option value="<?= htmlspecialchars($provider['id']) ?>">
                                <?= htmlspecialchars($provider['provider_name']) ?>
                                <?php if (!empty($provider['contact_info'])): ?>
                                    (Contact: <?= htmlspecialchars($provider['contact_info']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No providers available</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Effective Date</label>
                    <input type="date" class="profile-input w-full p-2 border rounded" name="effective_date"
                        min="<?= date('Y-m-d') ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Expiry Date</label>
                    <input type="date" class="profile-input w-full p-2 border rounded" min="<?= date('Y-m-d') ?>"
                        name="expiry_date">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Coverage Amount</label>
                <input type="text" class="profile-input w-full p-2 border rounded" name="coverage_amount"
                    placeholder="e.g., 100000">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Monthly Premium</label>
                <input type="text" class="profile-input w-full p-2 border rounded" name="monthly_premium"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Dependents (if applicable)</label>
                <textarea class="profile-input w-full p-2 border rounded" name="dependents" rows="2"
                    placeholder="List dependents..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg"
                    onclick="closeModal('enrollBenefitModal')">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Enroll Employee</button>
            </div>
        </form>
    </div>
</div>