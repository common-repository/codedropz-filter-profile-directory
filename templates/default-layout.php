<?php
	/**
	* Description : Default Layout
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/
?>

<div <?php pdfi_directory_class( 'column', $data ); ?>>

	<!-- Profile Image-->
	<div class="image-wrapper">

		<!-- Profile Image-->
		<?php if( isset( $data['_thumbnail_id']) ) : ?>
			<img src="<?php the_pdfi_image( $data['_thumbnail_id'] ) ?>" alt="<?php echo esc_attr( $data['pdfi_firstname'] ); ?>">
		<?php endif; ?>

		<!-- Button Details-->
		<div class="profile-details">
			<a href="javascript:void(0)" data-modal="<?php echo $data['ID']; ?>" title="View Details"><i class="arrow"></i></a>
		</div>
	</div>

	<!-- Profile Content -->
	<div class="profile-content">
		<h3 class="profile-title"><?php echo esc_html( $data['pdfi_firstname'].' '.$data['pdfi_lastname'] ); ?></h3>
		<span class="profile-occupation"><?php echo esc_html( $data['pdfi_occupation'] ); ?></span>
	</div>
</div>