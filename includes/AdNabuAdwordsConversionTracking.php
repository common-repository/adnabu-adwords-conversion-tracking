<?php
/**
 * Created by PhpStorm.
 * User: Mahaveer Chouhan
 * Date: 12/10/18
 * Time: 6:20 PM
 */


class AdNabuAdwordsConversionTracking extends AdNabuPixelBase {
    public static $app_prefix = "adnabu_woocommerce_adwords_conversion_tracking_";
    public static $app_id = 'GOOGLE_ADS_CONVERSION_TRACKING';
    public static $app_version = "1.0.0";
    public static $app_name = "AdNabu Adwords Conversion Tracking";
    public $pixel_table;

    function __construct() {
        $this->pixel_table =  $this->get_app_db_prefix() . "pixels";
        $this->app_dir = plugin_dir_path( dirname( __FILE__, 1 ) );
        $this->app_dir_url = plugins_url(basename(dirname(__FILE__,2)));
    }

    function activate_app(){
        $this->create_pixel_table();
        set_transient($this::$app_name, 1, 5);
        self::activate();
    }


    function create_pixel_table(){
        $create_pixel_table_query = "
            CREATE TABLE IF NOT EXISTS `{$this->pixel_table}` (
              ID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `pixel` text NOT NULL,
              `status` TinyInt(1)
            )";
        self::create_table($create_pixel_table_query);
    }

    function enqueue_pixel_scripts(){
        $pixels = $this->read_pixel_list_from_db(1);
        foreach ($pixels as $pixel) {
            if (wp_script_is($pixel)) {
                return;
            }
            else {
                $script_url = "https://storage.googleapis.com/adnabu-woocommerce/conversion-pixels/$pixel.js";
                $script_array = array("gtag_$pixel" =>"https://storage.googleapis.com/adnabu-woocommerce/global-site-tags/$pixel.js");

                if(is_order_received_page()){
                    $script_array = $script_array + array($pixel => $script_url);
                }
                $this->enqueue_scripts($script_array);
            }
        }
    }


    function settings_link($links){
        $settings_link = '<a href="admin.php?page=adnabu-conversion-tracking">Home</a>';
        array_push($links, $settings_link);
        return $links;
    }


    function admin_index(){
        require_once  $this->app_dir . '/templates/app_home.php' ;
    }


    function add_app_page(){
        add_submenu_page('adnabu_plugin',
            'Conversion Tracking',
            'Conversion Tracking',
            'manage_options',
            'adnabu-conversion-tracking',
            array($this, 'admin_index'));
    }


    function enqueue_admin_assets($hook){
        if($hook == 'adnabu_page_adnabu-conversion-tracking'){
            $this->enqueue_base_assets();
            $this->enqueue_app_assets($this::$app_prefix);
        }
    }


    function expose_parameters(){
        $exposer_array = array('page_type' => self::wc_page_type());
        if (is_order_received_page()) {
            $order_id = wc_get_order_id_by_order_key($_GET['key']);
            $order = wc_get_order($order_id);

            $exposer_array2 = array('order' =>
                array(
                    'value' => $order->get_subtotal(),
                    'id' => $order_id,
                    'currency' => $order->get_currency(),
                )
            );
            $exposer_array = $exposer_array + $exposer_array2;
        }
        $this->localise_enqueue_scripts(
            "checkout_handle",
            $exposer_array,
            'adnabu_conversion_data');
        $this->enqueue_pixel_scripts();
    }

    public  static function uninstall_app(){
        parent::uninstall(self::$app_prefix);
    }
}