<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-dashboard"></i> <?php echo _l('lms_dashboard', 'Learning Management System Dashboard'); ?>
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="btn-group">
                                    <a href="<?php echo admin_url('klms/Lms_admin/add_course'); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Course
                                    </a>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="<?php echo admin_url('klms/Lms_admin/courses'); ?>"><i class="fa fa-graduation-cap"></i> Manage Courses</a></li>
                                        <li><a href="<?php echo admin_url('klms/Lms_admin/students'); ?>"><i class="fa fa-users"></i> View Students</a></li>
                                        <li><a href="<?php echo admin_url('klms/Lms_admin/enrollments'); ?>"><i class="fa fa-list"></i> View Enrollments</a></li>
                                        <li role="separator" class="divider"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="row">
            <!-- Total Courses -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="dashboard-stat-card bg-primary">
                    <div class="stat-icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['total_courses']; ?></div>
                        <div class="stat-label">Total Courses</div>
                        <div class="stat-detail">
                            <small><?php echo $stats['active_courses']; ?> Active</small>
                        </div>
                    </div>
                    <a href="<?php echo admin_url('klms/Lms_admin/courses'); ?>" class="stat-footer">
                        View Courses <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Students -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="dashboard-stat-card bg-success">
                    <div class="stat-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                        <div class="stat-label">Total Students</div>
                        <div class="stat-detail">
                            <small><?php echo $stats['enrolled_students']; ?> Enrollments</small>
                        </div>
                    </div>
                    <a href="<?php echo admin_url('klms/Lms_admin/students'); ?>" class="stat-footer">
                        View Students <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="dashboard-stat-card bg-warning">
                    <div class="stat-icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-detail">
                            <small>₹<?php echo number_format($stats['monthly_revenue'], 2); ?> This Month</small>
                        </div>
                    </div>
                    <a href="<?php echo admin_url('invoices'); ?>" class="stat-footer">
                        View Details <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Active Enrollments -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="dashboard-stat-card bg-info">
                    <div class="stat-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['active_enrollments']; ?></div>
                        <div class="stat-label">Active Enrollments</div>
                        <div class="stat-detail">
                            <small><?php echo $stats['pending_payments']; ?> Pending</small>
                        </div>
                    </div>
                    <a href="<?php echo admin_url('klms/Lms_admin/enrollments'); ?>" class="stat-footer">
                        Manage <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold mbot20">
                            <i class="fa fa-bolt"></i> Quick Actions
                        </h4>
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('klms/Lms_admin/add_course'); ?>" class="quick-action-card">
                                    <div class="quick-action-icon bg-primary">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <h5>Add New Course</h5>
                                        <p>Create a new course</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('klms/Lms_admin/courses'); ?>" class="quick-action-card">
                                    <div class="quick-action-icon bg-info">
                                        <i class="fa fa-graduation-cap"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <h5>Manage Courses</h5>
                                        <p>View all courses</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('klms/Lms_admin/students'); ?>" class="quick-action-card">
                                    <div class="quick-action-icon bg-success">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <h5>View Students</h5>
                                        <p>Manage students</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('klms/Lms_admin/enrollments'); ?>" class="quick-action-card">
                                    <div class="quick-action-icon bg-warning">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <h5>Enrollments</h5>
                                        <p>View all enrollments</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       


        <!-- Data Tables Row -->
        <div class="row">
            <!-- Popular Courses -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold">
                            <i class="fa fa-star"></i> Popular Courses
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Enrollments</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($popular_courses)): ?>
                                        <?php foreach ($popular_courses as $course): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo html_escape($course['title']); ?></strong>
                                                    <br><small class="text-muted"><?php echo html_escape($course['category']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="label label-primary"><?php echo $course['total_enrollments']; ?></span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">₹<?php echo number_format($course['total_revenue'], 2); ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold">
                            <i class="fa fa-clock-o"></i> Recent Enrollments
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_enrollments)): ?>
                                        <?php foreach ($recent_enrollments as $enrollment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo html_escape($enrollment['firstname'] . ' ' . $enrollment['lastname']); ?></strong>
                                                </td>
                                                <td>
                                                    <small><?php echo html_escape($enrollment['course_title']); ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($enrollment['payment_status'] === 'paid'): ?>
                                                        <span class="label label-success">Paid</span>
                                                    <?php else: ?>
                                                        <span class="label label-warning">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo time_ago($enrollment['enrolled_date']); ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No recent enrollments</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<?php //init_tail(); ?>

<!-- Styles -->
<style>
/* Dashboard Stat Cards */
.dashboard-stat-card {
    border-radius: 12px;
    padding: 25px 20px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.dashboard-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.dashboard-stat-card .stat-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 48px;
    opacity: 0.2;
}

.dashboard-stat-card .stat-content {
    position: relative;
    z-index: 1;
}

.dashboard-stat-card .stat-value {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 8px;
}

.dashboard-stat-card .stat-label {
    font-size: 16px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.dashboard-stat-card .stat-detail {
    font-size: 13px;
    opacity: 0.8;
}

.dashboard-stat-card .stat-footer {
    display: block;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.3);
    color: white;
    text-decoration: none;
    font-size: 14px;
}

.dashboard-stat-card .stat-footer:hover {
    opacity: 0.9;
    text-decoration: none;
    color: white;
}

/* Quick Action Cards */
.quick-action-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 15px;
    min-height: 100px;
}

