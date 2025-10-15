<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-user"></i>
                                    <?php echo html_escape($student->firstname . ' ' . $student->lastname); ?>
                                    <?php if ($student->active == 1): ?>
                                        <span class="label label-success">Active</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Inactive</span>
                                    <?php endif; ?>
                                </h4>
                                <p class="text-muted">
                                    <i class="fa fa-envelope"></i> <?php echo html_escape($student->email); ?>
                                    <?php if (!empty($student->phonenumber)): ?>
                                        | <i class="fa fa-phone"></i> <?php echo html_escape($student->phonenumber); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('klms/Lms_admin/edit_student/' . $student->id); ?>" class="btn btn-info">
                                    <i class="fa fa-edit"></i> Edit Student
                                </a>
                                <a href="<?php echo admin_url('klms/Lms_admin/students'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body text-center">
                                <h3 class="bold text-primary"><?php echo $stats['total_courses']; ?></h3>
                                <p class="text-muted">Total Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body text-center">
                                <h3 class="bold text-success"><?php echo $stats['completed_courses']; ?></h3>
                                <p class="text-muted">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body text-center">
                                <h3 class="bold text-warning"><?php echo $stats['in_progress_courses']; ?></h3>
                                <p class="text-muted">In Progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body text-center">
                                <h3 class="bold text-info">
                                    <?php echo date('M d, Y', strtotime($student->datecreated)); ?>
                                </h3>
                                <p class="text-muted">Registered Date</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Details -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="bold"><i class="fa fa-info-circle"></i> Student Information</h4>
                                <hr>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="bold" width="40%">Full Name:</td>
                                            <td><?php echo html_escape($student->firstname . ' ' . $student->lastname); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Email:</td>
                                            <td><?php echo html_escape($student->email); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Phone:</td>
                                            <td><?php echo html_escape($student->phonenumber ?: '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Title:</td>
                                            <td><?php echo html_escape($student->title ?: 'Student'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Status:</td>
                                            <td>
                                                <?php if ($student->active == 1): ?>
                                                    <span class="label label-success">Active</span>
                                                <?php else: ?>
                                                    <span class="label label-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Last Login:</td>
                                            <td>
                                                <?php if (!empty($student->last_login)): ?>
                                                    <?php echo time_ago($student->last_login); ?>
                                                <?php else: ?>
                                                    Never
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Registered:</td>
                                            <td><?php echo date('F d, Y h:i A', strtotime($student->datecreated)); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolled Courses -->
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="bold"><i class="fa fa-graduation-cap"></i> Enrolled Courses</h4>
                                <hr>
                                <?php if (!empty($enrolled_courses)): ?>
                                    <div class="list-group">
                                        <?php foreach ($enrolled_courses as $course): ?>
                                            <div class="list-group-item">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h5 class="bold"><?php echo html_escape($course['course_title']); ?></h5>
                                                        <p class="text-muted no-margin">
                                                            <small>
                                                                Enrolled: <?php echo date('M d, Y', strtotime($course['enrolled_date'])); ?>
                                                            </small>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <div class="progress" style="margin-top: 10px;">
                                                            <div class="progress-bar progress-bar-success" 
                                                                 role="progressbar" 
                                                                 style="width: <?php echo $course['progress_percentage']; ?>%">
                                                                <?php echo round($course['progress_percentage']); ?>%
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?php echo $course['completed_videos']; ?>/<?php echo $course['total_videos']; ?> videos
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">
                                        <i class="fa fa-info-circle"></i> No courses enrolled yet
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Log
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="bold"><i class="fa fa-history"></i> Recent Activity</h4>
                                <hr>
                                <?php //if (!empty($activity_logs)): ?>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php //foreach ($activity_logs as $log): ?>
                                                <tr>
                                                    <td><?php //echo time_ago($log['date']); ?></td>
                                                    <td><?php //echo html_escape($log['description']); ?></td>
                                                </tr>
                                            <?php //endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php //else: ?>
                                    <p class="text-muted text-center">No activity logs found</p>
                                <?php //endif; ?>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    // Any additional JavaScript for the view page
});
</script>

</body>
</html>