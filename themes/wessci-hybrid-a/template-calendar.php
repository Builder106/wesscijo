<?php
/**
 * Template Name: Calendar
 *
 * Assign this to a "Calendar" page in wp-admin. Lists upcoming wessci_event
 * posts while retaining undated fixture events until real dates are supplied.
 */
get_header();
?>

<main class="site-main" id="main" tabindex="-1">

	<article class="article">
		<h1 class="article__title"><?php the_title(); ?></h1>

		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( get_the_content() ) :
					?>
					<div class="prose"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
		<?php endif; ?>
	</article>

	<?php
	$wessci_events = function_exists( 'wessci_site_get_upcoming_events' ) ? wessci_site_get_upcoming_events() : array();
	?>

	<section class="division">
		<?php if ( ! empty( $wessci_events ) ) : ?>
			<div class="cards">
				<?php
				foreach ( $wessci_events as $event ) :
					setup_postdata( $event );
					$start = wessci_site_get_event_datetime( $event->ID, 'start' );
					$end   = wessci_site_get_event_datetime( $event->ID, 'end' );
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
							</a>
						<?php endif; ?>
						<h3 class="card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<?php if ( $start ) : ?>
							<p class="meta">
								<time datetime="<?php echo esc_attr( $start->format( DATE_ATOM ) ); ?>"><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $start->getTimestamp() ) ); ?></time>
								<?php if ( $end ) : ?>
									<span aria-hidden="true">–</span>
									<time datetime="<?php echo esc_attr( $end->format( DATE_ATOM ) ); ?>"><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $end->getTimestamp() ) ); ?></time>
								<?php endif; ?>
							</p>
						<?php endif; ?>
						<?php if ( get_post_meta( $event->ID, '_wessci_event_location', true ) ) : ?>
							<p class="card__excerpt"><?php echo esc_html( get_post_meta( $event->ID, '_wessci_event_location', true ) ); ?></p>
						<?php endif; ?>
						<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
						<?php $url = get_post_meta( $event->ID, '_wessci_event_url', true ); ?>
						<?php if ( $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" rel="noopener noreferrer">Event details</a>
						<?php endif; ?>
					</article>
					<?php
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<p class="empty">No upcoming events yet — check back soon.</p>
		<?php endif; ?>
	</section>

</main>

<?php get_footer(); ?>
