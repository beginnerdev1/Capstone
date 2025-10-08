<div class="modal fade" id="setPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="setPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="setPasswordModalLabel">Set Your Password</h5>
                    </div>
                    <div class="modal-body">
                        <form id="setPasswordForm">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6">
                            </div>
                            <div id="setPasswordMsg"></div>
                            <button type="submit" class="btn btn-primary w-100">Save Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>