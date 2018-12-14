<?php
if(!class_exists('CTA_Banner'))
{
	/**
	 * A PostTypeTemplate class that provides 1 additional meta fields
	 */
	class CTA_Banner
	{
		const POST_TYPE	= "cta-banner";
		private $_meta	= array(
			'cta-banner-url',
		);
		
    	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
    		add_action('init', array(&$this, 'init'));
    		add_action('admin_init', array(&$this, 'admin_init'));
    	} // END public function __construct()

    	/**
    	 * hook into WP's init action hook
    	 */
    	public function init()
    	{
    		// Initialize Post Type
    		$this->create_post_type();
            
            // Adding custom imagem size (cta-banner-size)
            add_image_size( 'cta-banner-size', 960, 300, true ); // hard crop mode

            //Adding the [cta-banner] shortcode
            add_shortcode(self::POST_TYPE, array($this, 'cta_rockcontent_shortcode_show_html'));

        
    		add_action('save_post', array(&$this, 'save_post'));
    	} // END public function init()

    	/**
    	 * Create the post type
    	 */
    	public function create_post_type()
    	{
    		register_post_type(self::POST_TYPE,
    			array(
                    'label'                 => __( 'CTA', 'cta-rockcontent' ),
                    'description'           => __( 'Call to Action', 'cta-rockcontent' ),
    				'labels' => array(
                        'name'                  => _x( "CTA's", 'Post Type General Name', 'cta-rockcontent' ),
                        'singular_name'         => _x( 'CTA', 'Post Type Singular Name', 'cta-rockcontent' ),
                        'menu_name'             => __( "CTA's", 'cta-rockcontent' ),
                        'name_admin_bar'        => __( 'CTA', 'cta-rockcontent' )
                    ),
    				'supports'              => array( 'title', 'thumbnail' ),
                    'hierarchical'          => false,
                    'public'                => true,
                    'show_ui'               => true,
                    'show_in_menu'          => true,
                    'menu_position'         => 5,
                    'show_in_admin_bar'     => false,
                    'show_in_nav_menus'     => true,
                    'can_export'            => true,
                    'has_archive'           => true,
                    'exclude_from_search'   => false,
                    'publicly_queryable'    => false,
                    'menu_icon'             => 'dashicons-admin-comments',
                    'capability_type'       => 'post'
    			)
    		);
    	}
	
    	/**
    	 * Save the metaboxes for this custom post type
    	 */
    	public function save_post($post_id)
    	{
            // verify if this is an auto save routine. 
            // If it is our form has not been submitted, so we dont want to do anything
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            {
                return;
            }
            
    		if(isset($_POST['post_type']) && $_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    		{
    			foreach($this->_meta as $field_name)
    			{
    				// Update the post's meta field
    				update_post_meta($post_id, $field_name, $_POST[$field_name]);
    			}
    		}
    		else
    		{
    			return;
    		} // if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    	} // END public function save_post($post_id)

    	/**
    	 * hook into WP's admin_init action hook
    	 */
    	public function admin_init()
    	{			
    		// Add metaboxes
    		add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
    	} // END public function admin_init()
			
    	/**
    	 * hook into WP's add_meta_boxes action hook
    	 */
    	public function add_meta_boxes()
    	{
    		// Add this metabox to every selected post
    		add_meta_box( 
    			sprintf('wp_plugin_template_%s_section', self::POST_TYPE),
    			sprintf('URL'),
    			array(&$this, 'add_inner_meta_boxes'),
    			self::POST_TYPE
    	    );					
    	} // END public function add_meta_boxes()

		/**
		 * called off of the add meta box
		 */		
		public function add_inner_meta_boxes($post)
		{		
			// Render the job order metabox
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));			
		} // END public function add_inner_meta_boxes($post)

        
        /**
         * Shortcode function to return banner in html elements
         */
        public function cta_rockcontent_shortcode_show_html( $atts ){
            //Verify if array $atts has the 'id' param, and if is not empty and has a numeric value
            if(!isset($atts['id']) || empty($atts['id']) || !is_numeric($atts['id'])) { 
                return;
            }

            $cta = get_post($atts['id']);
            if(!$cta){
                return __("#ERROR: No CTA's has found with id=" . $atts['id'], 'cta-rockcontent');
            }
            
            //Check if the post has thumbnail
            if(!has_post_thumbnail($cta)){
                return;
            }
            
            //Get the featured image
            $featured_image = get_the_post_thumbnail( $cta, 'cta-banner-size' );
            
            //Get banner URL
            $cta_banner_url = get_post_meta( $cta->ID, 'cta-banner-url', true);

            //Return just banner image if URL is empty
            if(empty($cta_banner_url)){
                return $featured_image; 
            }
                
            //Verify if the CTA has title to use it as title attribute
            $cta_title = !empty($cta->post_title)?$cta->post_title:'';  

            //And then return image link html element
            return sprintf('<a href="%s" title="%s">%s</a>', $cta_banner_url, $cta_title, $featured_image);
        } // END public function cta_rockcontent_shortcode_show_html($atts)

	} // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
