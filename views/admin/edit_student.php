<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold">
                            <i class="fa fa-edit"></i> Edit Student
                        </h4>
                        <hr>

                        <?php echo form_open(admin_url('klms/Lms_admin/edit_student/' . $student->id), ['id' => 'edit_student_form']); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname" class="control-label">
                                        <span class="text-danger">*</span> First Name
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="firstname" 
                                           id="firstname" 
                                           value="<?php echo html_escape($student->firstname); ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname" class="control-label">
                                        <span class="text-danger">*</span> Last Name
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="lastname" 
                                           id="lastname" 
                                           value="<?php echo html_escape($student->lastname); ?>" 
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="control-label">
                                        <span class="text-danger">*</span> Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           name="email" 
                                           id="email" 
                                           value="<?php echo html_escape($student->email); ?>" 
                                           required>
                                    <small class="text-muted">Used for login</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phonenumber" class="control-label">
                                        Phone Number
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="phonenumber" 
                                           id="phonenumber" 
                                           value="<?php echo html_escape($student->phonenumber); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="control-label">
                                        Title
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="title" 
                                           id="title" 
                                           value="<?php echo html_escape($student->title); ?>" 
                                           placeholder="Student">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="active" class="control-label">
                                        Status
                                    </label>
                                    <select name="active" id="active" class="form-control selectpicker">
                                        <option value="1" <?php echo $student->active == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo $student->active == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- <h5 class="bold">Change Password (Optional)</h5>
                        <p class="text-muted">Leave blank to keep current password</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="control-label">
                                        New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password" 
                                           id="password" 
                                           autocomplete="new-password">
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirm" class="control-label">
                                        Confirm Password
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password_confirm" 
                                           id="password_confirm" 
                                           autocomplete="new-password">
                                </div>
                            </div>
                        </div> -->

                        <!-- <hr> -->

                        <div class="form-group text-right">
                            <a href="<?php echo admin_url('klms/Lms_admin/view_student/' . $student->id); ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit_btn">
                                <i class="fa fa-save"></i> Update Student
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>

                <!-- Student Information Card -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="bold"><i class="fa fa-info-circle"></i> Student Information</h5>
                        <hr>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Student ID:</strong></td>
                                <td><?php echo $student->id; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Registered:</strong></td>
                                <td><?php echo date('F d, Y h:i A', strtotime($student->datecreated)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Last Login:</strong></td>
                                <td>
                                    <?php if (!empty($student->last_login)): ?>
                                        <?php echo time_ago($student->last_login); ?>
                                    <?php else: ?>
                                        Never
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email Verified:</strong></td>
                                <td>
                                    <?php if (!empty($student->email_verified_at)): ?>
                                        <span class="label label-success"><i class="fa fa-check"></i> Verified</span>
                                    <?php else: ?>
                                        <span class="label label-warning"><i class="fa fa-clock-o"></i> Not Verified</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    // Form validation
    $('#edit_student_form').on('submit', function(e) {
        var password = $('#password').val();
        var password_confirm = $('#password_confirm').val();
        
        // If password is entered, validate
        if (password !== '' || password_confirm !== '') {
            if (password !== password_confirm) {
                e.preventDefault();
                alert_float('danger', 'Passwords do not match');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert_float('danger', 'Password must be at least 6 characters');
                return false;
            }
        }
        
        // Show loading state
        $('#submit_btn').prop('disabled', true)
                       .html('<i class="fa fa-spinner fa-spin"></i> Updating...');
    });

    // Initialize selectpicker
    $('.selectpicker').selectpicker();
});
</script>

</body>
</html>