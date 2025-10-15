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
                            <a href="<?php echo admin_url('klms/Lms_admin/add_student'); ?>" class="btn btn-info pull-left">
                                <i class="fa fa-plus-circle"></i> Add New Student
                            </a>
                            <a href="#" class="btn btn-default pull-left mleft5" onclick="exportStudents(); return false;">
                                <i class="fa fa-download"></i> Export
                            </a>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="hr-panel-heading" />
                        
                        <h4 class="bold">
                            <i class="fa fa-users"></i> Students Management 
                            <span class="text-muted">(<?php echo count($students); ?> students)</span>
                        </h4>
                        
                        <!-- Filter Options -->
                        <div class="row mtop15 mbot15">
                            <div class="col-md-3">
                                <select class="form-control selectpicker" id="filter_status" data-width="100%">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control selectpicker" id="filter_verified" data-width="100%">
                                    <option value="">Email Verification</option>
                                    <option value="verified">Verified</option>
                                    <option value="unverified">Not Verified</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control selectpicker" id="filter_login" data-width="100%">
                                    <option value="">Last Login</option>
                                    <option value="logged_in">Has Logged In</option>
                                    <option value="never_logged_in">Never Logged In</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-default btn-block" onclick="resetFilters()">
                                    <i class="fa fa-refresh"></i> Reset Filters
                                </button>
                            </div>
                        </div>

                        <!-- Students Table -->
                        <?php if(!empty($students)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="students-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="select_all_students">
                                            </th>
                                            <th width="60">#</th>
                                            <th>Student</th>
                                            <th>Email</th>
                                            <th>Company</th>
                                            <th>Phone</th>
                                            <th width="80">Status</th>
                                            <th>Registered</th>
                                            <th>Last Login</th>
                                            <th width="200" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($students as $index => $student) { ?>
                                        <tr data-student-id="<?php echo $student['id']; ?>" data-status="<?php echo $student['active']; ?>">
                                            <td>
                                                <input type="checkbox" class="student-checkbox" value="<?php echo $student['id']; ?>">
                                            </td>
                                            <td><?php echo ($index + 1); ?></td>
                                            <td>
                                                <div class="student-info">
                                                    <?php 
                                                    $initials = strtoupper(substr($student['firstname'], 0, 1) . substr($student['lastname'], 0, 1));
                                                    $colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a'];
                                                    $color = $colors[array_rand($colors)];
                                                    ?>
                                                    <div class="student-avatar-placeholder" style="background: <?php echo $color; ?>">
                                                        <?php echo $initials; ?>
                                                    </div>
                                                    <div class="student-details">
                                                        <strong class="student-name">
                                                            <a href="<?php echo admin_url('klms/Lms_admin/view_student/' . $student['id']); ?>">
                                                                <?php echo html_escape($student['firstname'] . ' ' . $student['lastname']); ?>
                                                            </a>
                                                        </strong>
                                                        <?php if(!empty($student['title'])) { ?>
                                                            <br><small class="text-muted"><?php echo html_escape($student['title']); ?></small>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo $student['email']; ?>" class="text-info">
                                                    <?php echo html_escape($student['email']); ?>
                                                </a>
                                                <?php if($student['email_verified_at']) { ?>
                                                    <i class="fa fa-check-circle text-success" title="Email Verified" data-toggle="tooltip"></i>
                                                <?php } else { ?>
                                                    <i class="fa fa-exclamation-circle text-warning" title="Email Not Verified" data-toggle="tooltip"></i>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($student['company']) ? html_escape($student['company']) : '<span class="text-muted">-</span>'; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($student['phonenumber'])) { ?>
                                                    <a href="tel:<?php echo $student['phonenumber']; ?>">
                                                        <?php echo html_escape($student['phonenumber']); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo $student['active'] == 1 ? 'success' : 'danger'; ?> student-status-badge">
                                                    <?php echo $student['active'] == 1 ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span data-toggle="tooltip" title="<?php echo _dt($student['datecreated']); ?>">
                                                    <?php echo time_ago($student['datecreated']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if(!empty($student['last_login'])) { ?>
                                                    <span data-toggle="tooltip" title="<?php echo _dt($student['last_login']); ?>">
                                                        <?php echo time_ago($student['last_login']); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted">Never</span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="<?php echo admin_url('klms/Lms_admin/view_student/' . $student['id']); ?>" 
                                                       class="btn btn-default btn-sm" 
                                                       data-toggle="tooltip" 
                                                       title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('klms/Lms_admin/edit_student/' . $student['id']); ?>" 
                                                       class="btn btn-info btn-sm" 
                                                       data-toggle="tooltip" 
                                                       title="Edit Student">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-<?php echo $student['active'] == 1 ? 'warning' : 'success'; ?> btn-sm" 
                                                            onclick="toggleStudentStatus(<?php echo $student['id']; ?>, <?php echo $student['active']; ?>)"
                                                            data-toggle="tooltip" 
                                                            title="<?php echo $student['active'] == 1 ? 'Deactivate' : 'Activate'; ?>">
                                                        <i class="fa fa-<?php echo $student['active'] == 1 ? 'ban' : 'check'; ?>"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm" 
                                                            onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo addslashes($student['firstname'] . ' ' . $student['lastname']); ?>')"
                                                            data-toggle="tooltip" 
                                                            title="Delete Student">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bulk Actions -->
                            <div id="bulk-actions-wrapper" style="display: none;">
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <strong>
                                            <span id="selected-count">0</span> student(s) selected
                                        </strong>
                                        <div class="btn-group mleft10">
                                            <button type="button" class="btn btn-success btn-sm" onclick="bulkActivate()">
                                                <i class="fa fa-check"></i> Activate Selected
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm" onclick="bulkDeactivate()">
                                                <i class="fa fa-ban"></i> Deactivate Selected
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                                                <i class="fa fa-trash"></i> Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } else { ?>
                            <!-- Empty State -->
                            <div class="text-center mtop50 mbot50">
                                <i class="fa fa-users" style="font-size: 80px; color: #ddd;"></i>
                                <h3 class="text-muted">No students found</h3>
                                <p class="text-muted">Start building your student base by adding the first student!</p>
                                <a href="<?php echo admin_url('klms/Lms_admin/add_student'); ?>" class="btn btn-info btn-lg mtop20">
                                    <i class="fa fa-plus"></i> Add First Student
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
/* Student Info Styling */
.student-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar-placeholder {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 13px;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.student-details {
    flex: 1;
    min-width: 0;
}

.student-name {
    display: block;
    line-height: 1.4;
}

.student-name a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
}

.student-name a:hover {
    color: #2572ec;
    text-decoration: underline;
}

/* Table Styling */
#students-table {
    font-size: 13px;
}

#students-table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 8px;
    white-space: nowrap;
}

#students-table tbody td {
    vertical-align: middle;
    padding: 10px 8px;
}

#students-table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Status Labels */
.student-status-badge {
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 12px;
    display: inline-block;
}

/* Action Buttons */
.btn-group .btn {
    margin: 0;
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-radius: 4px 0 0 4px;
}

.btn-group .btn:last-child {
    border-radius: 0 4px 4px 0;
}

/* Checkbox Styling */
input[type="checkbox"] {
    cursor: pointer;
    transform: scale(1.2);
}

#select_all_students {
    margin: 0;
}

/* Filter Section */
.selectpicker {
    border: 1px solid #ddd !important;
}

/* DataTable Customization */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_length select {
    padding: 5px 30px 5px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px 10px;
    margin-left: 5px;
}

.dataTables_wrapper .dataTables_info {
    color: #6c757d;
    font-size: 13px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 5px 10px;
    margin: 0 2px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #2572ec;
    color: white !important;
    border-color: #2572ec;
}

/* Responsive */
@media (max-width: 768px) {
    .student-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 4px !important;
        margin: 2px 0 !important;
    }
}

/* Bulk Actions */
#bulk-actions-wrapper {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-top: 15px;
}
</style>

