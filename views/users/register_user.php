<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php get_template_part('alerts'); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        
        <!-- Registration Container -->
        <div class="registration-container">
          <div class="registration-card">
            
            <!-- Header Section -->
            <div class="registration-header">
              <div class="header-icon">
                <i class="fa fa-user-plus"></i>
              </div>
              <h2>Create Your Account</h2>
              <p>Join our learning platform and start your journey today</p>
            </div>

            <!-- Form Section -->
            <div class="registration-body">
            <?php
            $base = isset($module_base_url) ? $module_base_url : 'lms/Lms_users';
            $cid = isset($course_id) ? (int)$course_id : 0;
            $action = site_url($base . '/registration/' . $cid);
            $form_token = bin2hex(random_bytes(16));
            $this->session->set_userdata('expected_form_token', $form_token);
            ?>
            
            <?= form_open($action, ['id' => 'registration-form', 'class' => 'registration-form']); ?>       
            <input type="hidden" name="course_id" value="<?= $cid; ?>">
            <input type="hidden" name="form_token" value="<?= $form_token; ?>">


              <!-- Personal Information -->
              <div class="form-section">
                <h4 class="section-title">
                  <i class="fa fa-user"></i> Personal Information
                </h4>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        <span class="text-danger">*</span> First Name
                      </label>
                      <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input type="text" 
                               name="firstname" 
                               class="form-control" 
                               required 
                               placeholder="Enter your first name"
                               value="<?php echo set_value('firstname'); ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        <span class="text-danger">*</span> Last Name
                      </label>
                      <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input type="text" 
                               name="lastname" 
                               class="form-control" 
                               required 
                               placeholder="Enter your last name"
                               value="<?php echo set_value('lastname'); ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Contact Information -->
              <div class="form-section">
                <h4 class="section-title">
                  <i class="fa fa-envelope"></i> Contact Information
                </h4>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        <span class="text-danger">*</span> Email Address
                      </label>
                      <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input type="email" 
                               name="email" 
                               id="email"
                               class="form-control" 
                               required 
                               placeholder="your@email.com"
                               value="<?php echo set_value('email'); ?>">
                      </div>
                      <small class="help-block">We'll send your login credentials to this email</small>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Phone Number</label>
                      <div class="input-icon">
                        <i class="fa fa-phone"></i>
                        <input type="text" 
                               name="phonenumber" 
                               class="form-control" 
                               placeholder="+91 9876543210"
                               value="<?php echo set_value('phonenumber'); ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Company/Organization</label>
                      <div class="input-icon">
                        <i class="fa fa-building"></i>
                        <input type="text" 
                               name="company" 
                               class="form-control" 
                               placeholder="Your company name"
                               value="<?php echo set_value('company'); ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        <span class="text-danger">*</span> Password
                      </label>
                      <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               id="password"
                               required 
                               class="form-control" 
                               placeholder="Enter the Password">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Location Information -->
              <div class="form-section">
                <h4 class="section-title">
                  <i class="fa fa-map-marker"></i> Location (Optional)
                </h4>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Country</label>
                      <div class="input-icon">
                        <i class="fa fa-globe"></i>
                        <input type="text" 
                               name="country" 
                               class="form-control" 
                               placeholder="India"
                               value="<?php echo set_value('country'); ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">City</label>
                      <div class="input-icon">
                        <i class="fa fa-map-marker"></i>
                        <input type="text" 
                               name="city" 
                               class="form-control" 
                               placeholder="Bangalore"
                               value="<?php echo set_value('city'); ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Address</label>
                  <div class="input-icon">
                    <i class="fa fa-home"></i>
                    <textarea name="address" 
                              rows="3" 
                              class="form-control" 
                              placeholder="Enter your complete address"><?php echo set_value('address'); ?></textarea>
                  </div>
                </div>
              </div>


              <!-- Action Buttons -->
              <div class="form-actions">
                <a href="<?php echo site_url('lms/Lms_users/'); ?>" class="btn btn-default btn-lg">
                  <i class="fa fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                  <i class="fa fa-check"></i> Create Account
                </button>
              </div>

              <!-- Login Link -->
              <div class="login-link">
                Already have an account? <a href="<?php echo site_url('authentication/login'); ?>">Login here</a>
              </div>

              <?php echo form_close(); ?>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Styles -->
<style>
/* Registration Container */
.registration-container {
  max-width: 900px;
  margin: 40px auto;
  padding: 0 15px;
}

.registration-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.1);
  overflow: hidden;
}

/* Header */
.registration-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  text-align: center;
  padding: 40px 30px;
}

.header-icon {
  width: 80px;
  height: 80px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 36px;
}

.registration-header h2 {
  margin: 0 0 10px 0;
  font-size: 2rem;
  font-weight: 700;
  color: #fff;
}

.registration-header p {
  margin: 0;
  font-size: 1.1rem;
  opacity: 0.9;
}

/* Form Body */
.registration-body {
  padding: 40px 30px;
}

.registration-form {
  max-width: 100%;
}

