<?php

/**
 * Provide fields for the image attachment being edited
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wpazure_describe_image
 * @subpackage wpazure_describe_image/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<button
	type="button"
	class="button button-primary"
	class="js-wpazure-describe-image__get-descriptions"
	id="jsPluginNameGetDescriptions"
	data-image-url="<?php echo esc_url( wp_get_attachment_url() ); ?>">
	<?php esc_html_e( 'Generate Alternate Text', 'wpazure-describe-image' ); ?>
</button>

<div class="js-wpazure-describe-image__results" id="jsPluginNameResults">

	<fieldset>
		<legend>
			<?php esc_html_e( 'Pick appropriate alternate text.', 'wpazure-describe-image' ); ?>
			<a href="https://webaim.org/techniques/alttext/"><?php esc_html_e( 'WebAIM Alternative Text Techniques', 'wpazure-describe-image' ); ?></a>.
		</legend>
		<ul class="js-wpazure-describe-image__choices" id="jsPluginNameChoices">
		</ul>
	</fieldset>
	<button class="button button-primary" type="button" id="jsPluginNameSetDescription" disabled><?php esc_html_e( 'Submit', 'wpazure-describe-image' ); ?></button>
	<button class="button button-secondary" type="button" id="jsPluginNameCancel"><?php esc_html_e( 'Cancel', 'wpazure-describe-image' ); ?></button>
</div>
