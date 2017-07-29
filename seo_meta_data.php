<?php 
/*
Plugin Name: SEO META DATA
Plugin URI:
seo_description: Add Title, Description, Keyword, Image Fields to every page and post
Version: 1.0.1
Author: Abolfath
Author URI: http://www.stradigi.ca/
License: GPL
Copyright: Abolfath
*/
namespace My_Plugin{
    class MyOptions{
    /**
     * Holds the values to be used in the fields callbacks
     */
    public $options ;

    public function __construct()
    {
        // add plugin setting to dashbord
        register_activation_hook( __FILE__, array( $this, 'update_setting' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
public function update_setting(){
        $temp_option=get_option( "my_plugin_options" ,false);      
        if(!is_array($temp_option)){           
            $arr= array(
                'checkbox1'=>''
            );
            update_option("my_plugin_options",$arr);
        }

     }
     /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        $page_title = 'SEO SETTING';
    $menu_title = 'SEO_META_DATA';
    $capability = 'manage_options';
    $menu_slug = 'seo_setting_page';
    $function = array( $this,'create_admin_page');
    $icon_url = '';
    $position = 99;
   add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );      
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        
        // Set class property
       // var_dump(get_option( 'my-option_name' ));
        $this->options = get_option( 'my_plugin_options' );
        ?>
        <div class="wrap">           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields                
                settings_fields( 'my_plugin_options' );
                do_settings_sections( 'seo_setting_page' );
               submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_plugin_options', // Option group
            'my_plugin_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'first_section', // ID
            'SEO Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'seo_setting_page' // Page
        );  

        add_settings_field(
            'checkbox1', // ID
            'Allow SEO Disabling', // Title 
            array( $this, 'checkbox1_callback' ), // Callback
            'seo_setting_page', // Page
            'first_section' // Section           
        );     

       //  wp_cache_delete ( 'alloption', 'options' ); 
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
       if( isset( $input['checkbox1'] ) )
            $new_input['checkbox1'] = absint( $input['checkbox1'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {     
        
        print '';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function checkbox1_callback()
    {
       $html='<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">';
       $html.='<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>';
       $html .= '<div style="background-color:inherit;"> <input type="checkbox" id="checkbox1"  name="my_plugin_options[checkbox1]" value="1"';
        $html .= checked( 1, $this->options['checkbox1'], false ) .' data-toggle="toggle" data-on="Yes" data-off="No" data-onstyle="primary" data-offstyle="danger"/> </div>';
      // $html .= '<input type="checkbox" id="checkbox1" name="my_plugin_options[checkbox1]" value="1"' . checked( 1, $this->options['checkbox1'], false ) . '/>';       
         print $html;              
    }
}
if( is_admin() )
$myoption= new MyOptions();

// check for class
if( !class_exists('FieldAdder') ):
class FieldAdder
{
    public function __construct()
    {
        //include class options
        // includes
		//$this->include_before_theme();
        // add custom fields
        add_action( 'add_meta_boxes', array( $this,'seo_meta_box_add' ));
        // enqueue scripts for loading image from media library
        add_action('admin_enqueue_scripts', array( $this,'my_enqueue_media_lib_uploader'));
        // enqueue styles for editor fields
        add_action('admin_enqueue_scripts', array( $this,'my_enqueue_style_seo'));
        //save fields
       add_action( 'save_post', array( $this,'cfadd_meta_box_save' ));
       // make fields protected so it not appear twice in edit pannel
       add_filter('is_protected_meta', array( $this,'my_is_protected_meta_filter'), 10, 2);
    }
 

    function include_before_theme()
	{
		// incudes
		include_once( 'includes/options.php' );
        
    }
    
// hide elements from showing in the custom fields
function my_is_protected_meta_filter($protected, $meta_key) {
    if( in_array($meta_key, array('seo_title', 'seo_desc', 'seo_keyword','seo_image_url','seo_image_url_holder','seo_disabled')) ){
    return true;
}
return $protected;
}
// Step 1
// Add custom Input Box field to every 'Edit Page' and post

public function seo_meta_box_add() {
    $id='my-meta-box-id';
    $seo_title = 'seo';
    $callback = array( $this,'cfadd_meta_box_cb');
    $Screens = array('post','page');    
    $context = 'normal';
    $priority = 'high';
    $callback_args = null ;
    foreach($Screens as $Screen){
        add_meta_box( $id, $seo_title, $callback, $Screen, $context, $priority );
    }
    
}
public function cfadd_meta_box_cb( $post ) {
    $values = get_post_custom( $post->ID );
    $seo_title = isset( $values['seo_title'] ) ? esc_attr( $values['seo_title'][0] ) : '';
    $seo_desc = isset( $values['seo_desc'] ) ? esc_attr( $values['seo_desc'][0] ) : '';
    $seo_keyword = isset( $values['seo_keyword'] ) ? esc_attr( $values['seo_keyword'][0] ) : '';
    $seo_image_url_holder = isset( $values['seo_image_url_holder'] ) ? esc_attr( $values['seo_image_url_holder'][0] ) : '';
    $seo_chk_unchk = ( $values['seo_disabled'][0]=="1" ) ? ' checked="checked"' : "";    
    ?>
    <form method="post" action="#">
    <?php
    // check to see if allowed to show disable fields
      if(get_option( 'my_plugin_options' )['checkbox1']=="1"){
      $this->show_check_field($seo_chk_unchk);
      // check to see if we want to add other fields or not
      if($values['seo_disabled'][0]!="1") 
     {$this->show_other_fields($seo_title,$seo_desc,$seo_keyword,$seo_image_url_holder);}
      }else // here we only show the other fields 
      {$this->show_other_fields($seo_title,$seo_desc,$seo_keyword,$seo_image_url_holder);}
      ?>
      
     
    <?php wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' ); ?>
    </form>
    <?php   
}
public function show_check_field($seo_chk_unchk){
$html='<div class="myfont right">
          <label class="mylable" for="seo_disabled">Disable SEO: </label>
          <input type="checkbox" name="seo_disabled" id="seo_disabled" value="1" %1s />
      </div>';
      printf($html,$seo_chk_unchk);
}
public function show_other_fields( $seo_title,$seo_desc,$seo_keyword,$seo_image_url_holder){
        
    $html='
      <div class="myfont">
          <label class="mylable"  for="seo_title">Title: </label>
          <input class="test-bg-red" type="text" name="seo_title" id="seo_title" value="%1s" />
      </div>    
    <div><hr /><div>
     <div class="myfont">
        <label  class="mylable" for="seo_desc">Description: </label>
    </div>     
     <div class="myfont">
        <textarea class="fullwidth"  name="seo_desc" id="seo_desc" rows="6"   />%2s</textarea>
    </div>
    <div><hr /><div>
     <div class="myfont">
        <label class="mylable" for="seo_keyword">Keyword: </label>
        <input type="text" name="seo_keyword" id="seo_keyword" value="%3s" />
    </div>
    <div><hr /><div>
    <div class="myfont" style="display:flex; align-items:flex-start">
    <label class="mylable" for="upload-button">Upload un image:</label>  
    <input id="upload-button" type="button" class="button" value="Select Image" />    
    </div>
    <div><hr /><div>
    <div>
    <input type="text" name="seo_image_url_holder" id="seo_image_url_holder" hidden="hidden" value="%4s" />
    <img id="seo_image_url" style="width:200px; height:200px"  name="seo_image_url" src="%5s" alt="selected-image" />
    </div>';
    
    printf ($html, $seo_title,$seo_desc,$seo_keyword,$seo_image_url_holder,$seo_image_url_holder);
}
public function my_enqueue_media_lib_uploader() {

    //Core media script
    wp_enqueue_media();

    // Your custom js file
    wp_register_script( 'media-lib-uploader-js', plugins_url( 'includes/media-lib-uploader.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'media-lib-uploader-js' );
}
public function my_enqueue_style_seo() {

    // Your custom css file    
    wp_register_style( 'my_plugin_seo_style', plugins_url( 'includes/style.css' , __FILE__ ) );
    wp_enqueue_style( 'my_plugin_seo_style' );
}
public function cfadd_meta_box_save( $post_id ) {
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;
    // now we can actually save the data
    $allowed = array( 
        'a' => array( // on allow a tags
            'href' => array() // and those anchords can only have href attribute
        )
    );
    
    // Probably a good idea to make sure your data is set
    if( isset( $_POST['seo_title'] ) )
        update_post_meta( $post_id, 'seo_title', $_POST['seo_title'] );
    if( isset( $_POST['seo_desc'] ) )
        update_post_meta( $post_id, 'seo_desc', $_POST['seo_desc'] );
    if( isset( $_POST['seo_keyword'] ) )
        update_post_meta( $post_id, 'seo_keyword', $_POST['seo_keyword'] );
    if( isset( $_POST['seo_image_url_holder'] ) )
       update_post_meta( $post_id, 'seo_image_url_holder', $_POST['seo_image_url_holder'] );
    $my_checkbox=$_POST['seo_disabled']?true:false;
    update_post_meta( $post_id, 'seo_disabled', $my_checkbox );
}
}
function FieldAdder()
{
	global $FieldAdder;
	
	if( !isset($FieldAdder) )
	{
		$FieldAdder = new FieldAdder();
	}
	
	return $FieldAdder;
}

// initialize
FieldAdder();

endif; // class_exists check
}
?>