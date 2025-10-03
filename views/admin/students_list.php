<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('klms/Lms_admin/add_student'); ?>" class="btn btn-info pull-left">
                                <i class="fa fa-plus-circle"></i> Add New Student
                            </a>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="hr-panel-heading" />
                        
                        <h4>Students Management (<?php echo count($students); ?> students)</h4>
                        
                        <?php if(!empty($students)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped dt-table" id="students-table">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Student</th>
                                            <th>Email</th>
                                            <th>Company</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Registered</th>
                                            <th>Last Login</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($students as $index => $student) { ?>
                                        <tr>
                                            <td><?php echo ($index + 1); ?></td>
                                            <td>
                                                <div class="student-info">
                                                    <div class="student-avatar">
                                                        <?php if(!empty($student['profile_image'])) { ?>
                                                            <img src="<?php echo contact_profile_image_url($student['userid'], 'thumb'); ?>" 
                                                                 alt="<?php echo $student['firstname'] . ' ' . $student['lastname']; ?>" 
                                                                 class="client-profile-image-small">
                                                        <?php } else { ?>
                                                            <div class="student-avatar-placeholder">
                                                                <?php echo strtoupper(substr($student['firstname'], 0, 1) . substr($student['lastname'], 0, 1)); ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="student-details">
                                                        <strong><?php echo $student['firstname'] . ' ' . $student['lastname']; ?></strong>
                                                        <?php if(!empty($student['title'])) { ?>
                                                            <br><small class="text-muted"><?php echo $student['title']; ?></small>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo $student['email']; ?>" class="text-info">
                                                    <?php echo $student['email']; ?>
                                                </a>
                                                <?php if($student['email_verified_at']) { ?>
                                                    <i class="fa fa-check-circle text-success" title="Email Verified"></i>
                                                <?php } else { ?>
                                                    <i class="fa fa-exclamation-circle text-warning" title="Email Not Verified"></i>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $student['company'] ? $student['company'] : '-'; ?></td>
                                            <td><?php echo $student['phonenumber'] ? $student['phonenumber'] : '-'; ?></td>
                                            <td>
                                                <?php if($student['active'] == 1) { ?>
                                                    <span class="label label-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="label label-danger">Inactive</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span title="<?php echo _dt($student['datecreated']); ?>">
                                                    <?php echo time_ago($student['datecreated']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if(!empty($student['last_login'])) { ?>
                                                    <span title="<?php echo _dt($student['last_login']); ?>">
                                                        <?php echo time_ago($student['last_login']); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted">Never</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="row-options">
                                                    <a href="<?php echo admin_url('klms/Lms_admin/view_student/' . $student['userid']); ?>" 
                                                       class="text-info" title="View Details">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('klms/Lms_admin/edit_student/' . $student['userid']); ?>" 
                                                       class="text-success" title="Edit Student">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    |
                                                    <?php if($student['active'] == 1) { ?>
                                                        <a href="<?php echo admin_url('klms/Lms_admin/deactivate_student/' . $student['userid']); ?>" 
                                                           class="text-warning" title="Deactivate">
                                                            <i class="fa fa-ban"></i> Deactivate
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo admin_url('klms/Lms_admin/activate_student/' . $student['userid']); ?>" 
                                                           class="text-success" title="Activate">
                                                            <i class="fa fa-check"></i> Activate
                                                        </a>
                                                    <?php } ?>
                                                    |
                                                    <a href="<?php echo admin_url('klms/Lms_admin/delete_student/' . $student['userid']); ?>" 
                                                       class="text-danger _delete" title="Delete Student">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <i class="fa fa-users fa-3x text-muted"></i>
                                        <h4>No students found</h4>
                                        <p>Start building your student base by adding the first student!</p>
                                        <a href="<?php echo admin_url('klms/Lms_admin/add_student'); ?>" class="btn btn-info">
                                            <i class="fa fa-plus"></i> Add First Student
                                        </a>
                                    </div>
                                </div>
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
}

.student-avatar {
    margin-right: 10px;
}

.student-avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.client-profile-image-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.student-details {
    flex: 1;
}

/* Table Styling */
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.row-options {
    white-space: nowrap;
}

.row-options a {
    margin: 0 2px;
    text-decoration: none;
}

.row-options a:hover {
    text-decoration: underline;
}

/* Status Labels */
.label {
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
}

/* Responsive Table */
@media (max-width: 768px) {
    .student-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .student-avatar {
        margin-bottom: 5px;
        margin-right: 0;
    }
    
    .row-options {
        font-size: 12px;
    }
}

/* DataTable Custom Styling */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 4px 8px;
}

.dataTables_wrapper .dataTables_info {
    color: #6c757d;
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#students-table').DataTable({
        "pageLength": 25,
        "order": [[6, "desc"]], // Sort by registration date
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }, // Disable sorting for # and Actions columns
            { "searchable": false, "targets": [0, 8] }  // Disable search for # and Actions columns
        ],
        "language": {
            "search": "Search students:",
            "lengthMenu": "Show _MENU_ students per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ students",
            "infoEmpty": "No students found",
            "emptyTable": "No students registered yet"
        },
        "responsive": true
    });
});
</script>

<?php init_tail(); ?>