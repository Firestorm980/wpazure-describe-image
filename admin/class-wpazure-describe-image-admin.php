<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpazure_describe_image
 * @subpackage wpazure_describe_image/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wpazure_describe_image
 * @subpackage wpazure_describe_image/admin
 * @author     Your Name <email@example.com>
 */
class Wpazure_Describe_Image_Admin {

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
	 * @param      string    $wpazure_describe_image       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wpazure_describe_image, $version ) {

		$this->wpazure_describe_image = $wpazure_describe_image;
		$this->version = $version;

		$this->set_defaults();

	}

	public function set_defaults() {
		$defaults = array(
			'wpadi_azure_api_key'        => '',
			'wpadi_azure_api_region'     => '',
			'wpadi_confidence_threshold' => '0.8',
		);
		$options  = get_option( 'wpadi_settings' );
		$settings = wp_parse_args( $options, $defaults );

		update_option( 'wpadi_settings', $settings );
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->wpazure_describe_image, plugin_dir_url( __FILE__ ) . 'css/wpazure-describe-image-admin.css', array( 'thickbox' ), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->wpazure_describe_image, plugin_dir_url( __FILE__ ) . 'js/wpazure-describe-image-admin.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Filter to add the extra fields to the attachment edit screen.
	 *
	 * @param Array $form_fields The array of form fields to edit.
	 * @param WP_Post $post The post currently being edited.
	 * @return Array The updated form fields.
	 */
	public function wpadi_admin_attachment_fields_to_edit( $form_fields, $post ) {

		// If it isn't an image, don't bother.
		if ( ! wp_attachment_is_image( $post->ID ) ) {
			return;
		}

		ob_start();

		// Has all of our HTML in one spot
		include 'partials/wpazure-describe-image-admin-display.php';

		$html = ob_get_clean();

		// Add the new form fields
		$form_fields['generate_alt'] = array(
			'label' => __( 'Generate Alternate Text', 'wpazure-describe-image' ),
			'input' => 'html',
			'helps' => __( 'Generate alternative text for this attachment based on Azure Computer Vision API.', 'wpazure-describe-image' ),
			'html'  => $html,
		);

		return $form_fields;
	}

	/**
	 * Plugin AJAX function that will get our image descriptions from the Azure API.
	 *
	 * @return Object
	 */
	public function wpadi_ajax_azure_describe_image() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die( '-1' );
		}

		$image          = esc_url_raw( filter_input( INPUT_POST, 'image', FILTER_SANITIZE_URL ) );
		$wpadi_settings = get_option( 'wpadi_settings', false );

		// Error if no settings.
		if ( ! $wpadi_settings ) {
			wp_send_json_error( [
				'message' => __( 'Error: Options have not been set for the plugin.', 'wpazure-describe-image' ),
			] );
		}

		$api_key        = $wpadi_settings['wpadi_azure_api_key'];
		$region         = $wpadi_settings['wpadi_azure_api_region'];
		$max_candidates = 100; // If we want to make this a setting at some point.

		// Error out if no API or region
		if ( '' === $api_key || '' === $region ) {
			wp_send_json_error( [
				'message' => __( 'Error: Invalid key or region detected. Please fill out the API key and appropriate region.', 'wpazure-describe-image' ),
			] );
		};

		// Set up the request
		$url  = "https://${region}.api.cognitive.microsoft.com/vision/v1.0/describe?maxCandidates=${max_candidates}";
		$body = wp_json_encode( array(
			'url' => $image,
		) );
		$args = array(
			'headers' => array(
				'Content-Type'              => 'application/json',
				'Ocp-Apim-Subscription-Key' => $api_key,
			),
			'body'    => $body,
		);

		// Call the API
		$response = wp_remote_post( $url, $args );

		// Return AJAX call
		wp_send_json_success( [
			'threshold' => $wpadi_settings['wpadi_confidence_threshold'],
			'api'       => $response,
		] );
	}



	public function wpadi_add_admin_menu() {
		add_options_page( 'WP Azure Describe Image', 'WP Azure Describe Image', 'manage_options', 'wp_azure_describe_image', array( $this, 'wpadi_options_page' ) );
	}

	public function wpadi_settings_init() {

		register_setting( 'wpadiOptionsPage', 'wpadi_settings' );

		/**
		 * API Information
		 */
			add_settings_section(
				'wpadi_api_section',
				__( 'API Information', 'wpazure-describe-image' ),
				array( $this, 'wpadi_settings_api_section_callback' ),
				'wpadiOptionsPage'
			);

			add_settings_field(
				'wpadi_azure_api_key',
				__( 'Microsoft Azure API Key', 'wpazure-describe-image' ),
				array( $this, 'wpadi_azure_api_key_render' ),
				'wpadiOptionsPage',
				'wpadi_api_section',
				array( 'label_for' => 'wpadi_azure_api_key' )
			);

			add_settings_field(
				'wpadi_azure_api_region',
				__( 'Microsoft Azure Region', 'wpazure-describe-image' ),
				array( $this, 'wpadi_azure_api_region_render' ),
				'wpadiOptionsPage',
				'wpadi_api_section',
				array( 'label_for' => 'wpadi_azure_api_region' )
			);

		/**
		 * Plugin Specific Options
		 */
			add_settings_section(
				'wpadi_options_section',
				__( 'Describe Image Options', 'wpazure-describe-image' ),
				array( $this, 'wpadi_settings_options_section_callback' ),
				'wpadiOptionsPage'
			);

			add_settings_field(
				'wpadi_confidence_threshold',
				__( 'Confidence Threshold', 'wpazure-describe-image' ),
				array( $this, 'wpadi_confidence_threshold_render' ),
				'wpadiOptionsPage',
				'wpadi_options_section',
				array( 'label_for' => 'wpadi_confidence_threshold' )
			);
	}

	public function wpadi_azure_api_key_render() {

		$options = get_option( 'wpadi_settings' );
		?>
		<input
			id='wpadi_azure_api_key'
			type='password'
			name='wpadi_settings[wpadi_azure_api_key]'
			value='<?php echo $options['wpadi_azure_api_key']; ?>'
			aria-describedby="wpadi_azure_api_key_description">
		<p class="wpadi-description description" id="wpadi_azure_api_key_description">
			<?php esc_html_e( 'Enter your API key that you recieved from Microsoft Azure.', 'wpazure-describe-image' ); ?>
		</p>
		<?php
	}

	public function wpadi_azure_api_region_render() {

		$options = get_option( 'wpadi_settings' );
		?>
		<select
			id='wpadi_azure_api_region'
			name='wpadi_settings[wpadi_azure_api_region]'
			aria-describedby="wpadi_azure_api_region_description">
			<option value=""><?php esc_html_e( 'Select a region', 'wpazure-describe-image' ); ?></option>
			<option value="westus" <?php selected( $options['wpadi_azure_api_region'], 'westus' ); ?>>West US</option>
			<option value="westus2" <?php selected( $options['wpadi_azure_api_region'], 'westus2' ); ?>>West US 2</option>
			<option value="eastus" <?php selected( $options['wpadi_azure_api_region'], 'eastus' ); ?>>East US</option>
			<option value="eastus2" <?php selected( $options['wpadi_azure_api_region'], 'eastus2' ); ?>>East US 2</option>
			<option value="westcentralus" <?php selected( $options['wpadi_azure_api_region'], 'westcentralus' ); ?>>West Central US</option>
			<option value="southcentralus" <?php selected( $options['wpadi_azure_api_region'], 'southcentralus' ); ?>>South Central US</option>
			<option value="westeurope" <?php selected( $options['wpadi_azure_api_region'], 'westeurope' ); ?>>West Europe</option>
			<option value="northeurope" <?php selected( $options['wpadi_azure_api_region'], 'northeurope' ); ?>>North Europe</option>
			<option value="southeastasia" <?php selected( $options['wpadi_azure_api_region'], 'southeastasia' ); ?>>Southeast Asia</option>
			<option value="eastasia" <?php selected( $options['wpadi_azure_api_region'], 'eastasia' ); ?>>East Asia</option>
			<option value="australiaeast" <?php selected( $options['wpadi_azure_api_region'], 'australiaeast' ); ?>>Australia East</option>
			<option value="brazilsouth" <?php selected( $options['wpadi_azure_api_region'], 'brazilsouth' ); ?>>Brazil South</option>
		</select>
		<p class="wpadi-description description" id="wpadi_azure_api_region_description">
			<?php esc_html_e( 'Select the region you were assigned to by Microsoft Azure.', 'wpazure-describe-image' ); ?>
		</p>
		<?php

	}

	public function wpadi_confidence_threshold_render() {

		$options = get_option( 'wpadi_settings' );
		?>
		<input
			id='wpadi_confidence_threshold'
			type='number'
			min='0'
			max='1'
			step='0.01'
			name='wpadi_settings[wpadi_confidence_threshold]'
			value='<?php echo $options['wpadi_confidence_threshold']; ?>'
			aria-describedby="wpadi_confidence_threshold_description">
		<p class="wpadi-description description" id="wpadi_confidence_threshold_description">
			<?php esc_html_e( 'Enter a number between 0 and 1 to only show suggestions for alternative text that meet a certain confidence level and above. 1 being the most confident that the description of the image is appropriate. Defaults to 0.8.', 'wpazure-describe-image' ); ?>
		</p>
		<?php

	}

	public function wpadi_settings_api_section_callback() {

		esc_html_e( 'Enter the API key and region information provided by Microsoft here.', 'wpazure-describe-image' );

	}

	public function wpadi_settings_options_section_callback() {

		esc_html_e( 'Plugin specific options for the Computer Vision API when describing an image.', 'wpazure-describe-image' );

	}

	public function wpadi_options_page() {

		?>
		<form action='options.php' method='post'>

			<h2><?php esc_html_e( 'WP Azure Describe Image', 'wpazure-describe-image' ); ?></h2>

			<?php
			settings_fields( 'wpadiOptionsPage' );
			do_settings_sections( 'wpadiOptionsPage' );
			submit_button();
			?>

		</form>
		<?php

	}

}