<script>
var studentsTable;

$(document).ready(function() {
    'use strict';

    // Initialize DataTable
    studentsTable = $('#students-table').DataTable({
        "pageLength": 25,
        "order": [[7, "desc"]], // Sort by registration date (descending)
        "columnDefs": [
            { "orderable": false, "targets": [0, 9] }, // Checkbox and Actions columns
            { "searchable": false, "targets": [0, 9] }
        ],
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search students...",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ students",
            "infoEmpty": "No students found",
            "infoFiltered": "(filtered from _MAX_ total students)",
            "emptyTable": "No students registered yet",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        "responsive": false,
        "autoWidth": false,
        "stateSave": true, // Remember table state
        "drawCallback": function() {
            $('[data-toggle="tooltip"]').tooltip();
            updateBulkActionsVisibility();
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize selectpicker
    $('.selectpicker').selectpicker();

    // Filter by Status
    $('#filter_status').on('change', function() {
        var status = $(this).val();
        if (status === '') {
            studentsTable.column(6).search('').draw();
        } else {
            var statusText = status == '1' ? 'Active' : 'Inactive';
            studentsTable.column(6).search(statusText).draw();
        }
    });

    // Filter by Email Verification
    $('#filter_verified').on('change', function() {
        var filter = $(this).val();
        if (filter === 'verified') {
            studentsTable.column(3).search('fa-check-circle').draw();
        } else if (filter === 'unverified') {
            studentsTable.column(3).search('fa-exclamation-circle').draw();
        } else {
            studentsTable.column(3).search('').draw();
        }
    });

    // Filter by Login Status
    $('#filter_login').on('change', function() {
        var filter = $(this).val();
        if (filter === 'logged_in') {
            studentsTable.column(8).search('^(?!.*Never).*$', true, false).draw();
        } else if (filter === 'never_logged_in') {
            studentsTable.column(8).search('Never').draw();
        } else {
            studentsTable.column(8).search('').draw();
        }
    });

    // Select All Checkbox
    $('#select_all_students').on('change', function() {
        var checked = $(this).is(':checked');
        $('.student-checkbox:visible').prop('checked', checked);
        updateBulkActionsVisibility();
    });

    // Individual Checkbox
    $(document).on('change', '.student-checkbox', function() {
        updateBulkActionsVisibility();
        updateSelectAllCheckbox();
    });
});

// Reset Filters
function resetFilters() {
    $('#filter_status').val('').selectpicker('refresh');
    $('#filter_verified').val('').selectpicker('refresh');
    $('#filter_login').val('').selectpicker('refresh');
    studentsTable.search('').columns().search('').draw();
}

// Update Select All Checkbox
function updateSelectAllCheckbox() {
    var total = $('.student-checkbox:visible').length;
    var checked = $('.student-checkbox:visible:checked').length;
    $('#select_all_students').prop('checked', total > 0 && total === checked);
}

// Update Bulk Actions Visibility
function updateBulkActionsVisibility() {
    var checked = $('.student-checkbox:checked').length;
    $('#selected-count').text(checked);
    if (checked > 0) {
        $('#bulk-actions-wrapper').slideDown();
    } else {
        $('#bulk-actions-wrapper').slideUp();
    }
}

// Toggle Student Status
function toggleStudentStatus(studentId, currentStatus) {
    var action = currentStatus == 1 ? 'deactivate' : 'activate';
    var confirmMsg = 'Are you sure you want to ' + action + ' this student?';
    
    if (confirm(confirmMsg)) {
        $.ajax({
            url: admin_url + 'klms/Lms_admin/toggle_student_status/' + studentId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    
                    // Update the row
                    var row = $('tr[data-student-id="' + studentId + '"]');
                    var newStatus = response.new_status;
                    
                    row.attr('data-status', newStatus);
                    row.find('.student-status-badge')
                        .removeClass('label-success label-danger')
                        .addClass(newStatus == 1 ? 'label-success' : 'label-danger')
                        .text(newStatus == 1 ? 'Active' : 'Inactive');
                    
                    // Update button
                    var btn = row.find('button[onclick*="toggleStudentStatus"]');
                    btn.removeClass('btn-warning btn-success')
                       .addClass(newStatus == 1 ? 'btn-warning' : 'btn-success')
                       .attr('onclick', 'toggleStudentStatus(' + studentId + ', ' + newStatus + ')')
                       .attr('data-original-title', newStatus == 1 ? 'Deactivate' : 'Activate')
                       .html('<i class="fa fa-' + (newStatus == 1 ? 'ban' : 'check') + '"></i>');
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while updating student status');
            }
        });
    }
}

// Delete Student
function deleteStudent(studentId, studentName) {
    if (confirm('Are you sure you want to delete ' + studentName + '? This action cannot be undone.')) {
        $.ajax({
            url: admin_url + 'klms/Lms_admin/delete_student/' + studentId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    
                    // Remove the row
                    var row = $('tr[data-student-id="' + studentId + '"]');
                    studentsTable.row(row).remove().draw();
                    
                    // Update count
                    updateStudentCount();
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while deleting student');
            }
        });
    }
}

// Bulk Activate
function bulkActivate() {
    var studentIds = getSelectedStudentIds();
    if (studentIds.length === 0) {
        alert_float('warning', 'Please select at least one student');
        return;
    }
    
    if (confirm('Activate ' + studentIds.length + ' selected student(s)?')) {
        bulkAction('activate', studentIds);
    }
}

// Bulk Deactivate
function bulkDeactivate() {
    var studentIds = getSelectedStudentIds();
    if (studentIds.length === 0) {
        alert_float('warning', 'Please select at least one student');
        return;
    }
    
    if (confirm('Deactivate ' + studentIds.length + ' selected student(s)?')) {
        bulkAction('deactivate', studentIds);
    }
}

// Bulk Delete
function bulkDelete() {
    var studentIds = getSelectedStudentIds();
    if (studentIds.length === 0) {
        alert_float('warning', 'Please select at least one student');
        return;
    }
    
    if (confirm('Delete ' + studentIds.length + ' selected student(s)? This action cannot be undone.')) {
        bulkAction('delete', studentIds);
    }
}

// Get Selected Student IDs
function getSelectedStudentIds() {
    var ids = [];
    $('.student-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    return ids;
}

// Bulk Action Handler
function bulkAction(action, studentIds) {
    $.ajax({
        url: admin_url + 'klms/Lms_admin/bulk_action',
        type: 'POST',
        data: {
            action: action,
            student_ids: studentIds,
            [csrf_token_name]: csrf_token_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                location.reload();
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function() {
            alert_float('danger', 'An error occurred during bulk action');
        }
    });
}

// Export Students
function exportStudents() {
    window.location.href = admin_url + 'klms/Lms_admin/export_students';
}

// Update Student Count
function updateStudentCount() {
    var count = studentsTable.rows().count();
    $('h4 .text-muted').text('(' + count + ' students)');
}
</script>

<?php init_tail(); ?>
</body>
</html>