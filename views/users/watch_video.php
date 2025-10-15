<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Video Player Page -->
<div class="row video-watch-container">
    <div class="col-md-9">
        <div class="video-player-section">
            <!-- Video Player Panel -->
            <div class="panel_s video-player-panel">
                <div class="video-player-wrapper">
                    <?php if (!empty($video['vimeo_url'])): ?>
                        <?php 
                        // Enhanced Vimeo URL parsing with validation
                        $vimeo_id = null;
                        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $video['vimeo_url'], $matches)) {
                            $vimeo_id = $matches[1];
                        }
                        ?>
                        
                        <?php if ($vimeo_id && ctype_digit($vimeo_id)): ?>
                            <div class="video-embed-container">
                                <iframe 
                                    id="vimeo-player"
                                    src="https://player.vimeo.com/video/<?php echo html_escape($vimeo_id); ?>?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479&amp;quality=auto" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write" 
                                    allowfullscreen 
                                    title="<?php echo html_escape($video['title']); ?>"
                                    loading="lazy">
                                </iframe>
                            </div>
                            <script src="https://player.vimeo.com/api/player.js"></script>
                        <?php else: ?>
                            <div class="video-error">
                                <i class="fa fa-exclamation-triangle fa-3x text-warning"></i>
                                <h4>Video Not Available</h4>
                                <p>Unable to load video. Please check the video URL or contact support.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="video-placeholder">
                            <i class="fa fa-video-camera fa-5x text-muted"></i>
                            <h4>No Video Available</h4>
                            <p>This lesson does not have a video configured yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Video Info Panel -->
            <div class="panel_s video-info-panel">
                <div class="panel-body">
                    <h1 class="video-title"><?php echo html_escape($video['title']); ?></h1>
                    
                    <div class="video-meta-bar">
                        <div class="meta-item">
                            <i class="fa fa-calendar"></i>
                            <span><?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                        </div>
                        <?php if (!empty($video['duration'])): ?>
                            <div class="meta-item">
                                <i class="fa fa-clock-o"></i>
                                <span><?php echo html_escape($video['duration']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($video['sort_order'])): ?>
                            <div class="meta-item">
                                <i class="fa fa-list-ol"></i>
                                <span>Lesson <?php echo (int)$video['sort_order']; ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="meta-item">
                            <i class="fa fa-eye"></i>
                            <span><?php echo (int)$current_index; ?> of <?php echo (int)$total_videos; ?></span>
                        </div>
                    </div>

                    <?php if (!empty($video['description'])): ?>
                        <div class="video-description">
                            <h4>Description</h4>
                            <div class="description-content">
                                <?php echo nl2br(html_escape($video['description'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Video Navigation -->
                    <div class="video-navigation">
                        <div class="nav-buttons-wrapper">
                            <?php if ($previous_video): ?>
                                <a href="<?php echo site_url($module_base_url . '/watch_video/' . (int)$course['id'] . '/' . (int)$previous_video['id']); ?>" 
                                   class="btn btn-default btn-nav btn-previous"
                                   title="<?php echo html_escape($previous_video['title']); ?>">
                                    <i class="fa fa-chevron-left"></i>
                                    <span class="nav-text">
                                        <small>Previous</small>
                                        <strong><?php echo html_escape($previous_video['title']); ?></strong>
                                    </span>
                                </a>
                            <?php else: ?>
                                <div class="btn btn-default btn-nav btn-disabled">
                                    <i class="fa fa-chevron-left"></i>
                                    <span class="nav-text">
                                        <small>No Previous Video</small>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($next_video): ?>
                                <a href="<?php echo site_url($module_base_url . '/watch_video/' . (int)$course['id'] . '/' . (int)$next_video['id']); ?>" 
                                   class="btn btn-primary btn-nav btn-next"
                                   id="next-video-btn"
                                   title="<?php echo html_escape($next_video['title']); ?>">
                                    <span class="nav-text">
                                        <small>Next</small>
                                        <strong><?php echo html_escape($next_video['title']); ?></strong>
                                    </span>
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <div class="btn btn-success btn-nav btn-disabled">
                                    <i class="fa fa-trophy"></i>
                                    <span class="nav-text">
                                        <small>Course Complete!</small>
                                        <strong>You've finished all videos</strong>
                                    </span>
                                </div>
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
            <!-- Course Info Card -->
            <div class="panel_s course-info-card">
                <div class="panel-body">
                    <div class="course-header">
                        <i class="fa fa-graduation-cap"></i>
                        <h5><?php echo html_escape($course['title']); ?></h5>
                    </div>
                    
                    <div class="course-category-badge">
                        <i class="fa fa-tag"></i>
                        <?php echo html_escape($course['category']); ?>
                    </div>
                    
                    <div class="enrollment-status">
                        <div class="status-badge status-active">
                            <i class="fa fa-check-circle"></i> Enrolled & Active
                        </div>
                        <small class="text-muted">
                            Access granted
                        </small>
                    </div>

                    <!-- Course Progress with Real-time Updates -->
                    <div class="course-progress-section">
                        <div class="progress-header">
                            <span class="progress-label">Your Progress</span>
                            <span class="progress-percentage" id="progress-percentage">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success" 
                                 id="course-progress-bar"
                                 role="progressbar"
                                 aria-valuenow="0"
                                 aria-valuemin="0" 
                                 aria-valuemax="100"
                                 style="width: 0%">
                            </div>
                        </div>
                        <div class="progress-stats">
                            <span id="progress-stats">Loading progress...</span>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="course-quick-actions">
                        <a href="<?php echo site_url($module_base_url . '/course_videos/' . (int)$course['id']); ?>" 
                           class="btn btn-default btn-block btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Course
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Videos Playlist -->
            <div class="panel_s playlist-panel">
                <div class="panel-body">
                    <div class="playlist-header">
                        <h5>
                            <i class="fa fa-play-circle"></i> Course Content
                        </h5>
                        <span class="playlist-count"><?php echo (int)$total_videos; ?> videos</span>
                    </div>
                    
                    <div class="videos-playlist" id="videosPlaylist">
                        <?php foreach ($videos as $index => $playlist_video): ?>
                            <div class="playlist-item <?php echo ((int)$playlist_video['id'] === (int)$video['id']) ? 'active' : ''; ?>" 
                                 data-video-id="<?php echo (int)$playlist_video['id']; ?>">
                                <a href="<?php echo site_url($module_base_url . '/watch_video/' . (int)$course['id'] . '/' . (int)$playlist_video['id']); ?>"
                                   class="playlist-link">
                                    <div class="playlist-number">
                                        <?php if ((int)$playlist_video['id'] === (int)$video['id']): ?>
                                            <i class="fa fa-play"></i>
                                        <?php else: ?>
                                            <span><?php echo ($index + 1); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="playlist-content">
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
                                    
                                    <div class="playlist-status" data-video-id="<?php echo (int)$playlist_video['id']; ?>">
                                        <!-- Will be updated by JavaScript -->
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Base Container */
.video-watch-container {
    margin-top: 20px;
}

/* Video Player Section */
.video-player-section {
    margin-bottom: 30px;
}

.video-player-panel {
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.video-player-wrapper {
    background: #000;
    position: relative;
}

.video-embed-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    overflow: hidden;
}

.video-embed-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

.enrollment-status {
    margin: 15px 0;
    padding: 12px;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    text-align: center;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #155724;
    margin-bottom: 5px;
}

.status-badge i {
    font-size: 16px;
}

.video-error,
.video-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    color: #666;
    text-align: center;
    padding: 40px 20px;
}

.video-error i,
.video-placeholder i {
    margin-bottom: 20px;
    opacity: 0.7;
}

.video-error h4,
.video-placeholder h4 {
    margin-bottom: 10px;
    color: #333;
}

/* Video Info Panel */
.video-info-panel {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.video-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 20px 0;
    line-height: 1.4;
}

.video-meta-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 15px 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 25px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 14px;
}

.meta-item i {
    color: #84c5fe;
    font-size: 16px;
}

/* Video Description */
.video-description {
    margin-bottom: 30px;
}

.video-description h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
}

