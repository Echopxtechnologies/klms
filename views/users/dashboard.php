<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row dashboard-container">
    <div class="col-md-12">
        <!-- Page Header -->
        <div class="page-header">
            <h3 class="page-title">
                <i class="fa fa-graduation-cap"></i> My Learning Dashboard
            </h3>
            <div class="header-actions">
                <a href="<?php echo site_url($module_base_url . '/all_courses'); ?>" class="btn btn-primary">
                    <i class="fa fa-search"></i> Explore Courses
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card stat-total">
                <div class="stat-icon">
                    <i class="fa fa-book"></i>
                </div>
                <div class="stat-content">
                    <h4><?php echo (int)$stats['total_enrolled']; ?></h4>
                    <p>Total Courses</p>
                </div>
            </div>

            <div class="stat-card stat-progress">
                <div class="stat-icon">
                    <i class="fa fa-spinner"></i>
                </div>
                <div class="stat-content">
                    <h4><?php echo (int)$stats['in_progress']; ?></h4>
                    <p>In Progress</p>
                </div>
            </div>

            <div class="stat-card stat-completed">
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h4><?php echo (int)$stats['completed']; ?></h4>
                    <p>Completed</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="panel_s">
            <div class="panel-body">
                
                <?php if (empty($enrolled_courses)): ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <h3>No Courses Yet</h3>
                        <p>You haven't enrolled in any courses yet. Start your learning journey today!</p>
                        <a href="<?php echo site_url($module_base_url . '/all_courses'); ?>" class="btn btn-primary btn-lg">
                            <i class="fa fa-search"></i> Explore Courses
                        </a>
                    </div>

                <?php else: ?>
                    <!-- Enrolled Courses Header -->
                    <div class="section-header">
                        <h4>
                            <i class="fa fa-play-circle"></i> My Courses
                            <small>(<?php echo count($enrolled_courses); ?>)</small>
                        </h4>
                        <div class="filter-actions">
                            <select id="courseFilter" class="form-control" onchange="filterDashboardCourses()">
                                <option value="">All Courses</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="not_started">Not Started</option>
                            </select>
                        </div>
                    </div>

                    <!-- Enrolled Courses Grid -->
                    <div class="enrolled-courses-grid" id="enrolledCoursesGrid">
                        <?php foreach ($enrolled_courses as $course): ?>
                            <?php
                                $progress = isset($course['progress_percent']) ? (int)$course['progress_percent'] : 0;
                                $status = $course['progress_status'] ?? 'not_started';
                                $videos_watched = isset($course['videos_watched']) ? (int)$course['videos_watched'] : 0;
                                $total_videos = isset($course['total_videos']) ? (int)$course['total_videos'] : 0;
                                
                                $status_class = [
                                    'completed' => 'success',
                                    'in_progress' => 'warning',
                                    'not_started' => 'default'
                                ][$status] ?? 'default';

                                $status_text = [
                                    'completed' => 'Completed',
                                    'in_progress' => 'In Progress',
                                    'not_started' => 'Not Started'
                                ][$status] ?? 'Not Started';

                                // Image
                                $img = !empty($course['cover_image_abs'] ?? $course['cover_image'])
                                     ? ($course['cover_image_abs'] ?? base_url(ltrim($course['cover_image'], '/')))
                                     : 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="338"><rect width="100%" height="100%" fill="#f0f2f5"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#9aa0a6" font-size="18" font-family="Arial">No Image</text></svg>');
                            ?>
                            
                            <div class="enrolled-course-card" data-status="<?php echo html_escape($status); ?>">
                                <div class="course-image">
                                    <img src="<?php echo $img; ?>" alt="<?php echo html_escape($course['title']); ?>">
                                    <span class="status-badge badge-<?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>

                                <div class="course-content">
                                    <h5 class="course-title">
                                        <?php echo html_escape($course['title']); ?>
                                    </h5>

                                    <div class="course-meta">
                                        <?php if (!empty($course['category'])): ?>
                                            <span><i class="fa fa-folder"></i> <?php echo html_escape($course['category']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($course['level'])): ?>
                                            <span><i class="fa fa-signal"></i> <?php echo html_escape($course['level']); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="course-progress">
                                        <div class="progress-info">
                                            <span><?php echo $videos_watched; ?> of <?php echo $total_videos; ?> videos</span>
                                            <span class="progress-percent"><?php echo $progress; ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-<?php echo $status_class; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $progress; ?>%"
                                                 aria-valuenow="<?php echo $progress; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="course-actions">
                                        <?php if ($status === 'completed'): ?>
                                            <a href="<?php echo site_url($module_base_url . '/view_course/' . (int)$course['id']); ?>" 
                                               class="btn btn-default btn-block">
                                                <i class="fa fa-eye"></i> Review Course
                                            </a>
                                        <?php elseif ($status === 'in_progress'): ?>
                                            <a href="<?php echo site_url($module_base_url . '/watch_video/' . (int)$course['id'] . '/' . (int)$course['last_video_id']); ?>" 
                                               class="btn btn-primary btn-block">
                                                <i class="fa fa-play"></i> Continue Learning
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo site_url($module_base_url . '/course_videos/' . (int)$course['id']); ?>" 
                                               class="btn btn-success btn-block">
                                                <i class="fa fa-play"></i> Start Course
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Explore More Section -->
                    <div class="explore-more-section">
                        <h4>Want to learn more?</h4>
                        <p>Explore our extensive course catalog and expand your knowledge.</p>
                        <a href="<?php echo site_url($module_base_url . '/all_courses'); ?>" class="btn btn-primary">
                            <i class="fa fa-search"></i> Browse All Courses
                        </a>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
