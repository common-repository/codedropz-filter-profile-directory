<?php
	/**
	* Description : Modal Popup Content - HTML
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	// Profile image sizes
	$sizes = array(
		'width'		=>	360,
		'height'	=>	440
	);

	$profile_image = ( isset( $data['_thumbnail_id'] ) ? true : false );
?>

<!-- Modal Image-->
<?php if( $profile_image ) : ?>
	<figure>
		<img alt="profile-image" class="img-responsive" src="<?php the_pdfi_image( $data['_thumbnail_id'], $sizes ) ?>" alt="<?php echo esc_attr( $data['pdfi_firstname'] ); ?>">
	</figure>
<?php endif; ?>

<!-- Modal Wrapper-->
<div class="modal-content-wrapper <?php echo( ! $profile_image ? 'full-width' : '' ); ?>">
	<h2><?php echo esc_html( $data['pdfi_firstname'] ).' '.esc_html( $data['pdfi_lastname'] ); ?></h2>
	<span><?php echo esc_html( $data['pdfi_occupation'] ); ?></span>

	<?php if( isset($data['pdfi_description']) && ! empty( $data['pdfi_description'] ) ) : ?>
		<div class="description <?php echo ( strlen( $data['pdfi_description'] ) > 253 ? 'scroll' : '' ); ?>">
			<?php echo wpautop( $data['pdfi_description'] ); ?>
		</div>
	<?php endif; ?>
</div>
