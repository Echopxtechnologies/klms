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
                                        <li class="breadcrumb-item active">Edit Video</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <h4><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <?php echo form_open(admin_url('lms/Lms_admin/edit_video/' . $course['id'] . '/' . $video['id'])); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title" class="control-label">
                                        <span class="text-danger">*</span> Video Title
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control" 
                                           value="<?php echo htmlspecialchars($video['title']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="control-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($video['description']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="vimeo_url" class="control-label">
                                        <span class="text-danger">*</span> Vimeo URL
                                    </label>
                                    <input type="url" name="vimeo_url" id="vimeo_url" class="form-control" 
                                           value="<?php echo htmlspecialchars($video['vimeo_url']); ?>"
                                           placeholder="https://vimeo.com/123456789" required>
                                    <p class="help-block">
                                        Enter the full Vimeo video URL | 
                                        <a href="<?php echo $video['vimeo_url']; ?>" target="_blank" class="text-info">
                                            <i class="fa fa-external-link"></i> View Current Video
                                        </a>
                                    </p>
                                </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('lms/Lms_admin/manage_videos/' . $course['id']); ?>" 
                               class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <a href="<?php echo admin_url('lms/Lms_admin/delete_video/' . $course['id'] . '/' . $video['id']); ?>" 
                               class="btn btn-danger _delete" style="margin-right: 10px;">
                                <i class="fa fa-trash"></i> Delete Video
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-check"></i> Update Video
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.video-preview {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
}

.video-preview iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.panel-default {
    margin-bottom: 15px;
}

.breadcrumb {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 8px 15px;
    margin-bottom: 20px;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    padding: 0 5px;
    color: #6c757d;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #6c757d;
}
</style>

<script>
// Auto-focus on title field
$(document).ready(function() {
    $('#title').focus();
});

// Validate Vimeo URL
$('#vimeo_url').on('blur', function() {
    var url = $(this).val();
    if (url && !url.match(/vimeo\.com\/\d+/)) {
        alert('Please enter a valid Vimeo URL (e.g., https://vimeo.com/123456789)');
        $(this).focus();
    }
});
</script>

<?php init_tail(); ?>