<?php
/**
 *  Widget Frontend Display
 *
 * @description: Responsible for the frontend display of the Google Places Reviews
 * @since      : 1.0
 */

//Handle website link
$website = isset( $response['result']['website'] ) ? $response['result']['website'] : '';
if ( ! isset( $response['result']['website'] ) || empty( $response['result']['website'] ) ) {
	//user g+ page since they have no website
	$website = isset( $response['result']['url'] ) ? $response['result']['url'] : '';
}
$place_avatar = isset( $response['place_avatar'] ) ? $response['place_avatar'] : GPR_PLUGIN_URL . '/assets/images/default-img.png';
?>

	<div class="gpr-<?php echo sanitize_title( $widget_style ); ?>">


		<?php

		//Business Information
		if ( $hide_header !== '1' ) {
			?>

			<div class="gpr-business-header gpr-clearfix" itemtype="http://schema.org/AggregateRating">

				<div class="gpr-business-avatar" style="background-image: url(<?php echo $place_avatar; ?>)"></div>

                <span class="gpr-business-name"><a href="<?php echo $website; ?>"
												   title="<?php echo $response['result']['name']; ?>" <?php echo $target_blank . $no_follow; ?>><span
							itemprop="itemReviewed"><?php echo $response['result']['name']; ?></span></a></span>

				<?php
				//Overall rating for biz display:
				$overall_rating = isset( $response['result']['rating'] ) ? $response['result']['rating'] : '';
				if ( $overall_rating ) {
					echo $this->get_star_rating( $overall_rating, null, $hide_out_of_rating, $hide_google_image );
				} //No rating for this biz yet:
				else {
					?>

					<span class="no-reviews-header"><?php
						$googleplus_page = isset( $response['result']['url'] ) ? $response['result']['url'] : '';
						echo sprintf( __( '<a href="%1$s" class="leave-review" target="_blank" class="new-window">Write a review</a>', 'gpr' ), esc_url( $googleplus_page ) ); ?></span>

				<?php } ?>



			</div>

		<?php } ?>


		<?php
		//Google Places Reviews
		if ( isset( $response['gpr_reviews'] ) && ! empty( $response['gpr_reviews'] ) ) {
			?>

			<div class="gpr-reviews-wrap">
				<?php
				$counter = 0;
				$review_limit = isset( $review_limit ) ? $review_limit : 5;

				//Loop Google Places reviews
				foreach ( $response['gpr_reviews'] as $review ) {

					//Set review vars
					$author_name    = $review['author_name'];
					$author_url     = isset( $review['author_url'] ) ? $review['author_url'] : '';
					$overall_rating = $review['rating'];
					$review_text    = $review['text'];
					$time           = $review['time'];
					$avatar         = isset( $review['avatar'] ) ? $review['avatar'] : GPR_PLUGIN_URL . '/assets/images/mystery-man.png';
					$review_filter  = isset( $review_filter ) ? $review_filter : '';
					$counter ++;


					//Review filter set OR count limit reached?
					if ( $overall_rating >= $review_filter && $counter <= $review_limit ) {
						?>

						<div class="gpr-review" itemprop="review" itemscope itemtype="http://schema.org/Review">

							<div class="gpr-review-header gpr-clearfix">
								<div class="gpr-review-avatar">
									<img src="<?php echo $avatar; ?>" alt="<?php echo $author_name; ?>"
										 title="<?php echo $author_name; ?>" />
								</div>

							<span class="grp-reviewer-name">
								<?php if ( ! empty( $author_url ) ) { ?>
									<a href="<?php echo $author_url; ?>"
									   title="<?php _e( 'View this profile.', 'gpr' ); ?>" <?php echo $target_blank . $no_follow; ?>><span itemprop="author"><?php echo $author_name; ?></span></a>
								<?php } else { ?>
									<?php echo $author_name; ?>
								<?php } ?>
							</span>
								<?php echo $this->get_star_rating( $overall_rating, $time, $hide_out_of_rating, false ); ?>
							</div>


							<div class="gpr-review-content" itemprop="reviewBody">
								<?php echo wpautop( $review_text ); ?>
							</div>

						</div><!--/.gpr-review -->

					<?php } //endif review filter ?>

				<?php } //end review loop	?>

			</div><!--/.gpr-reviews -->

		<?php
		} //end review if
		else {
			//No Reviews for this location
			?>

			<div class="gpr-no-reviews-wrap">
				<p class="no-reviews"><?php
					$googleplus_page = isset( $response['result']['url'] ) ? $response['result']['url'] : '';
					echo sprintf( __( 'There are no reviews yet for this business. <a href="%1$s" class="leave-review" target="_blank">Be the first to review</a>', 'gpr' ), esc_url( $googleplus_page ) ); ?></p>

			</div>

		<?php } ?>


	</div>


<?php
//after widget
echo ! empty( $after_widget ) ? $after_widget : '</div>';