.description-content {
    line-height: 1.8;
    color: #555;
    font-size: 15px;
}

/* Video Navigation */
.video-navigation {
    padding-top: 25px;
    border-top: 2px solid #e9ecef;
}

.nav-buttons-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.btn-nav {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    min-height: 80px;
    text-align: left;
    transition: all 0.3s ease;
    border-radius: 8px;
    text-decoration: none;
}

.btn-nav:hover:not(.btn-disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-previous {
    justify-content: flex-start;
}

.btn-next {
    justify-content: flex-end;
    text-align: right;
}

.btn-nav .nav-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.btn-nav small {
    font-size: 11px;
    text-transform: uppercase;
    opacity: 0.7;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.btn-nav strong {
    font-size: 13px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.btn-nav i {
    font-size: 18px;
    flex-shrink: 0;
}

.btn-nav.btn-disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Sidebar Styles */
.course-sidebar {
    position: sticky;
    top: 80px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
}

/* Course Info Card */
.course-info-card {
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.course-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 15px;
}

.course-header i {
    font-size: 24px;
    color: #84c5fe;
    margin-top: 3px;
}

.course-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.4;
}

.course-category-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Course Progress */
.course-progress-section {
    padding: 20px 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 20px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.progress-label {
    font-size: 13px;
    font-weight: 600;
    color: #6c757d;
}

.progress-percentage {
    font-size: 16px;
    font-weight: 700;
    color: #28a745;
}

.progress {
    height: 8px;
    margin-bottom: 8px;
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.progress-stats {
    font-size: 12px;
    color: #6c757d;
    text-align: center;
}

/* Quick Actions */
.course-quick-actions {
    margin-top: 15px;
}

/* Playlist Panel */
.playlist-panel {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.playlist-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.playlist-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.playlist-count {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    background: #f8f9fa;
    padding: 4px 10px;
    border-radius: 12px;
}

/* Videos Playlist */
.videos-playlist {
    max-height: 500px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.videos-playlist::-webkit-scrollbar {
    width: 6px;
}

.videos-playlist::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 10px;
}

.videos-playlist::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.videos-playlist::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.playlist-item {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.playlist-item:last-child {
    border-bottom: none;
}

.playlist-item:hover {
    background-color: #f8f9fa;
}

.playlist-item.active {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.playlist-link {
    display: flex;
    align-items: center;
    padding: 12px 8px;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.playlist-link:hover {
    text-decoration: none;
    color: inherit;
}

.playlist-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #e9ecef;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
    color: #495057;
    transition: all 0.2s ease;
}

.playlist-item.active .playlist-number {
    background: #2196f3;
    color: white;
}

.playlist-item.active .playlist-number i {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.playlist-content {
    flex: 1;
    min-width: 0;
}

.playlist-title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    color: #2c3e50;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.playlist-item.active .playlist-title {
    color: #2196f3;
}

.playlist-duration {
    font-size: 11px;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 4px;
}

.playlist-status {
    flex-shrink: 0;
    font-size: 16px;
}

/* Responsive Design */
@media (max-width: 991px) {
    .course-sidebar {
        position: static;
        max-height: none;
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .video-title {
        font-size: 1.5rem;
    }
    
    .video-meta-bar {
        gap: 15px;
    }
    
    .nav-buttons-wrapper {
        grid-template-columns: 1fr;
    }
    
    .btn-nav {
        min-height: 70px;
    }
    
    .btn-next {
        text-align: left;
    }
    
    .videos-playlist {
        max-height: 400px;
    }
}

@media (max-width: 576px) {
    .video-embed-container {
        padding-bottom: 75%;
    }
    
    .video-error,
    .video-placeholder {
        min-height: 250px;
    }
    
    .btn-nav {
        padding: 12px 15px;
        min-height: 60px;
    }
    
    .btn-nav i {
        font-size: 16px;
    }
    
    .playlist-number {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
</style>

<!-- Enhanced JavaScript with Video Progress Tracking -->
<script>
(function() {
    'use strict';
    
    // Global variables
    var vimeoPlayer = null;
    var progressMarked = false;
    var progressCheckInterval = null;
    var currentVideoId = <?php echo (int)$video['id']; ?>;
    var currentCourseId = <?php echo (int)$course['id']; ?>;
    var videoDuration = 0;
    var lastSavedTime = 0;
    
    $(document).ready(function() {
        // Initialize components
        initializeVideoPlayer();
        initializeKeyboardNavigation();
        scrollToActiveVideo();
        loadCourseProgress();
        
        // Save progress before leaving page
        $(window).on('beforeunload', function() {
            if (vimeoPlayer) {
                saveVideoProgress(true);
            }
        });
    });
    
    /**
     * Initialize Vimeo player with progress tracking
     */
    function initializeVideoPlayer() {
        const iframe = document.getElementById('vimeo-player');
        if (!iframe || typeof Vimeo === 'undefined') {
            console.log('Vimeo player not available');
            return;
        }
        
        try {
            vimeoPlayer = new Vimeo.Player(iframe);
            
            // Get video duration
            vimeoPlayer.getDuration().then(function(duration) {
                videoDuration = Math.floor(duration);
                console.log('Video duration:', videoDuration, 'seconds');
            });
            
            // Track every 10 seconds
            progressCheckInterval = setInterval(function() {
                saveVideoProgress(false);
            }, 10000);
            
            // Track when video ends
            vimeoPlayer.on('ended', function() {
                console.log('Video ended');
                clearInterval(progressCheckInterval);
                saveVideoProgress(true);
                
                // Mark as completed
                if (!progressMarked) {
                    progressMarked = true;
                    markVideoAsCompleted();
                }
                
                <?php if ($next_video): ?>
                // Show next video notification
                showNextVideoNotification();
                <?php endif; ?>
            });
            
            // Track when user watches 90%
            vimeoPlayer.on('timeupdate', function(data) {
                if (data.percent >= 0.9 && !progressMarked) {
                    progressMarked = true;
                    markVideoAsCompleted();
                }
            });
            
            // Track when video plays
            vimeoPlayer.on('play', function() {
                console.log('Video playing');
            });
            
            // Track when video pauses
            vimeoPlayer.on('pause', function() {
                saveVideoProgress(false);
            });
            
        } catch (error) {
            console.error('Vimeo player error:', error);
        }
    }
    
    /**
     * Save video progress
     */
    function saveVideoProgress(isSync) {
        if (!vimeoPlayer) return;
        
        vimeoPlayer.getCurrentTime().then(function(currentTime) {
            var watchTime = Math.floor(currentTime);
            
            // Don't save if same as last saved time
            if (watchTime === lastSavedTime && !isSync) {
                return;
            }
            
            lastSavedTime = watchTime;
            
            $.ajax({
                url: '<?php echo site_url($module_base_url . "/mark_video_watched"); ?>',
                type: 'POST',
                async: !isSync,
                data: {
                    course_id: currentCourseId,
                    video_id: currentVideoId,
                    watch_time: watchTime,
                    total_duration: videoDuration,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                success: function(response) {
                    console.log('Progress saved:', watchTime, '/', videoDuration, 'seconds');
                    
                    if (response.success && response.course_progress) {
                        updateCourseProgress(response.course_progress);
                    }
                },
                error: function() {
                    console.error('Failed to save progress');
                }
            });
        });
    }
    
    /**
     * Mark video as completed (90%+ watched)
     */
    function markVideoAsCompleted() {
        console.log('Marking video as completed');
        
        // Visual feedback
        var $currentPlaylistItem = $('.playlist-item[data-video-id="' + currentVideoId + '"]');
        $currentPlaylistItem.find('.playlist-status').html('<i class="fa fa-check-circle text-success"></i>');
        
        // Force save with completion flag
        saveVideoProgress(true);
    }
    
    /**
     * Load and update course progress
     */
    function loadCourseProgress() {
        $.ajax({
            url: '<?php echo site_url($module_base_url . "/get_course_progress/" . (int)$course["id"]); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.progress) {
                    updateCourseProgress(response.progress);
                    updatePlaylistStatus(response.video_progress);
                }
            }
        });
    }
    
    /**
     * Update course progress display
     */
    function updateCourseProgress(progress) {
        var percentage = parseFloat(progress.progress_percentage) || 0;
        var completed = parseInt(progress.completed_videos) || 0;
        var total = parseInt(progress.total_videos) || <?php echo (int)$total_videos; ?>;
        
        $('#course-progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
        $('#progress-percentage').text(Math.round(percentage) + '%');
        $('#progress-stats').text(completed + ' of ' + total + ' videos completed');
    }
    
    /**
     * Update playlist video status icons
     */
    function updatePlaylistStatus(videoProgress) {
        if (!videoProgress || !Array.isArray(videoProgress)) return;
        
        videoProgress.forEach(function(progress) {
            var $item = $('.playlist-status[data-video-id="' + progress.video_id + '"]');
            if (progress.completed == 1) {
                $item.html('<i class="fa fa-check-circle text-success"></i>');
            }
        });
    }
    
    /**
     * Show next video notification
     */
    function showNextVideoNotification() {
        <?php if ($next_video): ?>
        var $nextBtn = $('#next-video-btn');
        $nextBtn.addClass('btn-success').removeClass('btn-primary');
        $nextBtn.find('small').text('Up Next');
        
        // Optional: Auto-redirect after 3 seconds
        // setTimeout(function() {
        //     window.location.href = $nextBtn.attr('href');
        // }, 3000);
        <?php endif; ?>
    }
    
    /**
     * Keyboard navigation
     */
    function initializeKeyboardNavigation() {
        $(document).on('keydown', function(e) {
            if ($(e.target).is('input, textarea')) return;
            
            <?php if ($previous_video): ?>
            if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                e.preventDefault();
                window.location.href = '<?php echo site_url($module_base_url . '/watch_video/' . (int)$course['id'] . '/' . (int)$previous_video['id']); ?>';
            }
            <?php endif; ?>
            
            <?php if ($next_video): ?>
            if (e.key === 'ArrowRight' || e.keyCode === 39) {
                e.preventDefault();
                window.location.href = '<?php echo site_url($module_base_url . '/watch_video/' . (int)$course['id'] . '/' . (int)$next_video['id']); ?>';
            }
            <?php endif; ?>
            
            // Space = play/pause
            if (e.key === ' ' || e.keyCode === 32) {
                e.preventDefault();
                if (vimeoPlayer) {
                    vimeoPlayer.getPaused().then(function(paused) {
                        if (paused) {
                            vimeoPlayer.play();
                        } else {
                            vimeoPlayer.pause();
                        }
                    });
                }
            }
        });
    }
    
    /**
     * Scroll to active video in playlist
     */
    function scrollToActiveVideo() {
        var $activeItem = $('.playlist-item.active');
        var $playlist = $('.videos-playlist');
        
        if ($activeItem.length && $playlist.length) {
            var scrollTo = $activeItem.position().top + $playlist.scrollTop() - 100;
            $playlist.animate({ scrollTop: scrollTo }, 500);
        }
    }
    
})();
</script>