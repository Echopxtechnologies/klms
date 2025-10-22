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
                                            <a href="<?php echo admin_url('lms/Lms_admin/courses'); ?>">All Courses</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo admin_url('lms/Lms_admin/manage_videos/' . $course['id']); ?>">
                                                <?php echo $course['title']; ?>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active">Add Video</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <h4><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <?php echo form_open(admin_url('lms/Lms_admin/add_video/' . $course['id'])); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title" class="control-label">
                                        <span class="text-danger">*</span> Video Title
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="control-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="vimeo_url" class="control-label">
                                        <span class="text-danger">*</span> Vimeo URL
                                    </label>
                                    <input type="url" name="vimeo_url" id="vimeo_url" class="form-control" 
                                           placeholder="https://vimeo.com/123456789" required>
                                    <p class="help-block">Enter the full Vimeo video URL</p>
                                </div>

                                
                            </div>

                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Course Info</div>
                                    <div class="panel-body">
                                        <strong><?php echo $course['title']; ?></strong>
                                        <br><small class="text-muted"><?php echo $course['category']; ?></small>
                                        <hr>
                                        <p><?php echo $course['description']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('lms/Lms_admin/manage_videos/' . $course['id']); ?>" 
                               class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-check"></i> Save Video
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>