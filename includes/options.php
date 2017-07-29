<?php
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
echo "hi";
exit;
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
       
       $html = '<input type="checkbox" id="checkbox1" name="my_plugin_options[checkbox1]" value="1"' . checked( 1, $this->options['checkbox1'], false ) . '/>';       
         print $html;              
    }
}
if( is_admin() )
$myoption= new MyOptions();
?>