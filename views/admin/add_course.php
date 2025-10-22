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
                        
                        <?php echo form_open_multipart(admin_url('lms/Lms_admin/add_course')); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title" class="control-label">
                                        <span class="text-danger">*</span> Course Title
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control" required>
                                </div>

                                <!-- Category -->
                                <div class="form-group">
                                    <label for="category" class="control-label">Category</label>
                                    <input type="text" name="category" id="category" class="form-control">
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="control-label">
                                        <span class="text-danger">*</span> Description
                                    </label>
                                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                                </div>

                                <!-- Cover Image -->
                                <div class="form-group">
                                    <label for="cover_image" class="control-label">Cover Image</label>
                                    <input type="file" name="cover_image" id="cover_image" class="form-control" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <p class="help-block">Upload a cover image for the course (JPG, PNG, GIF). Recommended size: 400x300px</p>
                                    
                                    <!-- Image Preview -->
                                    <div id="image-preview" style="display: none; margin-top: 10px;">
                                        <img id="preview-img" src="" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                                        <br><small class="text-muted">Preview</small>
                                    </div>
                                </div>

                                <!-- Price and Duration Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price" class="control-label">Price (₹)</label>
                                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="course_duration" class="control-label">Course Duration</label>
                                            <input type="text" name="course_duration" id="course_duration" class="form-control" 
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
                                                <option value="beginner">Beginner</option>
                                                <option value="intermediate">Intermediate</option>
                                                <option value="advanced">Advanced</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language" class="control-label">Language</label>
                                            <input type="text" name="language" id="language" class="form-control" value="English">
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <!-- Right Column - Course Settings -->
                            <div class="col-md-4">
                                <div class="panel panel-default course-settings-panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Course Settings</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="settings-group">
                                            <div class="setting-item">
                                                <label class="setting-label">
                                                    <input type="checkbox" name="is_free" id="is_free" value="0" checked> 
                                                    <span class="setting-title">Free Course</span>
                                                </label>
                                                <p class="setting-desc">Check if this course is free for all users</p>
                                            </div>

                                            <div class="setting-item">
                                                <label class="setting-label">
                                                    <input type="checkbox" name="is_public" id="is_public" value="1" checked> 
                                                    <span class="setting-title">Public Course</span>
                                                </label>
                                                <p class="setting-desc">Make this course visible to all users</p>
                                            </div>

                                            <div class="setting-item">
                                                <label class="setting-label">
                                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked> 
                                                    <span class="setting-title">Active Course</span>
                                                </label>
                                                <p class="setting-desc">Enable this course for enrollment</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-check"></i> Save Course
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

/* Checkbox Styling */
input[type="checkbox"] {
    opacity: 1 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
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

/* Image Preview Styling */
#image-preview img {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        const isFreeCheckbox = $('#is_free');
        
        if (price > 0) {
            isFreeCheckbox.prop('checked', false);
        } else {
            isFreeCheckbox.prop('checked', true);
        }
    });

    $('#is_free').on('change', function() {
        if ($(this).is(':checked')) {
            $('#price').val('0.00');
        }
    });
});
</script>

<?php init_tail(); ?>