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
                                            <a href="<?php echo admin_url('lms/Lms_admin/courses'); ?>">
                                                <i class="fa fa-arrow-left"></i> All Courses
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active">Edit Course</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <?php echo form_open_multipart(admin_url('lms/Lms_admin/edit_course/' . $course['id'])); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title" class="control-label">
                                        <span class="text-danger">*</span> Course Title
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control" 
                                           value="<?php echo htmlspecialchars($course['title']); ?>" required>
                                </div>

                                <!-- Category -->
                                <div class="form-group">
                                    <label for="category" class="control-label">Category</label>
                                    <input type="text" name="category" id="category" class="form-control" 
                                           value="<?php echo htmlspecialchars($course['category']); ?>">
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="control-label">
                                        <span class="text-danger">*</span> Description
                                    </label>
                                    <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                                </div>

                                <!-- Cover Image -->
                                <div class="form-group">
                                    <label for="cover_image" class="control-label">Cover Image</label>
                                    
                                    <!-- Current Image Display -->
                                    <?php if (!empty($course['cover_image']) && file_exists($course['cover_image'])) { ?>
                                        <div class="current-image" style="margin-bottom: 10px;">
                                            <label class="control-label">Current Image:</label><br>
                                            <img src="<?php echo base_url($course['cover_image']); ?>" 
                                                 alt="Current cover image" 
                                                 style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                            <br><small class="text-muted">Current cover image</small>
                                        </div>
                                    <?php } ?>
                                    
                                    <input type="file" name="cover_image" id="cover_image" class="form-control" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <p class="help-block">
                                        <?php if (!empty($course['cover_image'])) { ?>
                                            Upload a new image to replace the current one. Leave empty to keep current image.
                                        <?php } else { ?>
                                            Upload a cover image for the course (JPG, PNG, GIF). Recommended size: 400x300px
                                        <?php } ?>
                                    </p>
                                    
                                    <!-- New Image Preview -->
                                    <div id="image-preview" style="display: none; margin-top: 10px;">
                                        <label class="control-label">New Image Preview:</label><br>
                                        <img id="preview-img" src="" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                        <br><small class="text-muted">New image preview</small>
                                    </div>
                                </div>

                                <!-- Price and Duration Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price" class="control-label">Price (₹)</label>
                                            <input type="number" step="0.01" name="price" id="price" class="form-control" 
                                                   value="<?php echo $course['price']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="course_duration" class="control-label">Course Duration</label>
                                            <input type="text" name="course_duration" id="course_duration" class="form-control" 
                                                   value="<?php echo htmlspecialchars($course['course_duration']); ?>" 
                                                   placeholder="e.g. 10h 30m">
                                        </div>
                                    </div>
                                </div>

                                <!-- Level and Language Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="level" class="control-label">Course Level</label>
                                            <select name="level" id="level" class="form-control">
                                                <option value="beginner" <?php echo ($course['level'] == 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                                                <option value="intermediate" <?php echo ($course['level'] == 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                                <option value="advanced" <?php echo ($course['level'] == 'advanced') ? 'selected' : ''; ?>>Advanced</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language" class="control-label">Language</label>
                                            <input type="text" name="language" id="language" class="form-control" 
                                                   value="<?php echo htmlspecialchars($course['language']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sort Order -->
                                <div class="form-group">
                                    <label for="sort_order" class="control-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control" 
                                           value="<?php echo $course['sort_order']; ?>" min="0">
                                    <p class="help-block">Lower numbers appear first in course listings</p>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-4">
                                <!-- Course Settings -->
                                <div class="panel panel-default course-settings-panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Course Settings</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="settings-group">
                                            <div class="setting-item">
                                                <label class="setting-label">
                                                    <input type="checkbox" name="is_free" value="1" 
                                                           <?php echo ($course['is_free']) ? 'checked' : ''; ?>> 
                                                    <span class="setting-title">Free Course</span>
                                                </label>
                                                <p class="setting-desc">Check if this course is free for all users</p>
                                            </div>

                                            <div class="setting-item">
                                                <label class="setting-label">
                                                    <input type="checkbox" name="is_public" value="1" 
                                                           <?php echo ($course['is_public']) ? 'checked' : ''; ?>> 
                                                    <span class="setting-title">Public Course</span>
                                                </label>
                                                <p class="setting-desc">Make this course visible to all users</p>
                                            </div>

                                            <div class="setting-item">
                                                <label class="setting-label">
                                                    <input type="checkbox" name="is_active" value="1" 
                                                           <?php echo ($course['is_active']) ? 'checked' : ''; ?>> 
                                                    <span class="setting-title">Active Course</span>
                                                </label>
                                                <p class="setting-desc">Enable this course for enrollment</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Actions -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">Course Actions</div>
                                    <div class="panel-body">
                                        <a href="<?php echo admin_url('lms/Lms_admin/manage_videos/' . $course['id']); ?>" 
                                           class="btn btn-success btn-block">
                                            <i class="fa fa-video-camera"></i> Manage Videos
                                        </a>
                                        <hr>
                                        <small class="text-muted">
                                            <strong>Created:</strong> <?php echo date('M d, Y H:i', strtotime($course['created_at'])); ?><br>
                                            <strong>Last Updated:</strong> <?php echo date('M d, Y H:i', strtotime($course['updated_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('lms/Lms_admin/courses'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Courses
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-check"></i> Update Course
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
/* Course Settings Panel Styling */
.course-settings-panel {
    border-color: #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.course-settings-panel .panel-heading {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
}

.course-settings-panel .panel-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.settings-group {
    padding: 0;
}

.setting-item {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.setting-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.setting-label {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    margin-bottom: 8px;
}

.setting-label input[type="checkbox"] {
    margin-right: 10px;
    margin-top: 2px;
    transform: scale(1.2);
    accent-color: #667eea;
}

.setting-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.setting-desc {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
    margin-left: 25px;
    line-height: 1.4;
}

/* Form Styling */
.form-group {
    margin-bottom: 20px;
}

.control-label {
    font-weight: 600;
    margin-bottom: 5px;
}

.help-block {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

/* Breadcrumb Styling */
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
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Wait for document to be ready
$(document).ready(function() {
    // Auto-check/uncheck price-related options
    $('#price').on('input', function() {
        const price = parseFloat($(this).val());
        const isFreeCheckbox = $('input[name="is_free"]');
        
        if (price > 0) {
            isFreeCheckbox.prop('checked', false);
        } else {
            isFreeCheckbox.prop('checked', true);
        }
    });

    $('input[name="is_free"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('#price').val('0.00');
        }
    });
});
</script>

<?php init_tail(); ?>