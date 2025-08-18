
<div class="container my-4">
    <h2 class="mb-4">Report a Problem</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('users/report') ?>" method="post">
                <div class="mb-3">
                    <label for="issueType" class="form-label">Issue Type</label>
                    <select class="form-select" id="issueType" name="issueType" required>
                        <option value="">Select an issue</option>
                        <option value="No Water">No Water</option>
                        <option value="Low Pressure">Low Pressure</option>
                        <option value="Leakage">Leakage</option>
                        <option value="Billing Concern">Billing Concern</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Describe the problem..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact Number (optional)</label>
                    <input type="text" class="form-control" id="contact" name="contact" placeholder="09xx-xxx-xxxx">
                </div>
                <button type="submit" class="btn btn-primary">Submit Report</button>
            </form>
        </div>
    </div>
</div>