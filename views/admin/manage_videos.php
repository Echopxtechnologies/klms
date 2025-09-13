<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Breadcrumb -->
                        <div class="row">
                            <div class="col-md-12">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo admin_url('klms/Lms_admin/courses'); ?>">
                                                <i class="fa fa-arrow-left"></i> All Courses
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active"><?php echo $course['title']; ?></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <!-- Course Info -->
                        <div class="row">
                            <div class="col-md-8">
                                <h3><?php echo $course['title']; ?></h3>
                                <p class="text-muted"><?php echo $course['description']; ?></p>
                                <small class="text-muted">
                                    <i class="fa fa-tag"></i> <?php echo $course['category']; ?> | 
                                    <i class="fa fa-clock-o"></i> Created: <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                                </small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('klms/Lms_admin/add_video/' . $course['id']); ?>" class="btn btn-info">
                                    <i class="fa fa-plus-circle"></i> Add New Video
                                </a>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Videos List -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Videos (<?php echo count($videos); ?>)</h4>
                                
                                <?php if(!empty($videos)) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="50">#</th>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Duration</th>
                                                    <th>Vimeo URL</th>
                                                    <th>Created</th>
                                                    <th width="150">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($videos as $index => $video) { ?>
                                                <tr>
                                                    <td><?php echo ($index + 1); ?></td>
                                                    <td><strong><?php echo $video['title']; ?></strong></td>
                                                    <td><?php echo substr($video['description'], 0, 50) . (strlen($video['description']) > 50 ? '...' : ''); ?></td>
                                                    <td><?php echo $video['duration'] ? $video['duration'] : 'N/A'; ?></td>
                                                    <td>
                                                        <a href="<?php echo $video['vimeo_url']; ?>" target="_blank" class="text-info">
                                                            <i class="fa fa-external-link"></i> View
                                                        </a>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($video['created_at'])); ?></td>
                                                    <td>
                                                        <div class="row-options">
                                                            <a href="<?php echo admin_url('klms/Lms_admin/edit_video/' . $course['id'] . '/' . $video['id']); ?>" class="text-info">
                                                                <i class="fa fa-edit"></i> Edit
                                                            </a> | 
                                                            <a href="<?php echo admin_url('klms/Lms_admin/delete_video/' . $course['id'] . '/' . $video['id']); ?>" 
                                                               class="text-danger _delete">
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
                                                <i class="fa fa-video-camera fa-3x text-muted"></i>
                                                <h4>No videos found</h4>
                                                <p>Start building your course by adding the first video!</p>
                                                <a href="<?php echo admin_url('klms/Lms_admin/add_video/' . $course['id']); ?>" class="btn btn-info">
                                                    <i class="fa fa-plus"></i> Add First Video
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
</div>

<?php init_tail(); ?>