<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Video Player Page -->
<div class="row">
    <!-- Main Video Player -->
    <div class="col-md-9">
        <div class="video-player-container">
            <!-- Video Player -->
            <div class="panel_s">
                <div class="video-player-wrapper">
                    <?php if (!empty($video['vimeo_url'])): ?>
                        <?php 
                        // Extract Vimeo ID
                        preg_match('/vimeo\.com\/(\d+)/', $video['vimeo_url'], $matches);
                        $vimeo_id = isset($matches[1]) ? $matches[1] : null;
                        ?>
                        
                        <?php if ($vimeo_id): ?>
                            <div class="video-embed-container">
                                <iframe src="https://player.vimeo.com/video/<?php echo $vimeo_id; ?>?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" 
                                        frameborder="0" 
                                        allow="autoplay; fullscreen; picture-in-picture" 
                                        allowfullscreen 
                                        title="<?php echo html_escape($video['title']); ?>">
                                </iframe>
                            </div>
                        <?php else: ?>
                            <div class="video-error">
                                <i class="fa fa-exclamation-triangle fa-3x text-warning"></i>
                                <h4>Video Not Available</h4>
                                <p>There was an issue loading this video. Please try again later.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="video-placeholder">
                            <i class="fa fa-video-camera fa-5x text-muted"></i>
                            <h4>No Video Available</h4>
                            <p>This video hasn't been configured yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Video Info Panel -->
            <div class="panel_s">
                <div class="panel-body">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="tw-mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?php echo site_url($module_base_url); ?>">
                                    <i class="fa fa-home"></i> Courses
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo site_url($module_base_url . '/view_course/' . $course['id']); ?>">
                                    <?php echo html_escape($course['title']); ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo site_url($module_base_url . '/course_videos/' . $course['id']); ?>">
                                    Videos
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                <?php echo html_escape($video['title']); ?>
                            </li>
                        </ol>
                    </nav>
                    
                    <!-- Video Header -->
                    <div class="video-header">
                        <div class="video-progress-info">
                            <span class="progress-text">
                                Video <?php echo $current_index; ?> of <?php echo $total_videos; ?>
                            </span>
                            <div class="progress">
                                <div class="progress-bar progress-bar-primary" 
                                     style="width: <?php echo ($current_index / $total_videos) * 100; ?>%">
                                </div>
                            </div>
                        </div>
                        
                        <h2 class="video-title"><?php echo html_escape($video['title']); ?></h2>
                        
                        <div class="video-meta">
                            <div class="meta-item">
                                <i class="fa fa-calendar text-muted"></i>
                                <span>Added <?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                            </div>
                            <?php if (!empty($video['duration'])): ?>
                                <div class="meta-item">
                                    <i class="fa fa-clock-o text-muted"></i>
                                    <span><?php echo html_escape($video['duration']); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($video['sort_order'])): ?>
                                <div class="meta-item">
                                    <i class="fa fa-sort-numeric-asc text-muted"></i>
                                    <span>Lesson <?php echo $video['sort_order']; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Course Context Info -->
                        <div class="course-context-info">
                            <div class="context-item">
                                <i class="fa fa-signal text-info"></i>
                                <span>Course Level: <strong><?php echo html_escape($course['level']); ?></strong></span>
                            </div>
                            <div class="context-item">
                                <i class="fa fa-globe text-success"></i>
                                <span>Language: <strong><?php echo html_escape($course['language']); ?></strong></span>
                            </div>
                            <div class="context-item">
                                <i class="fa fa-tag text-primary"></i>
                                <span>Category: <strong><?php echo html_escape($course['category']); ?></strong></span>
                            </div>
                            <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                <div class="context-item">
                                    <i class="fa fa-gift text-success"></i>
                                    <span><strong class="text-success">Free Course</strong></span>
                                </div>
                            <?php else: ?>
                                <div class="context-item">
                                    <i class="fa fa-money text-warning"></i>
                                    <span>Course Price: <strong class="text-warning">$<?php echo number_format($course['price'], 2); ?></strong></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($course['course_duration'])): ?>
                                <div class="context-item">
                                    <i class="fa fa-clock-o text-info"></i>
                                    <span>Total Duration: <strong><?php echo html_escape($course['course_duration']); ?></strong></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Video Description -->
                    <?php if (!empty($video['description'])): ?>
                        <div class="video-description">
                            <h4>About this Video</h4>
                            <p><?php echo nl2br(html_escape($video['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Video Navigation -->
                    <div class="video-navigation">
                        <div class="nav-buttons">
                            <?php if ($previous_video): ?>
                                <a href="<?php echo site_url($module_base_url . '/watch_video/' . $course['id'] . '/' . $previous_video['id']); ?>" 
                                   class="btn btn-default btn-lg">
                                    <i class="fa fa-chevron-left"></i> Previous Video
                                    <small class="nav-video-title"><?php echo html_escape($previous_video['title']); ?></small>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($next_video): ?>
                                <a href="<?php echo site_url($module_base_url . '/watch_video/' . $course['id'] . '/' . $next_video['id']); ?>" 
                                   class="btn btn-primary btn-lg">
                                    Next Video <i class="fa fa-chevron-right"></i>
                                    <small class="nav-video-title"><?php echo html_escape($next_video['title']); ?></small>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar - Course Videos List -->
    <div class="col-md-3">
        <div class="course-sidebar">
            <!-- Course Info -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5 class="course-sidebar-title">
                        <i class="fa fa-graduation-cap text-primary"></i>
                        <?php echo html_escape($course['title']); ?>
                    </h5>
                    <p class="text-muted course-category">
                        <i class="fa fa-tag"></i> <?php echo html_escape($course['category']); ?>
                    </p>

                    <!-- Course Details in Sidebar -->
                    <div class="course-sidebar-details">
                        <div class="detail-row">
                            <i class="fa fa-signal text-info"></i>
                            <span><?php echo html_escape($course['level']); ?> Level</span>
                        </div>
                        <div class="detail-row">
                            <i class="fa fa-globe text-success"></i>
                            <span><?php echo html_escape($course['language']); ?></span>
                        </div>
                        <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                            <div class="detail-row">
                                <i class="fa fa-gift text-success"></i>
                                <span class="text-success font-weight-bold">FREE</span>
                            </div>
                        <?php else: ?>
                            <div class="detail-row">
                                <i class="fa fa-money text-warning"></i>
                                <span class="text-warning font-weight-bold">$<?php echo number_format($course['price'], 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($course['course_duration'])): ?>
                            <div class="detail-row">
                                <i class="fa fa-clock-o text-info"></i>
                                <span><?php echo html_escape($course['course_duration']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-progress">
                        <div class="progress-stats">
                            <span class="progress-text">Progress: <?php echo $current_index; ?>/<?php echo $total_videos; ?></span>
                            <span class="progress-percent"><?php echo round(($current_index / $total_videos) * 100); ?>%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar progress-bar-success" 
                                 style="width: <?php echo ($current_index / $total_videos) * 100; ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <div class="course-actions">
                        <a href="<?php echo site_url($module_base_url . '/view_course/' . $course['id']); ?>" 
                           class="btn btn-default btn-sm btn-block">
                            <i class="fa fa-arrow-left"></i> Back to Course
                        </a>
                        <a href="<?php echo site_url($module_base_url . '/course_videos/' . $course['id']); ?>" 
                           class="btn btn-info btn-sm btn-block">
                            <i class="fa fa-th"></i> All Videos
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Videos Playlist -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5>
                        <i class="fa fa-list-ol"></i> Course Videos
                        <small class="text-muted">(<?php echo $total_videos; ?>)</small>
                    </h5>
                    
                    <div class="videos-playlist">
                        <?php foreach ($all_videos as $index => $playlist_video): ?>
                            <div class="playlist-item <?php echo ($playlist_video['id'] == $video['id']) ? 'active' : ''; ?>">
                                <a href="<?php echo site_url($module_base_url . '/watch_video/' . $course['id'] . '/' . $playlist_video['id']); ?>"
                                   class="playlist-link">
                                    <div class="playlist-number">
                                        <?php if ($playlist_video['id'] == $video['id']): ?>
                                            <i class="fa fa-play text-primary"></i>
                                        <?php else: ?>
                                            <?php echo ($index + 1); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="playlist-info">
                                        <div class="playlist-title">
                                            <?php echo html_escape($playlist_video['title']); ?>
                                        </div>
                                        <?php if (!empty($playlist_video['duration'])): ?>
                                            <div class="playlist-duration">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo html_escape($playlist_video['duration']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($index < $current_index - 1): ?>
                                        <div class="playlist-status">
                                            <i class="fa fa-check-circle text-success" title="Completed"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Course Summary Card -->
            <div class="panel_s">
                <div class="panel-body">
                    <h6><i class="fa fa-info-circle"></i> Course Summary</h6>
                    
                    <div class="course-summary">
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="summary-value"><?php echo $total_videos; ?></div>
                                <div class="summary-label">Videos</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">
                                    <?php echo !empty($course['course_duration']) ? html_escape($course['course_duration']) : 'N/A'; ?>
                                </div>
                                <div class="summary-label">Duration</div>
                            </div>
                        </div>
                        
                        <div class="summary-badges">
                            <span class="badge badge-<?php echo ($course['level'] == 'Beginner') ? 'success' : (($course['level'] == 'Intermediate') ? 'warning' : 'danger'); ?>">
                                <?php echo html_escape($course['level']); ?>
                            </span>
                            <span class="badge badge-info">
                                <?php echo html_escape($course['language']); ?>
                            </span>
                            <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                <span class="badge badge-success">FREE</span>
                            <?php else: ?>
                                <span class="badge badge-warning">PAID</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="panel_s">
                <div class="panel-body">
                    <h6>Quick Actions</h6>
                    
                    <div class="quick-actions">
                        <?php if (!empty($video['vimeo_url'])): ?>
                            <a href="<?php echo $video['vimeo_url']; ?>" 
                               target="_blank" 
                               class="btn btn-default btn-sm btn-block">
                                <i class="fa fa-external-link"></i> Watch on Vimeo
                            </a>
                        <?php endif; ?>
                        
                        <button class="btn btn-success btn-sm btn-block" onclick="markAsCompleted()">
                            <i class="fa fa-check"></i> Mark as Completed
                        </button>
                        
                        <button class="btn btn-warning btn-sm btn-block" onclick="addToBookmarks()">
                            <i class="fa fa-bookmark"></i> Bookmark
                        </button>
                        
                        <button class="btn btn-info btn-sm btn-block" onclick="shareVideo()">
                            <i class="fa fa-share"></i> Share Video
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Video Player Container */
.video-player-container {
    margin-bottom: 30px;
}

.video-player-wrapper {
    background: #000;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
}

.video-embed-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.video-embed-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.video-error,
.video-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    background: #f8f9fa;
    color: #666;
    text-align: center;
}

.video-error i,
.video-placeholder i {
    margin-bottom: 20px;
}

/* Video Info */
.video-progress-info {
    margin-bottom: 20px;
}

.progress-text {
    font-size: 14px;
    color: #666;
    display: block;
    margin-bottom: 5px;
}

.progress {
    height: 6px;
    margin-bottom: 0;
}

.video-title {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.8rem;
    font-weight: 600;
    line-height: 1.3;
}

.video-meta {
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

/* Course Context Info */
.course-context-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.context-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.context-item i {
    font-size: 14px;
}

.video-description {
    margin-bottom: 30px;
}

.video-description h4 {
    color: #333;
    margin-bottom: 15px;
}

.video-description p {
    line-height: 1.6;
    color: #555;
}

/* Video Navigation */
.video-navigation {
    border-top: 1px solid #e9ecef;
    padding-top: 25px;
}

.nav-buttons {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.nav-buttons .btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    text-align: center;
    min-height: 80px;
    justify-content: center;
}

.nav-video-title {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    opacity: 0.8;
    font-weight: normal;
    line-height: 1.2;
}

/* Sidebar Styles */
.course-sidebar {
    position: sticky;
    top: 20px;
}

.course-sidebar-title {
    color: #333;
    margin-bottom: 5px;
}

.course-category {
    margin-bottom: 15px;
    font-size: 13px;
}

/* Course Sidebar Details */
.course-sidebar-details {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 13px;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.detail-row i {
    width: 16px;
    text-align: center;
}

.course-progress {
    margin-bottom: 20px;
}

.progress-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 13px;
}

.progress-percent {
    font-weight: 600;
    color: #28a745;
}

.progress-sm {
    height: 8px;
}

.course-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Videos Playlist */
.videos-playlist {
    max-height: 400px;
    overflow-y: auto;
}

.playlist-item {
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease;
}

.playlist-item:last-child {
    border-bottom: none;
}

.playlist-item:hover {
    background-color: #f8f9fa;
}

.playlist-item.active {
    background-color: #e3f2fd;
}

.playlist-link {
    display: flex;
    align-items: center;
    padding: 12px 0;
    text-decoration: none;
    color: inherit;
    gap: 12px;
}

.playlist-link:hover {
    text-decoration: none;
    color: inherit;
}

.playlist-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    background: #f8f9fa;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 600;
    flex-shrink: 0;
    color: #666;
}

.playlist-item.active .playlist-number {
    background: #007bff;
    color: white;
}

.playlist-info {
    flex: 1;
    min-width: 0;
}

.playlist-title {
    font-size: 14px;
    font-weight: 500;
    line-height: 1.3;
    margin-bottom: 3px;
    color: #333;
}

.playlist-item.active .playlist-title {
    color: #007bff;
    font-weight: 600;
}

.playlist-duration {
    font-size: 12px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 4px;
}

.playlist-status {
    flex-shrink: 0;
}

/* Course Summary */
.course-summary {
    text-align: center;
}

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.summary-item {
    text-align: center;
}

.summary-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 3px;
}

.summary-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    justify-content: center;
}

.summary-badges .badge {
    font-size: 10px;
    padding: 4px 8px;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .row {
        flex-direction: column-reverse;
    }
    
    .col-md-3 {
        margin-bottom: 30px;
    }
    
    .course-sidebar {
        position: static;
    }
    
    .video-title {
        font-size: 1.5rem;
    }
    
    .video-meta {
        flex-direction: column;
        gap: 10px;
    }

    .course-context-info {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 15px;
    }
    
    .nav-buttons {
        flex-direction: column;
    }
    
    .nav-buttons .btn {
        min-height: 60px;
        flex-direction: row;
        text-align: left;
    }
    
    .nav-video-title {
        margin-top: 0;
        margin-left: 10px;
    }
    
    .videos-playlist {
        max-height: 300px;
    }
    
    .course-actions {
        flex-direction: row;
    }
    
    .quick-actions {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    .quick-actions .btn {
        flex: 1;
        min-width: 0;
    }

    .summary-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}

@media (max-width: 576px) {
    .video-embed-container {
        padding-bottom: 75%; /* More square on mobile */
    }
    
    .video-error,
    .video-placeholder {
        height: 250px;
    }
    
    .course-actions {
        flex-direction: column;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .playlist-link {
        padding: 10px 0;
    }
    
    .playlist-number {
        width: 25px;
        height: 25px;
        font-size: 12px;
    }
    
    .playlist-title {
        font-size: 13px;
    }

    .course-context-info {
        padding: 12px;
    }

    .context-item {
        font-size: 12px;
    }
}
</style>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Auto-play next video when current ends (optional)
    // You can implement this with Vimeo Player API if needed
    
    // Keyboard navigation
    $(document).keydown(function(e) {
        <?php if ($previous_video): ?>
        if (e.key === 'ArrowLeft') {
            window.location.href = '<?php echo site_url($module_base_url . '/watch_video/' . $course['id'] . '/' . $previous_video['id']); ?>';
        }
        <?php endif; ?>
        
        <?php if ($next_video): ?>
        if (e.key === 'ArrowRight') {
            window.location.href = '<?php echo site_url($module_base_url . '/watch_video/' . $course['id'] . '/' . $next_video['id']); ?>';
        }
        <?php endif; ?>
    });
    
    // Smooth scroll to top when navigating
    $('html, body').animate({scrollTop: 0}, 300);
});

// Quick action functions
function markAsCompleted() {
    // Implement mark as completed functionality
    alert('Video marked as completed! (Feature to be implemented)');
}

function addToBookmarks() {
    // Implement bookmark functionality
    alert('Video bookmarked! (Feature to be implemented)');
}

function shareVideo() {
    // Implement share functionality
    if (navigator.share) {
        navigator.share({
            title: '<?php echo html_escape($video['title']); ?>',
            text: 'Check out this video from <?php echo html_escape($course['title']); ?>',
            url: window.location.href
        });
    } else {
        // Fallback for browsers without Web Share API
        const url = window.location.href;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Video link copied to clipboard!');
            });
        } else {
            prompt('Copy this link to share:', url);
        }
    }
}
</script>