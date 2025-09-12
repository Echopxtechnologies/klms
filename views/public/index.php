<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Video Gallery Section -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-video-camera"></i>
                    <?php echo _l('video_gallery'); ?>
                </h3>
            </div>
            <div class="panel-body">
                
                <!-- Primary Video Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="video-container mb-4">
                            <h4 class="video-title"><?php echo _l('echopx_technologies_overview'); ?></h4>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe 
                                    class="embed-responsive-item"
                                    src="https://player.vimeo.com/video/221533587?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                                    referrerpolicy="strict-origin-when-cross-origin" 
                                    title="<?php echo _l('echopx_technologies_video'); ?>"
                                    loading="lazy">
                                </iframe>
                            </div>
                            <div class="video-meta mt-2">
                                <small class="text-muted">
                                    <i class="fa fa-phone"></i> +91-8050-966-966 | 
                                    <i class="fa fa-envelope"></i> info@echopx.com | 
                                    <i class="fa fa-globe"></i> www.echopx.com
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Secondary Video Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="video-container">
                            <h4 class="video-title"><?php echo _l('world_rolling_presentation'); ?></h4>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe 
                                    class="embed-responsive-item"
                                    src="https://player.vimeo.com/video/1117709907?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                                    referrerpolicy="strict-origin-when-cross-origin" 
                                    title="<?php echo _l('world_rolling_video'); ?>"
                                    loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Video Gallery -->
<style>
.video-container {
    margin-bottom: 30px;
}

.video-title {
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.video-meta {
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #007bff;
}

.embed-responsive {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 4px;
    overflow: hidden;
}

.embed-responsive:hover {
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    transition: box-shadow 0.3s ease;
}

@media (max-width: 768px) {
    .video-container {
        margin-bottom: 20px;
    }
    
    .video-title {
        font-size: 18px;
    }
}
</style>