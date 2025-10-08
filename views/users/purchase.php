<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$courseId   = (int)($course['id'] ?? 0);
$title      = trim($course['title'] ?? 'Selected Course');
$price      = (float)($course['price'] ?? 0);
$isFree     = ($course['is_free'] ?? 0) == 1 || $price <= 0;

// Optional tax calc (adjust or remove if you don’t charge tax)
$taxRatePct = 0;               // e.g. 18 for 18%
$taxAmount  = $price * ($taxRatePct / 100);
$total      = $price + $taxAmount;

$backUrl    = site_url('klms/lms_users/view_course/' . $courseId);
$successUrl = site_url('klms/lms_users/payment_success/' . $courseId);
?>

<div class="content">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">

      <div class="checkout-card">
        <!-- Header -->
        <div class="checkout-header">
          <div class="row">
            <div class="col-sm-8">
              <h3 class="checkout-title">
                <i class="fa fa-shopping-bag"></i> Checkout
              </h3>
              <p class="text-muted m0">Complete your purchase to start learning right away.</p>
            </div>
            <div class="col-sm-4 text-right">
              <a href="<?php echo $backUrl; ?>" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to course
              </a>
            </div>
          </div>
        </div>

        <div class="checkout-body">
          <div class="row">
            <!-- Left: Course summary -->
            <div class="col-sm-7">
              <div class="panel_s">
                <div class="panel-body">
                  <div class="media">
                    <div class="media-left hidden-xs">
                      <div class="thumb-placeholder">
                        <i class="fa fa-graduation-cap"></i>
                      </div>
                    </div>
                    <div class="media-body">
                      <h4 class="m0"><?php echo html_escape($title); ?></h4>
                      <ul class="list-unstyled text-muted mtop10">
                        <?php if (!empty($course['category'])): ?>
                          <li><i class="fa fa-tag"></i> <?php echo html_escape($course['category']); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($course['level'])): ?>
                          <li><i class="fa fa-signal"></i> <?php echo html_escape($course['level']); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($course['course_duration'])): ?>
                          <li><i class="fa fa-clock-o"></i> <?php echo html_escape($course['course_duration']); ?></li>
                        <?php endif; ?>
                      </ul>
                      <?php if (!empty($course['description'])): ?>
                        <p class="text-muted mtop10">
                          <?php echo html_escape(mb_strimwidth(strip_tags($course['description']), 0, 220, '…')); ?>
                        </p>
                      <?php endif; ?>
                    </div>
                  </div>

                  <?php if ($isFree): ?>
                    <div class="alert alert-success mtop15">
                      <i class="fa fa-gift"></i> This is a free course. Click **Enroll Now** to start learning.
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Right: Order total -->
            <div class="col-sm-5">
              <div class="panel_s">
                <div class="panel-body">
                  <h4 class="m0 m-b-15">Order Summary</h4>

                  <div class="order-row">
                    <span>Course price</span>
                    <strong>₹<?php echo number_format($price, 2); ?></strong>
                  </div>

                  <?php if ($taxRatePct > 0): ?>
                    <div class="order-row">
                      <span>Tax (<?php echo (float)$taxRatePct; ?>%)</span>
                      <strong>₹<?php echo number_format($taxAmount, 2); ?></strong>
                    </div>
                  <?php endif; ?>

                  <hr class="m-t-10 m-b-10">

                  <div class="order-row total">
                    <span>Total</span>
                    <strong>₹<?php echo number_format($total, 2); ?></strong>
                  </div>

                  <div class="m-t-20">
                    <?php if ($isFree): ?>
                      <!-- Free enrollment -->
                      <a href="<?php echo $successUrl; ?>" class="btn btn-success btn-lg btn-block">
                        <i class="fa fa-check"></i> Enroll Now
                      </a>
                    <?php else: ?>
                      <!-- Simulated success for testing -->
                      <a href="<?php echo $successUrl; ?>" class="btn btn-success btn-lg btn-block m-b-5">
                        <i class="fa fa-check"></i> Simulate Payment Success
                      </a>

                      <!-- Real gateway buttons (placeholders) -->
                      <button type="button" class="btn btn-primary btn-block" id="pay-razorpay">
                        <i class="fa fa-credit-card"></i> Pay with Razorpay
                      </button>
                      <button type="button" class="btn btn-default btn-block" id="pay-stripe">
                        <i class="fa fa-cc-stripe"></i> Pay with Stripe
                      </button>

                      <p class="text-muted text-center mtop10 fs12">
                        By continuing, you agree to our Terms &amp; Refund Policy.
                      </p>
                    <?php endif; ?>
                  </div>

                </div>
              </div>

              <div class="panel_s">
                <div class="panel-body fs12 text-muted">
                  <i class="fa fa-lock"></i> Payments are processed over a secure encrypted connection.
                </div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /checkout-card -->

    </div>
  </div>
</div>

<style>
.checkout-card{background:#fff;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.07);overflow:hidden}
.checkout-header{padding:18px 22px;border-bottom:1px solid #eee;background:#fafbfd}
.checkout-title{margin:0 0 4px;font-weight:700}
.checkout-body{padding:22px}

.thumb-placeholder{width:90px;height:60px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9aa0a6}
.order-row{display:flex;align-items:center;justify-content:space-between;margin:6px 0}
.order-row.total{font-size:16px}
.fs12{font-size:12px}
.m-b-15{margin-bottom:15px}
.m-b-5{margin-bottom:5px}
</style>

<script>
// Hook up your real gateways here
document.addEventListener('DOMContentLoaded', function(){
  var rz = document.getElementById('pay-razorpay');
  var st = document.getElementById('pay-stripe');

  if (rz) rz.addEventListener('click', function(){
    // TODO: create Razorpay order with AJAX, then open checkout.js
    alert('Razorpay integration placeholder. Create order server-side, then open the checkout.');
  });

  if (st) st.addEventListener('click', function(){
    // TODO: create Stripe Checkout Session via AJAX, then redirect to session.url
    alert('Stripe integration placeholder. Create session server-side, then redirect.');
  });
});
</script>
