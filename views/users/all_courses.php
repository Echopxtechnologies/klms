<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
  .course-toolbar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:15px; }
  .course-grid { display:grid; grid-template-columns: repeat(1, 1fr); gap:16px; }
  @media (min-width: 576px){ .course-grid{ grid-template-columns: repeat(2, 1fr); } }
  @media (min-width: 992px){ .course-grid{ grid-template-columns: repeat(3, 1fr); } }
  .course-card { border:1px solid #e9ecef; border-radius:8px; overflow:hidden; background:#fff; display:flex; flex-direction:column; }
  .course-thumb { position:relative; width:100%; padding-top:56.25%; background:#f5f6f8; overflow:hidden; }
  .course-thumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  .badge-free { position:absolute; top:10px; left:10px; background:#28a745; color:#fff; padding:4px 8px; border-radius:4px; font-size:12px; }
  .badge-paid { position:absolute; top:10px; left:10px; background:#6c757d; color:#fff; padding:4px 8px; border-radius:4px; font-size:12px; }
  .course-body { padding:12px 14px; flex:1; display:flex; flex-direction:column; }
  .course-title { font-weight:600; font-size:16px; margin:0 0 6px; }
  .course-meta { font-size:12px; color:#6c757d; display:flex; gap:10px; flex-wrap:wrap; margin-bottom:8px; }
  .course-desc { font-size:13px; color:#495057; margin-bottom:10px; line-height:1.4; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
  .course-footer { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-top:1px solid #e9ecef; }
  .price { font-weight:700; }
  .btn-sm { padding:6px 10px; }
  .empty { padding:30px; text-align:center; color:#6c757d; border:1px dashed #e9ecef; border-radius:8px; }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="panel_s">
      <div class="panel-body">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" style="margin-bottom:10px;">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo site_url('lms/Lms_users/dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">All Courses</li>
          </ol>
        </nav>

        <h4 class="no-margin" style="margin-bottom:12px;"><?php echo html_escape($title ?? 'All Courses'); ?></h4>

        <!-- Toolbar: search + category -->
        <div class="course-toolbar">
          <div class="input-group" style="max-width:320px;">
            <input id="courseSearch" type="text" class="form-control" placeholder="Search courses...">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="document.getElementById('courseSearch').value=''; filterCourses();">
                Clear
              </button>
            </span>
          </div>

          <select id="categoryFilter" class="form-control" style="max-width:220px;" onchange="filterCourses()">
            <option value="">All categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo html_escape(strtolower($cat)); ?>">
                <?php echo html_escape($cat); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <div style="margin-left:auto;color:#6c757d;" id="resultCount"></div>
        </div>

        <?php if (empty($courses)): ?>
          <div class="empty">No courses found.</div>
        <?php else: ?>
          <div id="courseGrid" class="course-grid">
            <?php foreach ($courses as $c): ?>
              <?php
                $title   = trim($c['title'] ?? 'Untitled');
                $desc    = trim($c['description'] ?? '');
                $cat     = trim($c['category'] ?? '');
                $level   = trim($c['level'] ?? '');
                $dur     = trim($c['course_duration'] ?? '');
                $price   = isset($c['price']) ? (float)$c['price'] : 0.0;
                $is_free = !empty($c['_computed']['is_free']);
                $is_paid = !empty($c['_computed']['is_paid']);
                $can_watch = !empty($c['_computed']['can_watch']);

                // image
                $img = !empty($c['cover_image_abs'] ?? $c['cover_image'])
                     ? ($c['cover_image_abs'] ?? base_url(ltrim($c['cover_image'], '/')))
                     : 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="338"><rect width="100%" height="100%" fill="#f0f2f5"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#9aa0a6" font-size="18" font-family="Arial">No Image</text></svg>');
              ?>
              <div class="course-card"
                   data-title="<?php echo htmlspecialchars(strtolower($title), ENT_QUOTES); ?>"
                   data-cat="<?php echo htmlspecialchars(strtolower($cat), ENT_QUOTES); ?>">
                <div class="course-thumb">
                  <img src="<?php echo $img; ?>" alt="<?php echo html_escape($title); ?>">
                  <span class="<?php echo $is_free ? 'badge-free' : 'badge-paid'; ?>">
                    <?php echo $is_free ? 'Free' : 'Paid'; ?>
                  </span>
                </div>
                <div class="course-body">
                  <h5 class="course-title"><?php echo html_escape($title); ?></h5>
                  <div class="course-meta">
                    <?php if ($cat): ?><span><i class="fa fa-folder-open"></i> <?php echo html_escape($cat); ?></span><?php endif; ?>
                    <?php if ($level): ?><span><i class="fa fa-signal"></i> <?php echo html_escape($level); ?></span><?php endif; ?>
                    <?php if ($dur): ?><span><i class="fa fa-clock-o"></i> <?php echo html_escape($dur); ?></span><?php endif; ?>
                  </div>
                  <?php if ($desc): ?>
                    <div class="course-desc"><?php echo html_escape($desc); ?></div>
                  <?php endif; ?>
                </div>

                <div class="course-footer">
                  <div class="price">
                    <?php echo $is_free ? '₹0' : '₹'.number_format($price, 2); ?>
                  </div>
                  
                  <!-- Action Buttons -->
                    <?php if ($c['_computed']['can_watch']): ?>
                    <a href="<?php echo $c['urls']['watch_first']; ?>" class="btn btn-info btn-sm">Watch</a>
                    <?php elseif ($c['_computed']['is_paid']): ?>
                    <a href="<?php echo (is_client_logged_in() ? $c['urls']['purchase'] : $c['urls']['registration']); ?>" class="btn btn-warning btn-sm">Buy / Enroll</a>
                    <?php else: ?>
                    <a href="<?php echo $c['urls']['details']; ?>" class="btn btn-info btn-sm">View details</a>
                    <?php endif; ?>


                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var searchInput = document.getElementById('courseSearch');
  var catSelect   = document.getElementById('categoryFilter');
  var grid        = document.getElementById('courseGrid');
  var counter     = document.getElementById('resultCount');

  function normalize(s){ return (s||'').toLowerCase().trim(); }

  window.filterCourses = function(){
    if (!grid) return;
    var q = normalize(searchInput.value);
    var cat = normalize(catSelect.value);
    var cards = grid.querySelectorAll('.course-card');
    var shown = 0;

    cards.forEach(function(card){
      var title = card.getAttribute('data-title');
      var ccat  = card.getAttribute('data-cat');
      var matchQ   = !q  || (title && title.indexOf(q) !== -1);
      var matchCat = !cat || (ccat === cat);
      var show = matchQ && matchCat;
      card.style.display = show ? '' : 'none';
      if (show) shown++;
    });

    if (counter) {
      var total = cards.length;
      counter.textContent = shown + ' of ' + total + ' shown';
    }
  };

  // init
  filterCourses();
  searchInput.addEventListener('input', filterCourses);
})();
</script>
