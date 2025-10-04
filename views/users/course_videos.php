<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Course Videos Page -->
<div class="row">
    <div class="col-md-12">
        <!-- Course Header -->
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

                <!-- Course Info Header -->
                <div class="course-videos-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="course-title">
                                <i class="fa fa-play-circle text-primary"></i>
                                <?php echo html_escape($course['title']); ?> - Videos
                            </h2>
                            <p class="course-description text-muted">
                                <?php echo html_escape($course['description']); ?>
                            </p>
                            
                            <div class="course-stats">
                                <span class="stat-badge">
                                    <i class="fa fa-tag"></i>
                                    <?php echo html_escape($course['category']); ?>
                                </span>
                                <span class="stat-badge">
                                    <i class="fa fa-signal"></i>
                                    <?php echo html_escape($course['level']); ?> Level
                                </span>
                                <span class="stat-badge">
                                    <i class="fa fa-globe"></i>
                                    <?php echo html_escape($course['language']); ?>
                                </span>
                                <span class="stat-badge">
                                    <i class="fa fa-play-circle"></i>
                                    <?php echo $video_count; ?> Videos
                                </span>
                                <?php if (!empty($course['course_duration'])): ?>
                                <span class="stat-badge">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo html_escape($course['course_duration']); ?>
                                </span>
                                <?php elseif (!empty($total_duration)): ?>
                                <span class="stat-badge">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo $total_duration; ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                <span class="stat-badge stat-badge-free">
                                    <i class="fa fa-gift"></i>
                                    FREE
                                </span>
                                <?php else: ?>
                                <span class="stat-badge stat-badge-paid">
                                    <i class="fa fa-money"></i>
                                    $<?php echo number_format($course['price'], 2); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-right">
                            <!-- Course Overview Card -->
                            <div class="course-overview-card">
                                <div class="overview-price">
                                    <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                        <span class="price-free">FREE COURSE</span>
                                    <?php else: ?>
                                        <span class="price-paid">$<?php echo number_format($course['price'], 2); ?></span>
                                        <small class="price-label">One-time payment</small>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($videos)): ?>
                                    <a href="<?php echo site_url('klms/Lms_users/watch_video/' . $course['id'] . '/' . $videos[0]['id']); ?>" 
                                       class="btn btn-primary btn-lg btn-block">
                                        <i class="fa fa-play"></i> Start Learning
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo site_url('klms/Lms_users/view_course/' . $course['id']); ?>" 
                                   class="btn btn-default btn-block">
                                    <i class="fa fa-arrow-left"></i> Back to Course
                                </a>
                                
                                <div class="overview-details">
                                    <div class="detail-item">
                                        <i class="fa fa-signal"></i>
                                        <span><?php echo html_escape($course['level']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa fa-globe"></i>
                                        <span><?php echo html_escape($course['language']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa fa-play-circle"></i>
                                        <span><?php echo $video_count; ?> Videos</span>
                                    </div>
                                    <?php if (!empty($course['course_duration'])): ?>
                                    <div class="detail-item">
                                        <i class="fa fa-clock-o"></i>
                                        <span><?php echo html_escape($course['course_duration']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Videos Grid/List -->
        <div class="videos-container">
            <?php if (!empty($videos)): ?>
                
                <!-- View Toggle -->
                <div class="view-controls panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Course Videos (<?php echo $video_count; ?>)</h4>
                                <p class="text-muted">
                                    <?php echo html_escape($course['level']); ?> level • 
                                    <?php echo html_escape($course['language']); ?> • 
                                    <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                        <span class="text-success">Free</span>
                                    <?php else: ?>
                                        <span class="text-warning">Paid Course</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default active" id="grid-view-btn">
                                        <i class="fa fa-th"></i> Grid
                                    </button>
                                    <button type="button" class="btn btn-default" id="list-view-btn">
                                        <i class="fa fa-list"></i> List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div id="videos-grid" class="videos-grid">
                    <?php foreach ($videos as $index => $video): ?>
                        <div class="video-card panel_s">
                            <div class="video-thumbnail">
                                <!-- Video Thumbnail/Placeholder -->
                                <div class="video-thumbnail-container">
                                    <?php if (!empty($video['vimeo_url'])): ?>
                                        <?php 
                                        // Extract Vimeo ID for thumbnail
                                        preg_match('/vimeo\.com\/(\d+)/', $video['vimeo_url'], $matches);
                                        $vimeo_id = isset($matches[1]) ? $matches[1] : null;
                                        ?>
                                        <?php if ($vimeo_id): ?>
                                            <img src="https://vumbnail.com/<?php echo $vimeo_id; ?>.jpg" 
                                                 alt="<?php echo html_escape($video['title']); ?>"
                                                 class="video-thumb-image"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <!-- Fallback placeholder -->
                                    <div class="video-placeholder" <?php echo isset($vimeo_id) && $vimeo_id ? 'style="display:none;"' : ''; ?>>
                                        <i class="fa fa-play-circle fa-4x"></i>
                                    </div>
                                    
                                    <!-- Video Overlay -->
                                    <div class="video-overlay">
                                        <div class="video-number"><?php echo ($index + 1); ?></div>
                                        <?php if (!empty($video['duration'])): ?>
                                            <div class="video-duration">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo html_escape($video['duration']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Course Level Badge -->
                                    <div class="course-level-indicator">
                                        <span class="level-badge level-<?php echo strtolower($course['level']); ?>">
                                            <?php echo html_escape($course['level']); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Play Button -->
                                    <div class="video-play-btn">
                                        <a href="<?php echo site_url('klms/Lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>">
                                            <i class="fa fa-play"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel-body video-info">
                                <h5 class="video-title">
                                    <a href="<?php echo site_url('klms/Lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>">
                                        <?php echo html_escape($video['title']); ?>
                                    </a>
                                </h5>
                                
                                <?php if (!empty($video['description'])): ?>
                                    <p class="video-description">
                                        <?php echo html_escape(substr($video['description'], 0, 100)); ?>
                                        <?php if (strlen($video['description']) > 100): ?>...<?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="video-meta">
                                    <div class="meta-row">
                                        <small class="text-muted">
                                            <i class="fa fa-calendar"></i>
                                            <?php echo date('M d, Y', strtotime($video['created_at'])); ?>
                                        </small>
                                        <?php if (!empty($video['sort_order'])): ?>
                                        <small class="text-muted">
                                            <i class="fa fa-sort-numeric-asc"></i>
                                            Lesson <?php echo $video['sort_order']; ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="video-actions">
                                    <a href="<?php echo site_url('klms/Lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-play"></i> Watch Video
                                    </a>
                                    
                                    <?php if (!empty($video['vimeo_url'])): ?>
                                        <a href="<?php echo $video['vimeo_url']; ?>" 
                                           target="_blank" 
                                           class="btn btn-default btn-sm">
                                            <i class="fa fa-external-link"></i> Vimeo
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- List View (Hidden by default) -->
                <div id="videos-list" class="videos-list panel_s" style="display: none;">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover videos-table">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th width="80">Thumbnail</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th width="100">Duration</th>
                                        <th width="80">Lesson</th>
                                        <th width="120">Created</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($videos as $index => $video): ?>
                                        <tr class="video-row">
                                            <td class="text-center">
                                                <span class="video-number-badge"><?php echo ($index + 1); ?></span>
                                            </td>
                                            <td>
                                                <div class="video-list-thumbnail">
                                                    <?php if (!empty($video['vimeo_url'])): ?>
                                                        <?php 
                                                        preg_match('/vimeo\.com\/(\d+)/', $video['vimeo_url'], $matches);
                                                        $vimeo_id = isset($matches[1]) ? $matches[1] : null;
                                                        ?>
                                                        <?php if ($vimeo_id): ?>
                                                            <img src="https://vumbnail.com/<?php echo $vimeo_id; ?>.jpg" 
                                                                 alt="<?php echo html_escape($video['title']); ?>"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <div class="video-list-placeholder" <?php echo isset($vimeo_id) && $vimeo_id ? 'style="display:none;"' : ''; ?>>
                                                        <i class="fa fa-play-circle"></i>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>
                                                    <a href="<?php echo site_url('klms/Lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>">
                                                        <?php echo html_escape($video['title']); ?>
                                                    </a>
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo html_escape($course['level']); ?> • <?php echo html_escape($course['language']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    <?php 
                                                    $description = !empty($video['description']) ? $video['description'] : 'No description';
                                                    echo html_escape(substr($description, 0, 80)); 
                                                    ?>
                                                    <?php if (strlen($description) > 80): ?>...<?php endif; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($video['duration'])): ?>
                                                    <span class="badge badge-info">
                                                        <?php echo html_escape($video['duration']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($video['sort_order'])): ?>
                                                    <span class="badge badge-primary">
                                                        <?php echo $video['sort_order']; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($video['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group-sm">
                                                    <a href="<?php echo site_url('klms/Lms_users/watch_video/' . $course['id'] . '/' . $video['id']); ?>" 
                                                       class="btn btn-primary btn-xs">
                                                        <i class="fa fa-play"></i> Watch
                                                    </a>
                                                    <?php if (!empty($video['vimeo_url'])): ?>
                                                        <a href="<?php echo $video['vimeo_url']; ?>" 
                                                           target="_blank" 
                                                           class="btn btn-default btn-xs">
                                                            <i class="fa fa-external-link"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- No Videos State -->
                <div class="panel_s">
                    <div class="panel-body text-center" style="padding: 60px 20px;">
                        <i class="fa fa-video-camera fa-5x text-muted tw-mb-4"></i>
                        <h3>No Videos Available</h3>
                        <p class="text-muted">This course doesn't have any videos yet. Check back later!</p>
                        <div class="course-info-summary">
                            <span class="info-badge">
                                <i class="fa fa-signal"></i> <?php echo html_escape($course['level']); ?>
                            </span>
                            <span class="info-badge">
                                <i class="fa fa-globe"></i> <?php echo html_escape($course['language']); ?>
                            </span>
                            <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                <span class="info-badge info-badge-free">
                                    <i class="fa fa-gift"></i> FREE
                                </span>
                            <?php else: ?>
                                <span class="info-badge info-badge-paid">
                                    <i class="fa fa-money"></i> $<?php echo number_format($course['price'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo site_url('klms/Lms_users/view_course/' . $course['id']); ?>" 
                           class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Course
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Course Header */
.course-videos-header {
    margin-bottom: 30px;
}

.course-title {
    color: #333;
    margin-bottom: 10px;
}

.course-description {
    font-size: 1rem;
    line-height: 1.5;
    margin-bottom: 15px;
}

.course-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f8f9fa;
    color: #495057;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
}

.stat-badge-free {
    background: #d4edda;
    color: #155724;
}

.stat-badge-paid {
    background: #fff3cd;
    color: #856404;
}

/* Course Overview Card */
.course-overview-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
}

.overview-price {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.price-free {
    font-size: 18px;
    font-weight: 700;
    color: #28a745;
    text-transform: uppercase;
}

.price-paid {
    font-size: 24px;
    font-weight: 700;
    color: #ffc107;
    display: block;
}

.price-label {
    display: block;
    color: #666;
    font-size: 12px;
    margin-top: 3px;
}

.overview-details {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.detail-item {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 13px;
    color: #666;
}

.detail-item:last-child {
    margin-bottom: 0;
}

/* View Controls */
.view-controls {
    margin-bottom: 20px;
}

/* Videos Grid */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.video-card {
    overflow: hidden;
    transition: all 0.3s ease;
    border-radius: 12px;
}

.video-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Video Thumbnail */
.video-thumbnail {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.video-thumbnail-container {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-thumb-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: white;
}

.video-overlay {
    position: absolute;
    top: 12px;
    left: 12px;
    right: 12px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.video-number {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.video-duration {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Course Level Indicator */
.course-level-indicator {
    position: absolute;
    bottom: 12px;
    left: 12px;
}

.level-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.level-beginner {
    background: #28a745;
    color: white;
}

.level-intermediate {
    background: #ffc107;
    color: #333;
}

.level-advanced {
    background: #dc3545;
    color: white;
}

.video-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .video-play-btn {
    opacity: 1;
}

.video-play-btn a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.95);
    color: #007bff;
    border-radius: 50%;
    font-size: 20px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.video-play-btn a:hover {
    background: #007bff;
    color: white;
    transform: scale(1.1);
}

/* Video Info */
.video-info {
    padding: 20px;
}

.video-title {
    margin: 0 0 10px 0;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
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
    color: #666;
    font-size: 14px;
    line-height: 1.4;
    margin-bottom: 15px;
}

.video-meta {
    margin-bottom: 15px;
}

.meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
}

.video-actions {
    display: flex;
    gap: 8px;
}

/* List View Styles */
.videos-list {
    border-radius: 12px;
}

.videos-table {
    margin: 0;
}

.video-row:hover {
    background-color: #f8f9fa;
}

.video-number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 600;
}

.video-list-thumbnail {
    width: 60px;
    height: 40px;
    border-radius: 6px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.video-list-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-list-placeholder {
    color: #666;
    font-size: 18px;
}

/* No Videos State */
.course-info-summary {
    margin: 20px 0;
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f8f9fa;
    color: #495057;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
}

.info-badge-free {
    background: #d4edda;
    color: #155724;
}

.info-badge-paid {
    background: #fff3cd;
    color: #856404;
}

/* Responsive Design */
@media (max-width: 768px) {
    .videos-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .course-videos-header .row {
        flex-direction: column;
    }
    
    .course-videos-header .col-md-4 {
        text-align: left;
        margin-top: 20px;
    }
    
    .course-stats {
        flex-direction: column;
        gap: 8px;
    }
    
    .view-controls .row {
        flex-direction: column;
        gap: 15px;
    }
    
    .view-controls .col-md-6:last-child {
        text-align: left;
    }
    
    .video-thumbnail {
        height: 160px;
    }
    
    .video-actions {
        flex-direction: column;
    }
    
    .videos-table {
        font-size: 12px;
    }
    
    .video-list-thumbnail {
        width: 40px;
        height: 30px;
    }

    .meta-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .course-overview-card {
        margin-top: 20px;
    }

    .course-info-summary {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 576px) {
    .videos-grid {
        grid-template-columns: 1fr;
    }
    
    .video-info {
        padding: 15px;
    }
    
    .video-play-btn a {
        width: 50px;
        height: 50px;
        font-size: 16px;
    }

    .course-stats {
        gap: 5px;
    }

    .stat-badge {
        font-size: 11px;
        padding: 4px 8px;
    }
}
</style>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // View toggle functionality
    $('#grid-view-btn').on('click', function() {
        $(this).addClass('active').siblings().removeClass('active');
        $('#videos-grid').show();
        $('#videos-list').hide();
    });
    
    $('#list-view-btn').on('click', function() {
        $(this).addClass('active').siblings().removeClass('active');
        $('#videos-grid').hide();
        $('#videos-list').show();
    });
    
    // Video row click handler for list view
    $('.video-row').on('click', function(e) {
        if (!$(e.target).closest('a, button').length) {
            const videoUrl = $(this).find('.video-title a').attr('href');
            if (videoUrl) {
                window.location.href = videoUrl;
            }
        }
    });
    
    // Hover effects
    $('.video-card').hover(
        function() {
            $(this).find('.video-play-btn').addClass('show');
        },
        function() {
            $(this).find('.video-play-btn').removeClass('show');
        }
    );
});
</script>