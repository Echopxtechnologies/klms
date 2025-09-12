<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('klms/Lms_admin/add_course'); ?>" class="btn btn-info pull-left">
                                <i class="fa fa-plus-circle"></i> Add New Course
                            </a>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="hr-panel-heading" />
                        
                        <h4><?php echo $title; ?> (<?php echo count($courses); ?> courses)</h4>
                        
                        <div class="row">
                            <?php if(!empty($courses)) { ?>
                                <?php foreach($courses as $course) { ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="panel panel-default course-card">
                                        <div class="panel-body">
                                            <h5><strong><?php echo $course['title']; ?></strong></h5>
                                            <p class="text-muted">
                                                <i class="fa fa-tag"></i> <?php echo $course['category']; ?>
                                            </p>
                                            <p><?php echo substr($course['description'], 0, 100) . '...'; ?></p>
                                            <small class="text-muted">
                                                <i class="fa fa-clock-o"></i> 
                                                Created: <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="panel-footer">
                                            <div class="row-options text-center">
                                                <a href="<?php echo admin_url('klms/Lms_admin/edit_course/'.$course['id']); ?>" class="btn btn-sm btn-default">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <a href="<?php echo admin_url('klms/Lms_admin/delete_course/'.$course['id']); ?>" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="col-md-12 text-center">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h4>No courses found</h4>
                                            <p>Get started by adding your first course!</p>
                                            <a href="<?php echo admin_url('klms/Lms_admin/add_course'); ?>" class="btn btn-info">
                                                <i class="fa fa-plus"></i> Add New Course
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
</div>

<style>
.course-card {

    margin-bottom: 25px;
    transition: transform 0.2s;
}
.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<?php init_tail(); ?>