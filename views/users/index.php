<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Course Catalog Main Page -->
<div class="row">
    <!-- Sidebar Filters -->
    <div class="col-md-3">
        <div class="filters-sidebar">
            <!-- Search Box -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5><i class="fa fa-search"></i> Search Courses</h5>
                    <form id="search-form" method="get" action="<?php echo site_url('lms/lms_users/search'); ?>">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   id="search-input"
                                   placeholder="Search courses..." 
                                   value="<?php echo $this->input->get('q', TRUE); ?>"
                                   autocomplete="off">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Search Suggestions Dropdown -->
                    <div id="search-suggestions" class="search-suggestions" style="display: none;"></div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5><i class="fa fa-filter"></i> Filter by Category</h5>
                    <div class="category-filters">
                        <div class="category-item <?php echo empty($this->input->get('category')) ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms_users'); ?>" class="category-link">
                                <i class="fa fa-th-large"></i>
                                <span>All Courses</span>
                                <span class="course-count"><?php echo $total_courses; ?></span>
                            </a>
                        </div>
                        
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <div class="category-item <?php echo ($this->input->get('category') == $category['category']) ? 'active' : ''; ?>">
                                    <a href="<?php echo site_url('lms/lms_users/category/' . urlencode($category['category'])); ?>" 
                                       class="category-link">
                                        <i class="fa fa-folder"></i>
                                        <span><?php echo html_escape($category['category']); ?></span>
                                        <span class="course-count"><?php echo $category['course_count']; ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Level Filter -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5><i class="fa fa-signal"></i> Filter by Level</h5>
                    <div class="level-filters">
                        <div class="level-item <?php echo empty($this->input->get('level')) ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms_users'); ?>" class="level-link">
                                <i class="fa fa-th-large"></i>
                                <span>All Levels</span>
                            </a>
                        </div>
                        <div class="level-item <?php echo ($this->input->get('level') == 'Beginner') ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms/lms_users') . '?level=Beginner'; ?>" class="level-link">
                                <i class="fa fa-circle-o"></i>
                                <span>Beginner</span>
                            </a>
                        </div>
                        <div class="level-item <?php echo ($this->input->get('level') == 'Intermediate') ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms/lms_users') . '?level=Intermediate'; ?>" class="level-link">
                                <i class="fa fa-adjust"></i>
                                <span>Intermediate</span>
                            </a>
                        </div>
                        <div class="level-item <?php echo ($this->input->get('level') == 'Advanced') ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms/lms_users') . '?level=Advanced'; ?>" class="level-link">
                                <i class="fa fa-circle"></i>
                                <span>Advanced</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Filter -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5><i class="fa fa-money"></i> Filter by Price</h5>
                    <div class="price-filters">
                        <div class="price-item <?php echo empty($this->input->get('price_type')) ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms_users'); ?>" class="price-link">
                                <i class="fa fa-th-large"></i>
                                <span>All Courses</span>
                            </a>
                        </div>
                        <div class="price-item <?php echo ($this->input->get('price_type') == 'free') ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms/lms_users') . '?price_type=free'; ?>" class="price-link">
                                <i class="fa fa-gift"></i>
                                <span>Free Courses</span>
                            </a>
                        </div>
                        <div class="price-item <?php echo ($this->input->get('price_type') == 'paid') ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('lms/lms_users') . '?price_type=paid'; ?>" class="price-link">
                                <i class="fa fa-credit-card"></i>
                                <span>Paid Courses</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Statistics -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5><i class="fa fa-bar-chart"></i> Statistics</h5>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $total_courses; ?></div>
                            <div class="stat-label">Total Courses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo count($categories); ?></div>
                            <div class="stat-label">Categories</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured/Latest Toggle -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5><i class="fa fa-sort"></i> View Options</h5>
                    <div class="view-options">
                        <div class="btn-group-vertical btn-block" role="group">
                            <button type="button" class="btn btn-default view-option active" data-view="all">
                                <i class="fa fa-th"></i> All Courses
                            </button>
                            <button type="button" class="btn btn-default view-option" data-view="featured">
                                <i class="fa fa-star"></i> Featured
                            </button>
                            <button type="button" class="btn btn-default view-option" data-view="latest">
                                <i class="fa fa-clock-o"></i> Latest
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9">
        <!-- Page Header -->
        <div class="courses-header">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="page-title">
                        <i class="fa fa-graduation-cap text-primary"></i>
                        Course Catalog
                    </h1>
                    <p class="page-subtitle">
                        Discover and learn with our comprehensive course library
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="header-controls">
                        <!-- Sort Options -->
                        <div class="sort-controls">
                            <label for="sort-select">Sort by:</label>
                            <select id="sort-select" class="form-control form-control-sm">
                                <option value="sort_order_asc">Default Order</option>
                                <option value="date_desc">Newest First</option>
                                <option value="date_asc">Oldest First</option>
                                <option value="title_asc">Title A-Z</option>
                                <option value="title_desc">Title Z-A</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="level">Level</option>
                                <option value="category">Category</option>
                            </select>
                        </div>
                        
                        <!-- View Toggle -->
                        <div class="view-toggle">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default active" id="grid-view">
                                    <i class="fa fa-th"></i>
                                </button>
                                <button type="button" class="btn btn-default" id="list-view">
                                    <i class="fa fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Results Info -->
        <div class="results-info">
            <div class="results-count">
                Showing <span id="showing-count"><?php echo count($courses); ?></span> courses
                <?php if (!empty($this->input->get('category'))): ?>
                    in <strong><?php echo html_escape($this->input->get('category')); ?></strong>
                <?php endif; ?>
                <?php if (!empty($this->input->get('level'))): ?>
                    - <strong><?php echo html_escape($this->input->get('level')); ?></strong> level
                <?php endif; ?>
                <?php if (!empty($this->input->get('price_type'))): ?>
                    - <strong><?php echo ucfirst($this->input->get('price_type')); ?></strong> courses
                <?php endif; ?>
            </div>
            
            <?php if (!empty($this->input->get('q'))): ?>
                <div class="search-info">
                    <i class="fa fa-search"></i>
                    Search results for: <strong>"<?php echo html_escape($this->input->get('q')); ?>"</strong>
                    <a href="<?php echo site_url('lms_users'); ?>" class="clear-search">
                        <i class="fa fa-times"></i> Clear
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="loading-indicator" style="display: none;">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Loading courses...</p>
        </div>

        <!-- Courses Grid -->
        <div id="courses-container" class="courses-container">
            <?php if (!empty($courses)): ?>
                <div id="courses-grid" class="courses-grid">
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card panel_s">
                            <!-- Course Image/Thumbnail -->
                            <div class="course-image">
                                <?php if (!empty($course['cover_image']) && file_exists($course['cover_image'])): ?>
                                    <img src="<?php echo base_url($course['cover_image']); ?>" 
                                         alt="<?php echo html_escape($course['title']); ?>"
                                         class="course-thumb">
                                <?php else: ?>
                                    <div class="course-placeholder">
                                        <i class="fa fa-graduation-cap fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Course Overlay -->
                                <div class="course-overlay">
                                    <div class="course-category">
                                        <i class="fa fa-tag"></i>
                                        <?php echo html_escape($course['category']); ?>
                                    </div>
                                    <div class="course-action">
                                        <a href="<?php echo site_url('lms/lms_users/view_course/' . $course['id']); ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> View Course
                                        </a>
                                    </div>
                                </div>

                                <!-- Price Badge -->
                                <div class="course-price-badge">
                                    <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                        <span class="price-free">FREE</span>
                                    <?php else: ?>
                                        <span class="price-paid">$<?php echo number_format($course['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Level Badge -->
                                <div class="course-level-badge">
                                    <span class="level-<?php echo strtolower($course['level']); ?>">
                                        <?php echo html_escape($course['level']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Course Info -->
                            <div class="panel-body course-info">
                                <h4 class="course-title">
                                    <a href="<?php echo site_url('lms/lms_users/view_course/' . $course['id']); ?>">
                                        <?php echo html_escape($course['title']); ?>
                                    </a>
                                </h4>
                                
                                <p class="course-description">
                                    <?php echo html_escape(substr($course['description'], 0, 120)); ?>
                                    <?php if (strlen($course['description']) > 120): ?>...<?php endif; ?>
                                </p>
                                
                                <div class="course-meta">
                                    <div class="meta-item">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fa fa-play-circle"></i>
                                        <?php 
                                        $video_count = isset($course['video_count']) ? $course['video_count'] : 0;
                                        echo $video_count . ' video' . ($video_count != 1 ? 's' : '');
                                        ?>
                                    </div>
                                </div>

                                <div class="course-details">
                                    <div class="detail-item">
                                        <i class="fa fa-clock-o"></i>
                                        <span><?php echo !empty($course['course_duration']) ? html_escape($course['course_duration']) : 'Duration not specified'; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fa fa-globe"></i>
                                        <span><?php echo html_escape($course['language']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="course-actions">
                                    <a href="<?php echo site_url('lms/lms_users/view_course/' . $course['id']); ?>" 
                                       class="btn btn-primary btn-block">
                                        <i class="fa fa-play"></i> Start Learning
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- List View (Hidden by default) -->
                <div id="courses-list" class="courses-list" style="display: none;">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="60">Image</th>
                                            <th>Course Title</th>
                                            <th width="100">Price</th>
                                            <th width="100">Level</th>
                                            <th width="120">Category</th>
                                            <th width="80">Videos</th>
                                            <th width="100">Duration</th>
                                            <th width="80">Language</th>
                                            <th width="100">Created</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $course): ?>
                                            <tr>
                                                <td>
                                                    <div class="course-list-thumb">
                                                        <?php if (!empty($course['cover_image']) && file_exists($course['cover_image'])): ?>
                                                            <img src="<?php echo base_url($course['cover_image']); ?>" 
                                                                 alt="<?php echo html_escape($course['title']); ?>">
                                                        <?php else: ?>
                                                            <div class="course-list-placeholder">
                                                                <i class="fa fa-graduation-cap"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>
                                                        <a href="<?php echo site_url('lms/lms_users/view_course/' . $course['id']); ?>">
                                                            <?php echo html_escape($course['title']); ?>
                                                        </a>
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo html_escape(substr($course['description'], 0, 80)); ?>
                                                        <?php if (strlen($course['description']) > 80): ?>...<?php endif; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($course['is_free'] == 1 || $course['price'] == 0): ?>
                                                        <span class="label label-success">FREE</span>
                                                    <?php else: ?>
                                                        <span class="label label-warning">$<?php echo number_format($course['price'], 2); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="label label-<?php echo ($course['level'] == 'Beginner') ? 'success' : (($course['level'] == 'Intermediate') ? 'warning' : 'danger'); ?>">
                                                        <?php echo html_escape($course['level']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="label label-info">
                                                        <?php echo html_escape($course['category']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">
                                                        <?php 
                                                        $video_count = isset($course['video_count']) ? $course['video_count'] : 0;
                                                        echo $video_count;
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo !empty($course['course_duration']) ? html_escape($course['course_duration']) : '-'; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo html_escape($course['language']); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="<?php echo site_url('lms/lms_users/view_course/' . $course['id']); ?>" 
                                                       class="btn btn-primary btn-xs">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if (!empty($pagination)): ?>
                    <div class="pagination-container">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Courses Found -->
                <div class="no-courses-found">
                    <div class="panel_s">
                        <div class="panel-body text-center" style="padding: 60px 20px;">
                            <i class="fa fa-graduation-cap fa-5x text-muted"></i>
                            <h3 class="no-courses-title">No Courses Found</h3>
                            <p class="text-muted">
                                <?php if (!empty($this->input->get('q'))): ?>
                                    No courses match your search criteria. Try different keywords.
                                <?php elseif (!empty($this->input->get('category'))): ?>
                                    No courses found in this category.
                                <?php else: ?>
                                    No courses are currently available.
                                <?php endif; ?>
                            </p>
                            
                            <?php if (!empty($this->input->get('q')) || !empty($this->input->get('category'))): ?>
                                <a href="<?php echo site_url('lms_users'); ?>" class="btn btn-primary">
                                    <i class="fa fa-refresh"></i> View All Courses
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Page Header */
.courses-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
}

.page-title {
    color: #333;
    margin-bottom: 5px;
    font-weight: 600;
}

.page-subtitle {
    color: #666;
    font-size: 16px;
    margin: 0;
}

.header-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: flex-end;
}

.sort-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sort-controls label {
    font-size: 13px;
    margin: 0;
    white-space: nowrap;
}

.sort-controls select {
    width: auto;
    min-width: 140px;
}

/* Sidebar Styles */
.filters-sidebar {
    position: sticky;
    top: 20px;
}

.filters-sidebar .panel_s {
    margin-bottom: 25px;
}

.filters-sidebar h5 {
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

/* Search Box */
.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

.search-suggestion-item {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-suggestion-item:hover {
    background-color: #f8f9fa;
}

.search-suggestion-item:last-child {
    border-bottom: none;
}

/* Category Filters */
.category-filters {
    max-height: 300px;
    overflow-y: auto;
}

.category-item {
    margin-bottom: 3px;
}

.category-item.active .category-link {
    background-color: #007bff;
    color: white;
    border-radius: 4px;
}

.category-link {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.category-link:hover {
    background-color: #f8f9fa;
    text-decoration: none;
    color: #007bff;
}

.category-item.active .category-link:hover {
    background-color: #0056b3;
    color: white;
}

.category-link i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

.category-link span:nth-child(2) {
    flex: 1;
}

.course-count {
    background: #f8f9fa;
    color: #666;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    margin-left: auto;
}

.category-item.active .course-count {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

/* Level Filters */
.level-filters, .price-filters {
    max-height: 200px;
    overflow-y: auto;
}

.level-item, .price-item {
    margin-bottom: 3px;
}

.level-item.active .level-link, .price-item.active .price-link {
    background-color: #007bff;
    color: white;
    border-radius: 4px;
}

.level-link, .price-link {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.level-link:hover, .price-link:hover {
    background-color: #f8f9fa;
    text-decoration: none;
    color: #007bff;
}

.level-item.active .level-link:hover, .price-item.active .price-link:hover {
    background-color: #0056b3;
    color: white;
}

.level-link i, .price-link i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

/* Statistics */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: #666;
    font-weight: 500;
}

/* View Options */
.view-options .btn {
    text-align: left;
    justify-content: flex-start;
    border-radius: 4px;
    margin-bottom: 3px;
}

.view-options .btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.view-options .btn i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

/* Results Info */
.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
}

.results-count {
    font-size: 15px;
    color: #666;
}

.search-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #666;
}

.clear-search {
    color: #dc3545;
    text-decoration: none;
    font-size: 13px;
}

.clear-search:hover {
    text-decoration: none;
    color: #a71e2a;
}

/* Loading Indicator */
.loading-indicator {
    text-align: center;
    padding: 40px;
    color: #666;
}

.loading-indicator p {
    margin-top: 10px;
    font-size: 14px;
}

/* Courses Grid */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.course-card {
    overflow: hidden;
    transition: all 0.3s ease;
    border-radius: 12px;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Course Image */
.course-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.course-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.course-card:hover .course-thumb {
    transform: scale(1.05);
}

.course-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.course-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.course-card:hover .course-overlay {
    opacity: 1;
}

.course-category {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Price and Level Badges */
.course-price-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 2;
}

.price-free {
    background: #28a745;
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.price-paid {
    background: #ffc107;
    color: #333;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 700;
}

.course-level-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 2;
}

.level-beginner {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.level-intermediate {
    background: #ffc107;
    color: #333;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.level-advanced {
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

/* Course Info */
.course-info {
    padding: 20px;
}

.course-title {
    margin: 0 0 12px 0;
    font-size: 1.2rem;
    font-weight: 600;
    line-height: 1.3;
}

.course-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.course-title a:hover {
    color: #007bff;
    text-decoration: none;
}

.course-description {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.course-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 13px;
    color: #666;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.course-details {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 12px;
    color: #666;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.course-actions {
    margin-top: 15px;
}

/* List View Styles */
.courses-list {
    margin-bottom: 40px;
}

.course-list-thumb {
    width: 50px;
    height: 35px;
    border-radius: 4px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.course-list-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-list-placeholder {
    color: #666;
    font-size: 14px;
}

/* No Courses Found */
.no-courses-found {
    margin: 40px 0;
}

.no-courses-title {
    color: #333;
    margin: 20px 0 15px 0;
}

/* Pagination */
.pagination-container {
    margin: 40px 0;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .courses-header .row {
        flex-direction: column;
        gap: 20px;
    }
    
    .header-controls {
        justify-content: flex-start;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .sort-controls {
        width: 100%;
    }
    
    .sort-controls select {
        flex: 1;
        min-width: auto;
    }
    
    .filters-sidebar {
        position: static;
        margin-bottom: 30px;
    }
    
    .courses-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .results-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .course-overlay {
        opacity: 1;
        background: rgba(0, 0, 0, 0.4);
    }

    .course-details {
        flex-direction: column;
        gap: 8px;
    }
}

@media (max-width: 576px) {
    .course-image {
        height: 160px;
    }
    
    .course-info {
        padding: 15px;
    }
    
    .course-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .page-title {
        font-size: 1.8rem;
    }
}
</style>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // View Toggle (Grid/List)
    $('#grid-view').on('click', function() {
        $(this).addClass('active').siblings().removeClass('active');
        $('#courses-grid').show();
        $('#courses-list').hide();
    });
    
    $('#list-view').on('click', function() {
        $(this).addClass('active').siblings().removeClass('active');
        $('#courses-grid').hide();
        $('#courses-list').show();
    });
    
    // Search Suggestions
    let searchTimeout;
    $('#search-input').on('input', function() {
        const term = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (term.length >= 2) {
            searchTimeout = setTimeout(function() {
                loadSearchSuggestions(term);
            }, 300);
        } else {
            $('#search-suggestions').hide();
        }
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-input, #search-suggestions').length) {
            $('#search-suggestions').hide();
        }
    });
    
    // View Options
    $('.view-option').on('click', function() {
        const view = $(this).data('view');
        $('.view-option').removeClass('active');
        $(this).addClass('active');
        
        // Filter courses based on view
        filterCoursesByView(view);
    });
    
    // Sort Functionality
    $('#sort-select').on('change', function() {
        const sortBy = $(this).val();
        sortCourses(sortBy);
    });
    
    // CSRF token for AJAX requests
    function getCSRFToken() {
        return $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val() || 
               $('meta[name="csrf-token"]').attr('content');
    }
    
    // Load search suggestions
    function loadSearchSuggestions(term) {
        $.ajax({
            url: '<?php echo site_url('lms/lms_users/ajax_search_suggestions'); ?>',
            type: 'GET',
            data: { term: term },
            dataType: 'json',
            success: function(suggestions) {
                displaySuggestions(suggestions);
            },
            error: function() {
                $('#search-suggestions').hide();
            }
        });
    }
    
    // Display search suggestions
    function displaySuggestions(suggestions) {
        const container = $('#search-suggestions');
        container.empty();
        
        if (suggestions.length > 0) {
            suggestions.forEach(function(suggestion) {
                const item = $('<div class="search-suggestion-item">')
                    .html('<strong>' + suggestion.label + '</strong><br><small class="text-muted">' + suggestion.category + '</small>')
                    .on('click', function() {
                        window.location.href = suggestion.url;
                    });
                container.append(item);
            });
            container.show();
        } else {
            container.hide();
        }
    }
    
    // Filter courses by view type
    function filterCoursesByView(view) {
        $('#loading-indicator').show();
        $('#courses-container').hide();
        
        // Simulate loading for demo - replace with actual AJAX call
        setTimeout(function() {
            $('#loading-indicator').hide();
            $('#courses-container').show();
            
            // Update showing count
            updateShowingCount();
        }, 500);
    }
    
    // Sort courses
    function sortCourses(sortBy) {
        const grid = $('#courses-grid');
        const courseCards = grid.find('.course-card').get();
        
        courseCards.sort(function(a, b) {
            let aVal, bVal;
            
            switch (sortBy) {
                case 'title_asc':
                    aVal = $(a).find('.course-title a').text().toLowerCase();
                    bVal = $(b).find('.course-title a').text().toLowerCase();
                    return aVal.localeCompare(bVal);
                    
                case 'title_desc':
                    aVal = $(a).find('.course-title a').text().toLowerCase();
                    bVal = $(b).find('.course-title a').text().toLowerCase();
                    return bVal.localeCompare(aVal);
                    
                case 'date_asc':
                case 'date_desc':
                    // For demo - in real implementation, you'd store timestamps
                    aVal = $(a).find('.meta-item:first').text();
                    bVal = $(b).find('.meta-item:first').text();
                    return sortBy === 'date_asc' ? 
                        aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                    
                case 'category':
                    aVal = $(a).find('.course-category').text().toLowerCase();
                    bVal = $(b).find('.course-category').text().toLowerCase();
                    return aVal.localeCompare(bVal);
                    
                case 'price_asc':
                case 'price_desc':
                    // For demo - in real implementation, you'd use actual price data
                    aVal = $(a).find('.price-paid, .price-free').text();
                    bVal = $(b).find('.price-paid, .price-free').text();
                    return sortBy === 'price_asc' ? 
                        aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                    
                default:
                    return 0;
            }
        });
        
        grid.empty().append(courseCards);
    }
    
    // Update showing count
    function updateShowingCount() {
        const count = $('#courses-grid .course-card:visible').length;
        $('#showing-count').text(count);
    }
    
    // Initialize
    updateShowingCount();
});
</script>