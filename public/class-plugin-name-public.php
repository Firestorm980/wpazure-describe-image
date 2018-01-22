<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpazure_describe_image
 * @subpackage wpazure_describe_image/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    wpazure_describe_image
 * @subpackage wpazure_describe_image/public
 * @author     Your Name <email@example.com>
 */
class Wpazure_Describe_Image_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wpazure_describe_image    The ID of this plugin.
	 */
	private $wpazure_describe_image;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wpazure_describe_image       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wpazure_describe_image, $version ) {

		$this->wpazure_describe_image = $wpazure_describe_image;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpazure_Describe_Image_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpazure_Describe_Image_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wpazure_describe_image, plugin_dir_url( __FILE__ ) . 'css/wpazure-describe-image-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpazure_Describe_Image_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpazure_Describe_Image_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->wpazure_describe_image, plugin_dir_url( __FILE__ ) . 'js/wpazure-describe-image-public.js', array( 'jquery' ), $this->version, false );

	}

}
