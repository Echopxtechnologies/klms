<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Course Detail Page -->
<div class="row">
    <!-- Main Course Content -->
    <div class="col-md-8">
        <!-- Course Hero Section -->
        <div class="course-hero panel_s">
            <div class="course-hero-content">
                <?php if (!empty($course['cover_image']) && file_exists($course['cover_image'])): ?>
                    <div class="course-hero-image">
                        <img src="<?php echo base_url($course['cover_image']); ?>" 
                             alt="<?php echo html_escape($course['title']); ?>" 
                             class="img-responsive">
                        <div class="course-overlay">
                            <div class="course-badge">
                                <i class="fa fa-tag"></i> <?php echo html_escape($course['category']); ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="course-placeholder-hero">
                        <i class="fa fa-graduation-cap fa-5x"></i>
                        <div class="course-badge">
                            <i class="fa fa-tag"></i> <?php echo html_escape($course['category']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Course Info Panel -->
        <div class="panel_s">
            <div class="panel-body">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="tw-mb-4">
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumb as $index => $crumb): ?>
                            <?php if ($index === count($breadcrumb) - 1): ?>
                                <li class="breadcrumb-item active"><?php echo html_escape($crumb['title']); ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item">
                                    <a href="<?php echo $crumb['url']; ?>">
                                        <?php if ($index === 0): ?><i class="fa fa-home"></i> <?php endif; ?>
                                        <?php echo html_escape($crumb['title']); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>

                <!-- Course Title & Meta -->
                <div class="course-header">
                    <h1 class="course-title"><?php echo html_escape($course['title']); ?></h1>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fa fa-calendar text-muted"></i>
                            <span>Created <?php echo date('M d, Y', strtotime($course['created_at'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fa fa-clock-o text-muted"></i>
                            <span><?php echo $total_duration; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fa fa-play-circle text-muted"></i>
                            <span><?php echo $video_count; ?> Videos</span>
                        </div>
                        <?php if (!empty($course['updated_at']) && $course['updated_at'] !== $course['created_at']): ?>
                        <div class="meta-item">
                            <i class="fa fa-refresh text-muted"></i>
                            <span>Updated <?php echo date('M d, Y', strtotime($course['updated_at'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Course Description -->
                <div class="course-description">
                    <h3>About this Course</h3>
                    <div class="lead"><?php echo nl2br(html_escape($course['description'])); ?></div>
                </div>

                <hr>

                <!-- Course Curriculum -->
                <div class="course-curriculum">
                    <div class="curriculum-header">
                        <h3>Course Content</h3>
                        <p class="text-muted">
                            <?php echo $video_count; ?> videos • Total duration: <?php echo $total_duration; ?>
                        </p>
                    </div>

                    <?php if (!empty($videos)): ?>
                        <div class="video-list">
                            <?php foreach ($videos as $index => $video): ?>
                                <div class="video-item">
                                    <div class="video-item-content">
                                        <div class="video-number">
                                            <span class="video-index"><?php echo ($index + 1); ?></span>
                                            <i class="fa fa-play-circle-o video-play-icon"></i>
                                        </div>
                                        
                                        <div class="video-info">
                                            <h5 class="video-title">
                                                <a href="<?php echo site_url('klms/lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>">
                                                    <?php echo html_escape($video['title']); ?>
                                                </a>
                                            </h5>
                                            <?php if (!empty($video['description'])): ?>
                                                <p class="video-description">
                                                    <?php echo html_escape(substr($video['description'], 0, 100)); ?>
                                                    <?php if (strlen($video['description']) > 100): ?>...<?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="video-actions">
                                            <?php if (!empty($video['duration'])): ?>
                                                <span class="video-duration">
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo html_escape($video['duration']); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <a href="<?php echo site_url('klms/lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fa fa-play"></i> Watch
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- View All Videos Button -->
                        <div class="text-center tw-mt-6">
                            <a href="<?php echo site_url('klms/lms_users/course_videos/' . $course['id']); ?>" 
                               class="btn btn-info btn-lg">
                                <i class="fa fa-list"></i> View All Videos
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="no-videos text-center">
                            <i class="fa fa-video-camera fa-3x text-muted"></i>
                            <h4>No videos available</h4>
                            <p class="text-muted">This course doesn't have any videos yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Course Actions Card -->
        <div class="panel_s sticky-sidebar">
            <div class="panel-body text-center">
                <h4 class="tw-mb-4">Start Learning</h4>
                
                <?php if (!empty($videos)): ?>
                    <a href="<?php echo site_url('klms/lms_users/watch_video/' . $course['id'] . '/' . $videos[0]['id']); ?>" 
                       class="btn btn-primary btn-lg btn-block tw-mb-3">
                        <i class="fa fa-play"></i> Start Course
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo site_url('klms/lms_users/course_videos/' . $course['id']); ?>" 
                   class="btn btn-default btn-block tw-mb-3">
                    <i class="fa fa-list"></i> View All Videos
                </a>
                
                <button class="btn btn-success btn-block" onclick="addToFavorites(<?php echo $course['id']; ?>)">
                    <i class="fa fa-heart-o"></i> Add to Favorites
                </button>

                <hr>
                
                <!-- Course Stats -->
                <div class="course-stats">
                    <div class="stat-row">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $video_count; ?></div>
                            <div class="stat-label">Videos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $total_duration; ?></div>
                            <div class="stat-label">Duration</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Courses -->
        <?php if (!empty($related_courses)): ?>
        <div class="panel_s">
            <div class="panel-body">
                <h4>Related Courses</h4>
                <div class="related-courses">
                    <?php foreach ($related_courses as $related): ?>
                        <div class="related-course-item">
                            <div class="related-course-image">
                                <?php if (!empty($related['cover_image']) && file_exists($related['cover_image'])): ?>
                                    <img src="<?php echo base_url($related['cover_image']); ?>" 
                                         alt="<?php echo html_escape($related['title']); ?>">
                                <?php else: ?>
                                    <div class="related-course-placeholder">
                                        <i class="fa fa-graduation-cap"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="related-course-info">
                                <h6>
                                    <a href="<?php echo site_url('klms/lms_users/view_course/' . $related['id']); ?>">
                                        <?php echo html_escape($related['title']); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <?php echo html_escape($related['category']); ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Course Hero Section */
.course-hero {
    margin-bottom: 30px;
    overflow: hidden;
    border-radius: 8px;
}

.course-hero-image {
    position: relative;
    height: 300px;
    overflow: hidden;
}

.course-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-placeholder-hero {
    height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
}

.course-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 20px;
}

.course-badge {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* Course Header */
.course-header {
    margin-bottom: 30px;
}

.course-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
}

.course-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 14px;
}

/* Course Description */
.course-description .lead {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #555;
}

/* Video List Styles */
.curriculum-header {
    margin-bottom: 25px;
}

.video-list {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.video-item {
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.3s ease;
}

.video-item:last-child {
    border-bottom: none;
}

.video-item:hover {
    background-color: #f8f9fa;
}

.video-item-content {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    gap: 15px;
}

.video-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}

.video-index {
    font-weight: 600;
    color: #666;
}

.video-play-icon {
    position: absolute;
    color: #007bff;
    opacity: 0;
    transition: opacity 0.3s ease;
    font-size: 1.2rem;
}

.video-item:hover .video-play-icon {
    opacity: 1;
}

.video-item:hover .video-index {
    opacity: 0;
}

.video-info {
    flex: 1;
    min-width: 0;
}

.video-title {
    margin: 0 0 5px 0;
    font-size: 1rem;
    font-weight: 600;
}

.video-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.video-title a:hover {
    color: #007bff;
}

.video-description {
    margin: 0;
    color: #666;
    font-size: 14px;
    line-height: 1.4;
}

.video-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-shrink: 0;
}

.video-duration {
    color: #666;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Sidebar Styles */
.sticky-sidebar {
    position: sticky;
    top: 20px;
}

.course-stats {
    margin-top: 20px;
}

.stat-row {
    display: flex;
    justify-content: space-around;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #007bff;
}

.stat-label {
    font-size: 13px;
    color: #666;
    margin-top: 2px;
}

/* Related Courses */
.related-courses {
    max-height: 300px;
    overflow-y: auto;
}

.related-course-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.related-course-item:last-child {
    border-bottom: none;
}

.related-course-image {
    width: 50px;
    height: 35px;
    border-radius: 4px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.related-course-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-course-placeholder {
    color: #666;
    font-size: 14px;
}

.related-course-info h6 {
    margin: 0 0 3px 0;
    font-size: 14px;
    line-height: 1.3;
}

.related-course-info a {
    color: #333;
    text-decoration: none;
}

.related-course-info a:hover {
    color: #007bff;
}

/* No Videos State */
.no-videos {
    padding: 40px 20px;
}

.no-videos i {
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .course-title {
        font-size: 2rem;
    }
    
    .course-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .video-item-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .video-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .sticky-sidebar {
        position: static;
    }
}

@media (max-width: 576px) {
    .course-hero-image,
    .course-placeholder-hero {
        height: 200px;
    }
    
    .video-item-content {
        padding: 12px 15px;
    }
    
    .stat-row {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<!-- JavaScript -->
<script>
function addToFavorites(courseId) {
    // Add to favorites functionality - you can implement this with AJAX
    // For now, just show a message
    alert('Course added to favorites! (Feature to be implemented)');
    
    // Example AJAX implementation:
    /*
    $.ajax({
        url: '<?php echo site_url('lms_users/ajax_add_to_favorites'); ?>',
        type: 'POST',
        data: {
            course_id: courseId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            if (response.success) {
                alert('Course added to favorites!');
                // Update UI
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while adding to favorites.');
        }
    });
    */
}

// Smooth scrolling for anchor links
$(document).ready(function() {
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Initialize tooltips if needed
    $('[data-toggle="tooltip"]').tooltip();
});
</script>