/* Dashboard Container */
.dashboard-container {
    padding: 20px 0;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
}

.page-title {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title i {
    color: #84c5fe;
}

.header-actions .btn {
    padding: 10px 20px;
}

/* Statistics Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
}

.stat-total .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-progress .stat-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-completed .stat-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-content h4 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-content p {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 12px;
}

.empty-icon {
    font-size: 80px;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 30px;
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.section-header h4 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.filter-actions select {
    min-width: 180px;
}

/* Enrolled Courses Grid */
.enrolled-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.enrolled-course-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.enrolled-course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.course-image {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background: #f5f6f8;
    overflow: hidden;
}

.course-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
}

.badge-success {
    background: #28a745;
}

.badge-warning {
    background: #ffc107;
    color: #212529;
}

.badge-default {
    background: #6c757d;
}

.course-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.course-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 12px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 15px;
}

.course-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.course-progress {
    margin-bottom: 20px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 13px;
}

.progress-percent {
    font-weight: 700;
    color: #28a745;
}

.progress {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.course-actions {
    margin-top: auto;
}

/* Explore More Section */
.explore-more-section {
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    color: #fff;
}

.explore-more-section h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #fff;
}

.explore-more-section p {
    font-size: 1.1rem;
    margin-bottom: 25px;
    opacity: 0.9;
}

.explore-more-section .btn {
    background: #fff;
    color: #667eea;
    border: none;
    padding: 12px 30px;
    font-weight: 600;
}

.explore-more-section .btn:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .enrolled-courses-grid {
        grid-template-columns: 1fr;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .filter-actions {
        width: 100%;
    }

    .filter-actions select {
        width: 100%;
    }
}
</style>

<!-- JavaScript -->
<script>
(function() {
    'use strict';

    // Filter courses by status
    window.filterDashboardCourses = function() {
        const filterValue = document.getElementById('courseFilter').value;
        const cards = document.querySelectorAll('.enrolled-course-card');

        cards.forEach(function(card) {
            const status = card.getAttribute('data-status');
            
            if (!filterValue || status === filterValue) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    };

})();
</script>