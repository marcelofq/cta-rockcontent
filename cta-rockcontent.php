<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name:  Call To Action - RockContent
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  A CTA WordPress plugin for RockContent Technical Challenge Job Interview
Version:      1.0
Author:       Marcelo Fiuza
Author URI:   http://marcelofq.com.br
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /cta-rockcontent
*/

//Adicionando a função que cria o custom post type cta-banner na action init do wordpress
add_action( 'init', 'cta_banner_custom_post_type', 0 );

// CTA Banner Register Custom Post Type Function
function cta_banner_custom_post_type() {

	$labels = array(
		'name'                  => _x( "CTA's", 'Post Type General Name', 'cta-rockcontent' ),
		'singular_name'         => _x( 'CTA', 'Post Type Singular Name', 'cta-rockcontent' ),
		'menu_name'             => __( "CTA's", 'cta-rockcontent' ),
		'name_admin_bar'        => __( 'CTA', 'cta-rockcontent' )
	);
	$args = array(
		'label'                 => __( 'CTA', 'cta-rockcontent' ),
		'description'           => __( 'Call to Action', 'cta-rockcontent' ),
		'labels'                => $labels,
		'supports'              => array('title', 'thumbnail'),
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
		'menu_icon'				=> 'dashicons-admin-comments',
		'register_meta_box_cb'  => 'cta_banner_add_metabox',
		'capability_type'       => 'post'
	);
	//Registrando o custom post type cta-banner
	register_post_type( 'cta-banner', $args );
	
	//Adding custom imagem size (cta-banner-size)
	add_image_size( 'cta-banner-size', 960, 300, true ); // hard crop mode	

	//Funcao para adicionar o metabox da URL do banner
	function cta_banner_add_metabox(){
		//Adicionando o campo de URL no custom post type cta-banner
		add_meta_box( 'cta-banner-url', 'URL', 'cta_banner_url_html', 'cta-banner', 'normal', 'high' );
	}

	function cta_banner_url_html(){
		global $post;

		// Nonce field to validade form request cam from current site
		wp_nonce_field( 'cta_banner', 'cta_banner_nonce' );

		// Get the url data if it's already entered
		$url = get_post_meta( $post->ID, 'cta-banner-url', true );

		//
		echo '<p>';
	    echo '<input id="cta_banner_url_input" type="text" name="cta_banner_url_text_input" value="' . esc_url( $url ) . '" style="width: 100%;" /></p>';
	    echo '<p>Link de para onde o CTA deve redirecionar o usuário. Informe a url completa. ex: https://blog.com.br/meu-ebook</p>';

	}


	//Adding the [cta-banner] shortcode
	add_shortcode('cta-banner', 'cta_rockcontent_shortcode_show_html');
	//Shortcode function to return banner in html elements
	function cta_rockcontent_shortcode_show_html( $atts ){
		//Verify if array $atts has the 'id' param, and if is not empty and has a numeric value
		if(isset($atts['id']) && !empty($atts['id']) && is_numeric($atts['id'])) { 
			$cta = get_post($atts['id']);
			if($cta){
				//Check if the post has thumbnail
				$featured_image = has_post_thumbnail($cta) ? get_the_post_thumbnail( $cta, 'cta-banner-size' ) : '';
				
				//Return CTA's banner with link, or just banner image
				$cta_banner_url = get_post_meta( $cta->ID, 'cta-banner-url', true);
				if(!is_null($cta_banner_url)){
					//Verify if the CTA has title to use it on link title attribute
					$cta_title = !empty($cta->post_title)?' title="'.$cta->post_title.'" ':'';
					//And then return link html element
					return '<a href="' . $cta_banner_url . '" ' . $cta_title . ' >'. $featured_image . '</a>';

				} else return $featured_image; //return just the featured image

			} else return __("#ERROR: No CTA's has found with id=" . $atts['id'], 'cta-rockcontent');

		} else return;
	}
}

// CTA Banner Metabox Data Function
add_action( 'save_post', 'cta_banner_save_meta' );
function cta_banner_save_meta( $post_id ) {
	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Check if our nonce is set.
    if ( ! isset( $_POST['cta_banner_nonce'] ) ) {
        return $post_id;
    }

    $nonce = $_POST['cta_banner_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'cta_banner' ) ) {
        return $post_id;
    }
    /*
     * If this is an autosave, our form has not been submitted,
     * so we don't want to do anything.
     */
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }


    // Sanitize the user input.
    $url = sanitize_text_field( $_POST['cta_banner_url_text_input'] );

    // Update the meta field.
    if ( get_post_meta( $post_id, 'cta-banner-url', true ) ) {
		// If the custom field already has a value, update it.
		update_post_meta( $post_id, 'cta-banner-url', $url );
	} else {
		// If the custom field doesn't have a value, add it.
		add_post_meta( $post_id, 'cta-banner-url', $url);
	}
    	
}
?>