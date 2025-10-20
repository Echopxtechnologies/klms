<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <!-- Header Actions -->
            <div class="_buttons">
              <a href="<?php echo admin_url('klms/Lms_admin/add_enrollment'); ?>" class="btn btn-info pull-left">
                <i class="fa fa-plus-circle"></i> New Enrollment
              </a>
              <a href="#" class="btn btn-default pull-left mleft5" onclick="exportEnrollments(); return false;">
                <i class="fa fa-download"></i> Export
              </a>
              <div class="clearfix"></div>
            </div>

            <hr class="hr-panel-heading" />
            <h4 class="bold">
              <i class="fa fa-graduation-cap"></i> Enrollments Management 
              <span class="text-muted">(<?php echo count($enrollments); ?> enrollments)</span>
            </h4>

            <!-- Enrollments Table -->
            <?php if (!empty($enrollments)) { ?>
              <div class="table-responsive">
                <table class="custom-enrollments table-hover table-striped" id="enrollments-table">
                  <thead>
                    <tr>
                      <th width="60">#</th>
                      <th>Student</th>
                      <th>Course</th>
                      <th>Enrolled Date</th>
                      <th>Expiry Date</th>
                      <th>Payment Status</th>
                      <th>Access Status</th>
                      <th width="200" class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($enrollments as $index => $enroll) { ?>
                      <tr data-enrollment-id="<?php echo $enroll['id']; ?>">
                        <td><?php echo ($index + 1); ?></td>
                        <td>
                          <strong><?php echo html_escape($enroll['firstname'] . ' ' . $enroll['lastname']); ?></strong><br>
                          <small class="text-muted"><?php echo html_escape($enroll['email']); ?></small>
                        </td>
                        <td>
                          <span class="label label-info"><?php echo html_escape($enroll['course_title']); ?></span>
                        </td>
                        <td><?php echo _dt($enroll['enrolled_date']); ?></td>
                        <td>
                          <?php
                          if (!empty($enroll['expiry_date'])) {
                              $expiry = strtotime($enroll['expiry_date']);
                              $daysLeft = floor(($expiry - time()) / 86400);
                              if ($daysLeft < 0) {
                                  echo '<span class="label label-danger">Expired</span>';
                              } elseif ($daysLeft < 7) {
                                  echo '<span class="label label-warning">' . $daysLeft . ' days left</span>';
                              } else {
                                  echo _d($enroll['expiry_date']);
                              }
                          } else {
                              echo '<span class="label label-default">No Expiry</span>';
                          }
                          ?>
                        </td>
                        <td>
                          <span class="label label-<?php echo strtolower($enroll['payment_status']) === 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($enroll['payment_status']); ?>
                          </span>
                        </td>
                        <td>
                          <?php
                          $statusClass = 'default';
                          if (strtolower($enroll['access_status']) === 'active') $statusClass = 'success';
                          elseif (strtolower($enroll['access_status']) === 'expired') $statusClass = 'danger';
                          elseif (strtolower($enroll['access_status']) === 'suspended') $statusClass = 'warning';
                          ?>
                          <span class="label label-<?php echo $statusClass; ?>">
                            <?php echo ucfirst($enroll['access_status']); ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="btn-group">
                            <a href="<?php echo admin_url('klms/Lms_admin/view_enrollment/' . $enroll['id']); ?>" 
                               class="btn btn-default btn-sm" title="View">
                              <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?php echo admin_url('klms/Lms_admin/enrollment/' . $enroll['id']); ?>" 
                               class="btn btn-info btn-sm" title="Edit">
                              <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="deleteEnrollment(<?php echo $enroll['id']; ?>)">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="text-center mtop50 mbot50">
                <i class="fa fa-graduation-cap" style="font-size: 80px; color: #ddd;"></i>
                <h3 class="text-muted">No enrollments found</h3>
                <p class="text-muted">Add your first enrollment to start tracking course progress.</p>
                <a href="<?php echo admin_url('klms/Lms_admin/add_enrollment'); ?>" class="btn btn-info btn-lg mtop20">
                  <i class="fa fa-plus"></i> Add Enrollment
                </a>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
<style>
/* Table Layout Fix */
#enrollments-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: auto;
  font-size: 13px;
}

#enrollments-table thead th {
  background: #f8f9fa;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
  padding: 10px 12px;
  vertical-align: middle;
  text-align: left;
  white-space: nowrap;
}

#enrollments-table tbody td {
  padding: 10px 12px;
  vertical-align: middle;
  border-bottom: 1px solid #f0f0f0;
}

/* Label Styling */
.label {
  padding: 4px 10px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 10px;
  display: inline-block;
}

/* Align last column (Actions) */
#enrollments-table th:last-child,
#enrollments-table td:last-child {
  text-align: center;
  white-space: nowrap;
}

/* Action Buttons */
.btn-group .btn {
  margin: 0 2px;
  border-radius: 4px !important;
  padding: 5px 8px;
}

/* Hover Highlight */
#enrollments-table tbody tr:hover {
  background-color: #f9fafb;
}

/* Make labels and text vertically centered */
td .label,
td small,
td strong {
  line-height: 1.4;
  vertical-align: middle;
}

/* Responsive tweak */
@media (max-width: 768px) {
  #enrollments-table thead {
    display: none;
  }
  #enrollments-table tbody tr {
    display: block;
    margin-bottom: 10px;
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 10px;
  }
  #enrollments-table tbody td {
    display: flex;
    justify-content: space-between;
    padding: 6px 8px;
  }
  #enrollments-table tbody td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #555;
  }
}
</style>


<script>
$(document).ready(function () {
    $('.custom-enrollments').DataTable({
        pageLength: 25,
        order: [[3, 'desc']], // by enrolled date
        columnDefs: [
            { orderable: false, targets: [7] },
            { searchable: false, targets: [7] }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search enrollments...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ enrollments",
            emptyTable: "No enrollments found"
        }
    });
});

function deleteEnrollment(id) {
    if (confirm('Are you sure you want to delete this enrollment?')) {
        $.post(admin_url + 'klms/Lms_admin/delete_enrollment/' + id, function (response) {
            var res = JSON.parse(response);
            if (res.success) {
                alert_float('success', res.message);
                location.reload();
            } else {
                alert_float('danger', res.message);
            }
        });
    }
}

function exportEnrollments() {
    window.location.href = admin_url + 'klms/Lms_admin/export_enrollments';
}
</script>


<?php init_tail(); ?>