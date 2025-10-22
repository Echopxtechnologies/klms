<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="bold"><i class="fa fa-edit"></i> Edit Enrollment</h4>
            <hr class="hr-panel-heading">

            <form id="editEnrollmentForm" method="post" action="<?php echo admin_url('lms/Lms_admin/update_enrollment'); ?>">
              <input type="hidden" name="id" value="<?php echo $enrollment->id; ?>">

              <div class="form-group">
                <label for="student_id">Student</label>
                <select name="student_id" class="form-control selectpicker" data-live-search="true" required>
                  <option value="">Select Student</option>
                  <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student->id; ?>" 
                      <?php echo ($student->id == $enrollment->student_id) ? 'selected' : ''; ?>>
                      <?php echo $student->firstname . ' ' . $student->lastname . ' (' . $student->email . ')'; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="course_id">Course</label>
                <select name="course_id" class="form-control selectpicker" data-live-search="true" required>
                  <option value="">Select Course</option>
                  <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course->id; ?>" 
                      <?php echo ($course->id == $enrollment->course_id) ? 'selected' : ''; ?>>
                      <?php echo $course->title; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="enrolled_date">Enrolled Date</label>
                    <input type="text" name="enrolled_date" class="form-control datepicker"
                      value="<?php echo _dt($enrollment->enrolled_date); ?>" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="text" name="expiry_date" class="form-control datepicker"
                      value="<?php echo !empty($enrollment->expiry_date) ? _d($enrollment->expiry_date) : ''; ?>">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="payment_status">Payment Status</label>
                    <select name="payment_status" class="form-control selectpicker">
                      <option value="paid" <?php echo ($enrollment->payment_status == 'paid') ? 'selected' : ''; ?>>Paid</option>
                      <option value="pending" <?php echo ($enrollment->payment_status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                      <option value="failed" <?php echo ($enrollment->payment_status == 'failed') ? 'selected' : ''; ?>>Failed</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="access_status">Access Status</label>
                    <select name="access_status" class="form-control selectpicker">
                      <option value="active" <?php echo ($enrollment->access_status == 'active') ? 'selected' : ''; ?>>Active</option>
                      <option value="expired" <?php echo ($enrollment->access_status == 'expired') ? 'selected' : ''; ?>>Expired</option>
                      <option value="suspended" <?php echo ($enrollment->access_status == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="text-right mtop20">
                <a href="<?php echo admin_url('lms/Lms_admin/enrollments'); ?>" class="btn btn-default">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-info">
                  <i class="fa fa-check"></i> Save Changes
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
  $('.selectpicker').selectpicker();
  $('.datepicker').datetimepicker({
    format: 'Y-m-d H:i',
    step: 30
  });

  $('#editEnrollmentForm').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.post(form.attr('action'), form.serialize(), function(response) {
      var res = JSON.parse(response);
      if (res.success) {
        alert_float('success', res.message);
        setTimeout(() => {
          window.location.href = admin_url + 'lms/Lms_admin/enrollments';
        }, 1000);
      } else {
        alert_float('danger', res.message);
      }
    });
  });
});
</script>
