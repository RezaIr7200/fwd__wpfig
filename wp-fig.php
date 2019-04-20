<?php
/*
Plugin Name: Fartaak Custom Image Gallery for posts
Description: Adds a metabox for post image galleries like Woocommerce.
Version: 1.0.0
Author: Fartaak Web 
Author URI: http://fartaakweb.ir
Text Domain: wp-fig
@package wp-fig
*/

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

define('WPFIG_TABLE_NAME', 'wpfig_table');

// load our helper functions
//require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';

// required file for creating plugin table
//require_once plugin_dir_path(__FILE__) . 'includes/install.php';

// run the installation when activating the plugin
//register_activation_hook(__FILE__, 'wpfig_install');


/**
 * the wpfig
 */
class WP_fig
{
    /**
     * Static property to hold our singleton instance
     *
     */
    public static $instance = false;

    /**
     * hold our plugin table name
     */
    private $_tableName= null;

    private $_wpdb = null;

    private $_isAjax = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        global $wpdb;

        $this->_wpdb = $wpdb;
        $this->_tableName = $wpdb->prefix . WPFIG_TABLE_NAME;


        // is current request type is ajax?
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $this->isAjax = true;
        }

        // load textdomain
        add_action('init', array( $this, 'loadTextDomain'));
    
   
        // add admin script
        //add_action('admin_enqueue_scripts', array( $this, 'addAdminScripts'));
 
  

    }

    /**
     * If an instance exists, this returns it.  If not, it creates one and
     * retuns it.
     *
     * @return WP_fig
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * load textdomain
     *
     * @return void
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain('wp-fig', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Admin scripts
     *
     * @return void
     */
    public function addAdminScripts()
    {
        //wp_enqueue_style( 'tailwind-admin', plugins_url('admin/css/tailwind.min.css', __FILE__) );
        wp_enqueue_style('wp-fig-adminstyle', plugins_url('admin/css/admin.css', __FILE__));
        wp_enqueue_script('wp-fig-adminjs', plugins_url('admin/js/consulting-form.js', __FILE__));
    }



    /**
     * Check current user perms
     *
     * @return void
     */
    public function checkCurrentUser()
    {
        // ensure current user has enough perms to access this tool.
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }
    }


    
}


// Instantiate our class
$wp_fig = wp_fig::getInstance();



// Add the Meta Box
function wpfig_add_custom_meta_box() {
    add_meta_box(
        'custom_meta_box', // $id
        'Portfolio Gallery', // $title
        'wpfig_show_custom_meta_box', // $callback
        'post', // $page
        'side', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'wpfig_add_custom_meta_box');



// Field Array
$prefix = 'wpfig_';
$custom_meta_fields = array(

    array(
        'label'=> 'Gallery Images',
        'desc'  => 'This is the gallery images on the single item page.',
        'id'    => $prefix.'gallery',
        'type'  => 'gallery'
    ),
);


// The Callback
function wpfig_show_custom_meta_box($object) {
    global $custom_meta_fields, $post;
    // Use nonce for verification
    echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

    // Begin the field table and loop
  
    foreach ($custom_meta_fields as $field) {
            // get value of this field if it exists for this post
            $meta = get_post_meta($post->ID, $field['id'], true);


            // begin a table row with

    
            switch($field['type']) {

                    case 'gallery':
                    $meta_html = null;
                    if ($meta) {
               
                            $meta_html .= '<ul class="wpfig_gallery_list">';
                            $meta_array = explode(',', $meta);
                            foreach ($meta_array as $meta_gall_item) {
                                    $meta_html .= '<li><div class="wpfig_gallery_container"><span class="wpfig_gallery_close"><img id="' . esc_attr($meta_gall_item) . '" src="' . wp_get_attachment_thumb_url($meta_gall_item) . '"></span></div></li>';
                            }
                            $meta_html .= '</ul>';
                    }
                    echo '<input id="wpfig_gallery" type="hidden" name="wpfig_gallery" value="' . esc_attr($meta) . '" />
                    <span id="wpfig_gallery_src">' . $meta_html . '</span>
                    <div class="shift8_gallery_button_container"><input id="wpfig_gallery_button" type="button" class="components-button is-button is-default is-large widefat" value="Manage Gallery" /></div>';
                    break;
            } //end switch
          
    } // end foreach

}


function wpfig_save_meta($post_id)
{
    global $prefix;
    if (array_key_exists('wpfig_gallery', $_POST)) {
  
        update_post_meta(
            $post_id,
            $prefix . 'gallery',
            $_POST['wpfig_gallery']
        );
    }
}
add_action('save_post', 'wpfig_save_meta');




// Register admin scripts for custom fields
function wpfig_load_wp_admin_style() {
    wp_enqueue_media();
    wp_enqueue_script('media-upload');
    wp_enqueue_style( 'wpfig_admin_css', plugins_url('admin/css/admin.css', __FILE__) );
    wp_enqueue_script( 'wpfig_admin_script', plugins_url('admin/js/wp-fig.js', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'wpfig_load_wp_admin_style' );

