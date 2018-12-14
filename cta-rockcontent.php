<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name:  Call To Action - RockContent
Plugin URI:   http://www.marcelofq.com.br/
Description:  A CTA WordPress plugin for RockContent Technical Challenge Job Interview
Version:      1.0
Author:       Marcelo Fiuza
Author URI:   http://marcelofq.com.br
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /cta-rockcontent
*/

// Plugin main function that creates a custom post type called cta-banner and add it to wordpress init action

if(!class_exists('CTA_RockContent'))
{
	class CTA_RockContent
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			
			// Register custom post types
			require_once(sprintf("%s/post-types/cta-banner.php", dirname(__FILE__)));
			$CTA_Banner_Post_Type = new CTA_Banner();

		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate

	} // END class CTA_RockContent
} // END if(!class_exists('CTA_RockContent'))

if(class_exists('CTA_RockContent'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('CTA_RockContent', 'activate'));
	register_deactivation_hook(__FILE__, array('CTA_RockContent', 'deactivate'));

	// instantiate the plugin class
	$cta_rockcontent_plugin = new CTA_RockContent();

}

?>
