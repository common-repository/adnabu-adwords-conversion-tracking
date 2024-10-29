<br>
<div class="container">
    <div class="col-8 container">
            <a href="https://ads.google.com/">
                <img src="<?php $instance = new AdNabuAdwordsConversionTracking();
                echo $instance->app_dir_url . '/assets/images/Google_Ads_logo.svg';
                ?>"
                     alt="Google_Ads_logo"
                     style="width:20%">
            </a>
            <strong style ="font-size: 200%; text-align: center">Adwords Conversion Tracking Stats</strong>
    </div>
    <br>
    <?php
    if(isset($_POST["enable_tracker"])){
        $pixel_id = sanitize_text_field($_POST["enable_tracker"]);
        if(wp_verify_nonce($_POST["wp_nonce"], 'create_pixel_' . $pixel_id ) == 1){
            $instance->enable_new_pixel($pixel_id);
        }

    }
    if(isset($_POST["toggle"])){
        $pixel_id = sanitize_text_field($_POST["toggle"]);
        if(wp_verify_nonce($_POST["wp_nonce"], 'toggle_pixel_' . $pixel_id) == 1){
            $instance->flip_pixel_status($pixel_id);
        }

    }
    if(isset($_POST['delete'])){
        $pixel_id = sanitize_text_field($_POST['delete']);
        if(wp_verify_nonce($_POST["wp_nonce"], 'delete_pixel_' . $pixel_id) == 1){
            $instance->delete_pixel($pixel_id);
        }
    }

    $url = $instance->get_action_url("fetch-all");
    $pixel_json = $instance->fetch_pixel_json($url);
    $instance->sync_db_with_remote($pixel_json);
    $instance->show_table($pixel_json);
   ?>
    <div class="container">
            <span class="icon-input-btn">
            <button name="enable_tracker" class="btn btn-primary btn-default add-pixel"
                    type="submit" id=
                    <?php
                    $pixel_id = wp_generate_password(20,false,false);
                    $nonce = wp_create_nonce( 'create_pixel_'. $pixel_id );
                    echo $pixel_id;
                    ?>
                    value="new" onclick="enable_tracker(this.id,'<?php echo $instance->add_pixel_url() . '&pixel_id=' . $pixel_id?>', '<?php echo $nonce?>')">
                <i class="fas fa-plus"> Add A Pixel</i>
            </button>
        </span>
    </div>
</div>



