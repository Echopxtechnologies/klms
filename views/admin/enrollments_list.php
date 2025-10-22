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
              <a href="<?php echo admin_url('lms/Lms_admin/enrollment'); ?>" class="btn btn-info pull-left">
                <i class="fa fa-plus-circle"></i> New Enrollment
              </a>
              <div class="clearfix"></div>
            </div>

            <hr class="hr-panel-heading" />

            <!-- Enrollments Table -->
            <div class="clearfix"></div>
            <?php if (!empty($enrollments)) { ?>
              <div class="table-responsive">
                <table class="table table-enrollments dt-table" id="enrollments-table" data-order-col="3" data-order-type="desc">
                  <thead>
                    <tr>
                      <th width="50">#</th>
                      <th width="200">Student</th>
                      <th width="180">Course</th>
                      <th width="130">Enrolled Date</th>
                      <th width="120">Expiry Date</th>
                      <th width="120">Payment Status</th>
                      <th width="120">Access Status</th>
                      <th width="150" class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($enrollments as $index => $enroll) { ?>
                      <tr>
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
                          <?php
                          $payment_status = strtolower($enroll['payment_status']);
                          $payment_class = 'default';
                          if ($payment_status === 'paid') $payment_class = 'success';
                          elseif ($payment_status === 'pending') $payment_class = 'warning';
                          elseif ($payment_status === 'failed') $payment_class = 'danger';
                          elseif ($payment_status === 'free') $payment_class = 'info';
                          ?>
                          <span class="label label-<?php echo $payment_class; ?>">
                            <?php echo ucfirst($enroll['payment_status']); ?>
                          </span>
                        </td>
                        <td>
                          <?php
                          $access_status = strtolower($enroll['access_status']);
                          $status_class = 'default';
                          if ($access_status === 'active') $status_class = 'success';
                          elseif ($access_status === 'expired') $status_class = 'danger';
                          elseif ($access_status === 'suspended') $status_class = 'warning';
                          ?>
                          <span class="label label-<?php echo $status_class; ?>">
                            <?php echo ucfirst($enroll['access_status']); ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="btn-group btn-group-sm">
                            <a href="<?php echo admin_url('lms/Lms_admin/enrollment/' . $enroll['id']); ?>" 
                               class="btn btn-default btn-icon" 
                               data-toggle="tooltip" 
                               title="View Details">
                              <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?php echo admin_url('lms/Lms_admin/enrollment/' . $enroll['id']); ?>" 
                               class="btn btn-info btn-icon" 
                               data-toggle="tooltip" 
                               title="Edit">
                              <i class="fa fa-edit"></i>
                            </a>
                            <a href="<?php echo admin_url('lms/Lms_admin/delete_enrollment/' . $enroll['id']); ?>" 
                               class="btn btn-danger btn-icon _delete" 
                               data-toggle="tooltip" 
                               title="Delete">
                              <i class="fa fa-trash"></i>
                            </a>
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
                <a href="<?php echo admin_url('lms/Lms_admin/enrollment'); ?>" class="btn btn-info btn-lg mtop20">
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
/* Table Container */
.table-responsive {
    overflow-x: auto;
    margin-top: 20px;
}

/* Table Base Styling */
.table-enrollments {
    width: 100%;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0 !important;
}

/* Table Header */
.table-enrollments thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 10px;
    vertical-align: middle;
    white-space: nowrap;
    text-align: left;
}

/* Table Body */
.table-enrollments tbody td {
    padding: 10px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
    word-wrap: break-word;
}

/* Row Hover Effect */
.table-enrollments tbody tr:hover {
    background-color: #f9fafb;
}

/* Column Width Control */
.table-enrollments th:nth-child(1),
.table-enrollments td:nth-child(1) { width: 50px; text-align: center; }

.table-enrollments th:nth-child(2),
.table-enrollments td:nth-child(2) { width: 200px; }

.table-enrollments th:nth-child(3),
.table-enrollments td:nth-child(3) { width: 180px; }

.table-enrollments th:nth-child(4),
.table-enrollments td:nth-child(4) { width: 130px; }

.table-enrollments th:nth-child(5),
.table-enrollments td:nth-child(5) { width: 120px; }

.table-enrollments th:nth-child(6),
.table-enrollments td:nth-child(6) { width: 120px; }

.table-enrollments th:nth-child(7),
.table-enrollments td:nth-child(7) { width: 120px; }

.table-enrollments th:nth-child(8),
.table-enrollments td:nth-child(8) { width: 150px; text-align: center; }

/* Labels */
.label {
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    display: inline-block;
    white-space: nowrap;
}

/* Action Buttons */
.btn-group-sm .btn {
    padding: 4px 8px;
    font-size: 12px;
}

.btn-icon {
    padding: 4px 8px !important;
}

.btn-icon i {
    font-size: 13px;
}

/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_length select {
    padding: 5px 10px;
    border-radius: 3px;
    border: 1px solid #d2d6de;
}

.dataTables_wrapper .dataTables_filter input {
    padding: 5px 10px;
    border-radius: 3px;
    border: 1px solid #d2d6de;
    margin-left: 5px;
}

/* Make small text smaller */
small.text-muted {
    font-size: 11px;
    display: block;
    margin-top: 2px;
}

/* Fix button alignment in action column */
.text-center .btn-group {
    display: inline-flex;
    gap: 4px;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .table-enrollments {
        font-size: 12px;
    }
    
    .table-enrollments th,
    .table-enrollments td {
        padding: 8px 6px;
    }
}
</style>

<script>
$(document).ready(function() {
    'use strict';
    
    // Initialize DataTable
    var enrollmentsTable = $('.table-enrollments').DataTable({
        "responsive": false,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[3, "desc"]], // Order by enrolled date
        "columnDefs": [
            {
                "targets": [0, 7], // # and Actions columns
                "orderable": false,
                "searchable": false
            }
        ],
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search enrollments...",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ enrollments",
            "infoEmpty": "No enrollments available",
            "infoFiltered": "(filtered from _MAX_ total enrollments)",
            "zeroRecords": "No matching enrollments found",
            "emptyTable": "No enrollments available"
        },
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        "drawCallback": function() {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    
    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Delete enrollment function
function deleteEnrollment(id) {
    if (confirm('Are you sure you want to delete this enrollment? This action cannot be undone.')) {
        window.location.href = admin_url + 'lms/Lms_admin/delete_enrollment/' + id;
    }
}
</script>

<?php init_tail(); ?>