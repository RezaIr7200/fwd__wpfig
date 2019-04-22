<?php 


?>

<input type="hidden" name="custom_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>" />

<?php foreach ($this->_metaFields as $field): ?>

	<?php if( 'gallery' === $field['type'] ) : ?>

		<?php
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		?>

		<input id="wpfig_gallery" type="hidden" name="wpfig_gallery" value="<?php echo  esc_attr($meta) ?>">

		<?php if ($meta) : ?>

			<?php $meta_array = explode(',', $meta);?>
			<div id="wpfig-wrap">
				<ul class="wpfig-images-wrap">
				<?php foreach ($meta_array as $atachment_id) : ?>
					<li class="wpfig-image-wrap">
						<button class="wpfig-image__remove">✕</button>
						<img id="<?php echo esc_attr($atachment_id) ?>" src="<?php echo wp_get_attachment_thumb_url($atachment_id) ?>">
					</li>
				<?php endforeach;?>
				</ul>
			</div>

		<?php endif; ?>
		
		<div class="wpfig-button-wrap">
			<input id="wpfig_gallery_button" type="button" class="button components-button is-button is-default is-large widefat" value="Manage Gallery" />
		</div>
	
	<?php endif;?>
	
<?php endforeach;?>