<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">

<?php echo form_open(admin_url('lms/Lms_admin/enrollment/' . (isset($enrollment) ? $enrollment->id : '')), ['id' => 'enrollment-form']); ?>

<div class="row">
    <div class="col-md-6">
        <!-- Student Selection -->
        <div class="form-group" app-field-wrapper="student_id">
            <label for="student_id" class="control-label">
                <span class="text-danger">*</span> <?php echo _l('Student'); ?>
            </label>
            <select name="student_id" id="student_id" class="selectpicker form-control" 
                    data-width="100%" 
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                    data-live-search="true" 
                    required>
                <option value="">-- <?php echo _l('Select Student'); ?> --</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>" 
                        <?php echo (isset($enrollment) && $enrollment->student_id == $student['id']) ? 'selected' : ''; ?>>
                        <?php echo $student['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Course Selection -->
        <div class="form-group" app-field-wrapper="course_id">
            <label for="course_id" class="control-label">
                <span class="text-danger">*</span> <?php echo _l('Course'); ?>
            </label>
            <select name="course_id" id="course_id" class="selectpicker form-control" 
                    data-width="100%" 
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                    data-live-search="true" 
                    required>
                <option value="">-- <?php echo _l('Select Course'); ?> --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>" 
                        data-price="<?php echo $course['price']; ?>"
                        <?php echo (isset($enrollment) && $enrollment->course_id == $course['id']) ? 'selected' : ''; ?>>
                        <?php echo $course['title']; ?> - ₹<?php echo number_format($course['price'], 2); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Enrolled Date -->
        <div class="form-group" app-field-wrapper="enrolled_date">
            <label for="enrolled_date" class="control-label">
                <span class="text-danger">*</span> <?php echo _l('Enrolled Date'); ?>
            </label>
            <div class="input-group date">
                <input type="text" 
                       id="enrolled_date" 
                       name="enrolled_date" 
                       class="form-control datetimepicker" 
                       value="<?php echo isset($enrollment) ? _dt($enrollment->enrolled_date) : _dt(date('Y-m-d H:i:s')); ?>"
                       required
                       autocomplete="off">
                <div class="input-group-addon">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Expiry Date -->
        <div class="form-group" app-field-wrapper="expiry_date">
            <label for="expiry_date" class="control-label">
                <?php echo _l('Expiry Date'); ?>
            </label>
            <div class="input-group date">
                <input type="text" 
                       id="expiry_date" 
                       name="expiry_date" 
                       class="form-control datepicker" 
                       value="<?php echo isset($enrollment) && !empty($enrollment->expiry_date) ? _d($enrollment->expiry_date) : ''; ?>"
                       autocomplete="off">
                <div class="input-group-addon">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>
            <small class="text-muted"><?php echo _l('Leave blank for no expiry'); ?></small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Payment Status -->
        <div class="form-group" app-field-wrapper="payment_status">
            <label for="payment_status" class="control-label">
                <span class="text-danger">*</span> <?php echo _l('Payment Status'); ?>
            </label>
            <select name="payment_status" id="payment_status" class="selectpicker form-control" 
                    data-width="100%" required>
                <option value="paid" <?php echo (isset($enrollment) && $enrollment->payment_status == 'paid') ? 'selected' : ''; ?>>
                    <?php echo _l('Paid'); ?>
                </option>
                <option value="unpaid" <?php echo (isset($enrollment) && $enrollment->payment_status == 'unpaid') ? 'selected' : ''; ?>>
                    <?php echo _l('Unpaid'); ?>
                </option>
                <option value="pending" <?php echo (isset($enrollment) && $enrollment->payment_status == 'pending') ? 'selected' : ''; ?>>
                    <?php echo _l('Pending'); ?>
                </option>
            </select>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Payment Reference -->
        <div class="form-group" app-field-wrapper="payment_reference">
            <label for="payment_reference" class="control-label">
                <?php echo _l('Payment Reference'); ?>
            </label>
            <input type="text" 
                   name="payment_reference" 
                   id="payment_reference" 
                   class="form-control" 
                   placeholder="<?php echo _l('Transaction ID / Reference Number'); ?>"
                   value="<?php echo isset($enrollment) ? $enrollment->payment_reference : ''; ?>">
            <small class="text-muted"><?php echo _l('Optional: Enter payment transaction ID'); ?></small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Access Status -->
        <div class="form-group" app-field-wrapper="access_status">
            <label for="access_status" class="control-label">
                <span class="text-danger">*</span> <?php echo _l('Access Status'); ?>
            </label>
            <select name="access_status" id="access_status" class="selectpicker form-control" 
                    data-width="100%" required>
                <option value="active" <?php echo (isset($enrollment) && $enrollment->access_status == 'active') ? 'selected' : ''; ?>>
                    <?php echo _l('Active'); ?>
                </option>
                <option value="inactive" <?php echo (isset($enrollment) && $enrollment->access_status == 'inactive') ? 'selected' : ''; ?>>
                    <?php echo _l('Inactive'); ?>
                </option>
                <option value="expired" <?php echo (isset($enrollment) && $enrollment->access_status == 'expired') ? 'selected' : ''; ?>>
                    <?php echo _l('Expired'); ?>
                </option>
                <option value="suspended" <?php echo (isset($enrollment) && $enrollment->access_status == 'suspended') ? 'selected' : ''; ?>>
                    <?php echo _l('Suspended'); ?>
                </option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Notes -->
        <div class="form-group" app-field-wrapper="notes">
            <label for="notes" class="control-label">
                <?php echo _l('Notes'); ?>
            </label>
            <textarea name="notes" 
                      id="notes" 
                      class="form-control" 
                      rows="4" 
                      placeholder="<?php echo _l('Additional notes about this enrollment'); ?>"><?php echo isset($enrollment) ? $enrollment->notes : ''; ?></textarea>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">
        <i class="fa fa-remove"></i> <?php echo _l('Close'); ?>
    </button>
    <button type="submit" class="btn btn-info" id="save-enrollment-btn">
        <i class="fa fa-check"></i> <?php echo _l('Save'); ?>
    </button>
</div>
                </div>
                </div>
            

<?php echo form_close(); ?>
<?php init_tail(); ?>


<script>
$(function() {
    // Initialize date pickers
    init_datepicker();
    init_datetime_picker($('#enrolled_date'));
    
    // Initialize selectpicker
    $('.selectpicker').selectpicker('refresh');
    
    // Form validation
    appValidateForm($('#enrollment-form'), {
        student_id: 'required',
        course_id: 'required',
        enrolled_date: 'required',
        payment_status: 'required',
        access_status: 'required'
    }, function(form) {
        // On submit
        var button = $('#save-enrollment-btn');
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l('Saving'); ?>...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    $('#enrollment_modal').modal('hide');
                    
                    // Reload table and stats
                    if (typeof enrollmentsTable !== 'undefined') {
                        enrollmentsTable.ajax.reload(null, false);
                    }
                    if (typeof load_enrollment_stats === 'function') {
                        load_enrollment_stats();
                    }
                } else {
                    alert_float('danger', response.message || '<?php echo _l('An error occurred'); ?>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert_float('danger', '<?php echo _l('An error occurred. Please try again.'); ?>');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fa fa-check"></i> <?php echo _l('Save'); ?>');
            }
        });
        
        return false;
    });
});
</script>