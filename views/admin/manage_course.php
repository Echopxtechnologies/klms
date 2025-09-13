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
                                    <div class="panel panel-default course-card clickable-card" 
                                         onclick="window.location.href='<?php echo admin_url('klms/Lms_admin/manage_videos/' . $course['id']); ?>'">
                                        
                                        <!-- Cover Image Section -->
                                        <div class="course-image-container">
                                            <?php if (!empty($course['cover_image']) && file_exists($course['cover_image'])) { ?>
                                                <img src="<?php echo base_url($course['cover_image']); ?>" 
                                                     alt="<?php echo $course['title']; ?>" 
                                                     class="course-cover-image">
                                            <?php } else { ?>
                                                <div class="course-placeholder-image">
                                                    <i class="fa fa-graduation-cap fa-3x"></i>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        
                                        <div class="panel-body">
                                            <h5><strong><?php echo $course['title']; ?></strong></h5>
                                            <p class="text-muted">
                                                <i class="fa fa-tag"></i> <?php echo $course['category']; ?>
                                            </p>
                                            <p><?php echo substr($course['description'], 0, 80) . '...'; ?></p>
                                            <small class="text-muted">
                                                <i class="fa fa-clock-o"></i> 
                                                Created: <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="panel-footer">
                                            <div class="text-center">
                                                <small class="text-info">
                                                    <i class="fa fa-video-camera"></i> Click to manage videos
                                                </small>
                                            </div>
                                            <div class="course-actions">
                                                <a href="<?php echo admin_url('klms/Lms_admin/edit_course/' . $course['id']); ?>" 
                                                class="course-edit-fab" 
                                                onclick="event.stopPropagation();" 
                                                title="Edit Course">
                                                    <i class="fa fa-edit"></i>
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

    /* Floating Action Button for Edit */
.course-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.course-edit-fab {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.9);
    color: #3498db;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    backdrop-filter: blur(4px);
}

.course-edit-fab:hover {
    background: #3498db;
    color: white;
    transform: scale(1.1);
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
}

.course-card {
    position: relative; /* Required for floating button positioning */
}
.course-card {
    margin-bottom: 20px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.clickable-card {
    cursor: pointer;
}

.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    border-color: #3498db;
}

.course-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
}

.course-cover-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.clickable-card:hover .course-cover-image {
    transform: scale(1.05);
}

.course-placeholder-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.course-card .panel-body {
    padding: 15px;
}

.course-card h5 {
    margin-top: 0;
    margin-bottom: 10px;
    min-height: 40px;
}
</style>

<?php init_tail(); ?>