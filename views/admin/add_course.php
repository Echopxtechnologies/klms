<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <?php echo form_open(admin_url('klms/Lms_admin/add_course')); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title" class="control-label">
                                        <span class="text-danger">*</span><?php echo _l('klms_course_title'); ?>
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="category" class="control-label"><?php echo _l('klms_course_category'); ?></label>
                                    <input type="text" name="category" id="category" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="description" class="control-label">
                                        <span class="text-danger">*</span><?php echo _l('klms_course_description'); ?>
                                    </label>
                                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                                </div>

                                <!-- <div class="form-group">
                                    <label for="vimeo_url" class="control-label">
                                        <span class="text-danger">*</span> echo _l('klms_course_vimeo_url'); 
                                    </label>
                                    <input type="url" name="vimeo_url" id="vimeo_url" class="form-control" 
                                           placeholder="https://vimeo.com/123456789" required>
                                    <p class="help-block">Enter the full Vimeo video URL</p>
                                </div> -->
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-check"></i> <?php echo _l('save'); ?>
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