.quick-action-card:hover {
    text-decoration: none;
    border-color: #84c5fe;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.quick-action-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

.quick-action-icon.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.quick-action-icon.bg-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.quick-action-icon.bg-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
}

.quick-action-icon.bg-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.quick-action-content h5 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 700;
    color: #333;
}

.quick-action-content p {
    margin: 0;
    font-size: 13px;
    color: #6c757d;
}

/* Mini Stat Cards */
.mini-stat-card {
    padding: 20px 15px;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 0;
}

.mini-stat-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.mini-stat-value {
    font-size: 24px;
    font-weight: bold;
    margin: 10px 0 5px 0;
}

.mini-stat-label {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
}

/* Chart Containers */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
    margin-top: 15px;
}

/* Activity Timeline */
.activity-timeline {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8f9fa;
    font-size: 16px;
}

.activity-content {
    flex: 1;
}

.activity-text {
    margin-bottom: 8px;
}

.activity-meta {
    display: flex;
    gap: 10px;
    align-items: center;
    font-size: 12px;
}

/* Color Themes */
.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
}

.bg-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.bg-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-stat-card {
        min-height: 160px;
    }
    
    .dashboard-stat-card .stat-value {
        font-size: 24px;
    }
    
    .quick-action-card {
        flex-direction: column;
        text-align: center;
        min-height: auto;
    }
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Enrollment Trend Chart
    const enrollmentData = <?php echo json_encode($enrollment_chart_data); ?>;
    new Chart(document.getElementById('enrollmentTrendChart'), {
        type: 'line',
        data: {
            labels: enrollmentData.map(d => d.date),
            datasets: [{
                label: 'Total Enrollments',
                data: enrollmentData.map(d => parseInt(d.enrollments)),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Paid Enrollments',
                data: enrollmentData.map(d => parseInt(d.paid_enrollments)),
                borderColor: '#56ab2f',
                backgroundColor: 'rgba(86, 171, 47, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Revenue Chart
    const revenueData = <?php echo json_encode($revenue_chart_data); ?>;
    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'bar',
        data: {
            labels: revenueData.map(d => d.month),
            datasets: [{
                label: 'Revenue (₹)',
                data: revenueData.map(d => parseFloat(d.revenue)),
                backgroundColor: 'rgba(245, 87, 108, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Chart
    const categoryData = <?php echo json_encode($category_distribution); ?>;
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: categoryData.map(d => d.category),
            datasets: [{
                data: categoryData.map(d => parseInt(d.course_count)),
                backgroundColor: ['#667eea', '#56ab2f', '#f5576c', '#4facfe', '#f093fb', '#a8e063']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Payment Status Chart
    const paymentData = <?php echo json_encode($payment_status_distribution); ?>;
    new Chart(document.getElementById('paymentStatusChart'), {
        type: 'pie',
        data: {
            labels: paymentData.map(d => d.payment_status),
            datasets: [{
                data: paymentData.map(d => parseInt(d.count)),
                backgroundColor: ['#56ab2f', '#f5576c', '#ffa726']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
<?php init_tail(); ?>