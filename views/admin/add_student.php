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
                                            <a href="<?php echo admin_url('lms/Lms_admin/students'); ?>">
                                                <i class="fa fa-arrow-left"></i> All Students
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active">Add Student</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <?php echo form_open(admin_url('lms/Lms_admin/add_student')); ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname" class="control-label">
                                                <span class="text-danger">*</span> First Name
                                            </label>
                                            <input type="text" name="firstname" id="firstname" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname" class="control-label">
                                                <span class="text-danger">*</span> Last Name
                                            </label>
                                            <input type="text" name="lastname" id="lastname" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="control-label">
                                                <span class="text-danger">*</span> Email
                                            </label>
                                            <input type="email" name="email" id="email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phonenumber" class="control-label">Phone Number</label>
                                            <input type="text" name="phonenumber" id="phonenumber" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company" class="control-label">Company</label>
                                            <input type="text" name="company" id="company" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="control-label">Password</label>
                                            <input type="password" name="password" id="password" class="form-control" 
                                                   placeholder="Leave empty to auto-generate">
                                            <p class="help-block">If empty, a random password will be generated</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="country" class="control-label">Country</label>
                                            <input type="text" name="country" id="country" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city" class="control-label">City</label>
                                            <input type="text" name="city" id="city" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address" class="control-label">Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Student Settings</div>
                                    <div class="panel-body">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="send_welcome_email" value="1" checked> 
                                                Send Welcome Email
                                            </label>
                                            <p class="help-block">Send welcome email with login details</p>
                                        </div>

                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="auto_activate" value="1" checked> 
                                                Auto Activate Account
                                            </label>
                                            <p class="help-block">Activate account immediately</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('lms/Lms_admin/students'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-check"></i> Create Student
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

<?php init_tail(); ?>