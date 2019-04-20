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

    private $_metaFields = null;

    private $_prefix = 'wpfig_';

    /**
     * Class constructor
     */
    public function __construct()
    {
        global $wpdb;

        $this->_wpdb = $wpdb;
        $this->_tableName = $wpdb->prefix . WPFIG_TABLE_NAME;


        $this->_metaFields = array(
            array(
                'label'=> 'Gallery Images',
                'desc'  => 'This is the gallery images on the single item page.',
                'id'    => $this->_prefix.'gallery',
                'type'  => 'gallery'
            ),
        );

        // is current request type is ajax?
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $this->isAjax = true;
        }

        add_action( 'admin_enqueue_scripts', array( $this, 'addAdminScripts' ));



        // load textdomain
        add_action('init', array( $this, 'loadTextDomain'));
    

        add_action('add_meta_boxes', array( $this, 'addMetaBox'));
        add_action('save_post',  array($this,'saveMeta'));
        
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
    public function addAdminScripts(){
        wp_enqueue_media();
        wp_enqueue_script('media-upload');
        wp_enqueue_style( 'wpfig_admin_css', plugins_url('admin/css/admin.css', __FILE__) );
        wp_enqueue_script( 'wpfig_admin_script', plugins_url('admin/js/wp-fig.js', __FILE__) );
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


    // Add the Meta Box
    public function addMetaBox() {
        add_meta_box(
            'custom_meta_box', // $id
            'Portfolio Gallery', // $title
            array($this,'renderMetabox'), // $callback
            'post', // $page
            'side', // $context
            'high'
        ); // $priority
    }

    public function renderMetabox($object){

        global  $post;
        // Use nonce for verification
        echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

        // Begin the field table and loop
    
        foreach ($this->_metaFields as $field) {
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
                                        $meta_html .= '<li><div class="wpfig_gallery_container"><button class="wpfig_gallery_remove">❌</button><img id="' . esc_attr($meta_gall_item) . '" src="' . wp_get_attachment_thumb_url($meta_gall_item) . '"></div></li>';
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

    /**
     * 
     */
    public function saveMeta($post_id)
    {
        if (array_key_exists($this->_prefix . 'gallery', $_POST)) {
            
            update_post_meta(
                $post_id,
                $this->_prefix . 'gallery',
                $_POST[$this->_prefix . 'gallery']
            );
        }
    }

}


// Instantiate our class
$wp_fig = wp_fig::getInstance();