/* Form Sections */
.form-section {
  margin-bottom: 35px;
  padding-bottom: 30px;
  border-bottom: 1px solid #e9ecef;
}

.form-section:last-of-type {
  border-bottom: none;
  margin-bottom: 25px;
}

.section-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.section-title i {
  color: #667eea;
  font-size: 1.3rem;
}

/* Form Groups */
.form-group {
  margin-bottom: 20px;
}

.control-label {
  font-weight: 600;
  color: #495057;
  margin-bottom: 8px;
  display: block;
}

/* Input Icons */
.input-icon {
  position: relative;
}

.input-icon i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  font-size: 16px;
  z-index: 1;
}

.input-icon .form-control {
  padding-left: 45px;
}

.input-icon textarea.form-control {
  padding-left: 45px;
  padding-top: 12px;
}

/* Form Controls */
.form-control {
  height: 45px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.15);
}

textarea.form-control {
  height: auto;
  min-height: 100px;
}

/* Help Text */
.help-block {
  font-size: 13px;
  color: #6c757d;
  margin-top: 5px;
  margin-bottom: 0;
}

/* Checkbox */
.checkbox {
  margin-bottom: 0;
}

.checkbox label {
  font-weight: normal;
  cursor: pointer;
}

.checkbox input[type="checkbox"] {
  margin-right: 8px;
  transform: scale(1.2);
}

.checkbox a {
  color: #667eea;
  text-decoration: none;
}

.checkbox a:hover {
  text-decoration: underline;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 30px;
}

.btn-lg {
  padding: 12px 30px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-default {
  background: #f8f9fa;
  border: 2px solid #e9ecef;
  color: #495057;
}

.btn-default:hover {
  background: #e9ecef;
  transform: translateY(-2px);
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: #fff;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a4193 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102,126,234,0.4);
}

/* Login Link */
.login-link {
  text-align: center;
  margin-top: 25px;
  padding-top: 25px;
  border-top: 1px solid #e9ecef;
  color: #6c757d;
}

.login-link a {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .registration-container {
    margin: 20px auto;
  }

  .registration-header {
    padding: 30px 20px;
  }

  .registration-header h2 {
    font-size: 1.6rem;
  }

  .registration-body {
    padding: 30px 20px;
  }

  .form-actions {
    flex-direction: column;
  }

  .form-actions .btn {
    width: 100%;
  }
}

/* Loading State */
.btn-loading {
  position: relative;
  pointer-events: none;
  opacity: 0.7;
}

.btn-loading::after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-left: -8px;
  margin-top: -8px;
  border: 2px solid #fff;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spinner 0.6s linear infinite;
}

@keyframes spinner {
  to { transform: rotate(360deg); }
}
</style>

<script>
let formSubmitting = false;
let emailTaken = false;

$('#registration-form').on('submit', function(e) {
  if (formSubmitting) { e.preventDefault(); return false; }
  if (emailTaken) {
    e.preventDefault();
    showEmailError('This email is already registered. Please use a different email.');
    $('#email').focus();
    return false;
  }
  formSubmitting = true;
  $('#submit-btn').addClass('btn-loading').prop('disabled', true);
  return true;
});

$(document).ready(function() {
    'use strict';

    let formSubmitting = false;

    // Email validation and check
    let emailTimeout;
    $('#email').on('input', function() {
        clearTimeout(emailTimeout);
        const email = $(this).val();
        
        if (email.length > 5 && validateEmail(email)) {
            emailTimeout = setTimeout(function() {
                checkEmailExists(email);
            }, 500);
        }
    });

    // Form submission - prevent double submit
    $('#registration-form').on('submit', function(e) {
        if (formSubmitting) {
            e.preventDefault();
            return false;
        }
        
        formSubmitting = true;
        $('#submit-btn').addClass('btn-loading').prop('disabled', true);
        
        // Let form submit normally
        return true;
    });

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function checkEmailExists(email) {
  $.ajax({
    url: '<?php echo site_url("lms/Lms_users/check_email_exists"); ?>',
    type: 'POST',
    dataType: 'json', // <-- important
    data: {
      email: email,
      <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    },
    success: function(res) {
      if (res && res.exists) {
        emailTaken = true;
        $('#email').addClass('is-invalid');
        showEmailError(
          'This email is already registered. <a href="<?php echo site_url("authentication/login"); ?>">Login here</a>'
        );
      } else {
        emailTaken = false;
        $('#email').removeClass('is-invalid');
        removeEmailError();
      }
    },
    error: function() {
      // optional: don’t block user if the check fails
      emailTaken = false;
      removeEmailError();
    }
  });
}


    function showEmailError(message) {
        removeEmailError();
        $('#email').closest('.form-group').append(
            '<div class="email-error-msg alert alert-danger" style="margin-top: 10px; padding: 8px 12px; font-size: 13px;">' + 
            message + 
            '</div>'
        );
    }

    function removeEmailError() {
        $('.email-error-msg').remove();
    }
});
</script>