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
<div class="js-wpazure-describe-image__field" id="jsWPADIField">
	<button
		type="button"
		class="button button-primary"
		class="js-wpazure-describe-image__get-descriptions"
		id="jsWPADIGetDescriptions"
		data-image-url="<?php echo esc_url( wp_get_attachment_url() ); ?>"
		aria-controls="jsWPADIFieldset">
		<?php esc_html_e( 'Generate Alternate Text', 'wpazure-describe-image' ); ?>
	</button>
	<span class="js-wpazure-describe-image__loader spinner" id="jsWPADILoader"></span>

	<div class="js-wpazure-describe-image__results" id="jsWPADIResults">
		<fieldset id="jsWPADIFieldset" aria-live="polite">
			<legend>
				<?php esc_html_e( 'Pick appropriate descriptive alternate text.', 'wpazure-describe-image' ); ?>
				<a href="https://webaim.org/techniques/alttext/"><?php esc_html_e( 'WebAIM Alternative Text Techniques', 'wpazure-describe-image' ); ?></a>.
			</legend>
			<ul class="js-wpazure-describe-image__choices" id="jsWPADIChoices">
			</ul>
		</fieldset>
		<button class="button button-primary" type="button" id="jsWPADISetDescription" disabled><?php esc_html_e( 'Submit, use selected alterate text', 'wpazure-describe-image' ); ?></button>
		<button class="button button-secondary" type="button" id="jsWPADICancel"><?php esc_html_e( 'Cancel, use my own alternate text', 'wpazure-describe-image' ); ?></button>
	</div>
	<div class="js-wpazure-describe-image__notice" id="jsWPADINotices">
		<div id="jsWPADINotice"> 
			<p id="jsWPADINoticeText"></p>
		</div>
	</div>	
</div>

