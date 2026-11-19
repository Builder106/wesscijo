<?php
/**
 * Template Name: Calendar
 *
 * Assign this to a "Calendar" page in wp-admin. Lists dated upcoming events
 * as an almanac: month, then day, then the events on that day.
 */
get_header();

$wessci_events = function_exists( 'wessci_site_get_upcoming_events' ) ? wessci_site_get_upcoming_events() : array();
$wessci_months = array();
foreach ( $wessci_events as $wessci_event ) {
	$wessci_start = wessci_site_get_event_datetime( $wessci_event->ID, 'start' );
	if ( $wessci_start ) {
		$wessci_months[ $wessci_start->format( 'Y-m' ) ][ $wessci_start->format( 'Y-m-d' ) ][] = $wessci_event;
	}
}
$wessci_today = wp_date( 'Y-m-d' );

// The next five event days become stars above the telescope, nearest day lowest.
// Five keeps each star a 24px tap target on a phone.
$wessci_sky = array();
foreach ( $wessci_months as $wessci_days ) {
	foreach ( $wessci_days as $wessci_day => $wessci_list ) {
		if ( count( $wessci_sky ) < 5 ) {
			$wessci_first = $wessci_list[0];
			$wessci_s     = wessci_site_get_event_datetime( $wessci_first->ID, 'start' );
			$wessci_sky[] = array(
				'day'    => $wessci_day,
				'label'  => wp_date( 'M j', $wessci_s->getTimestamp() ),
				'title'  => html_entity_decode( get_the_title( $wessci_first ), ENT_QUOTES, 'UTF-8' ),
				'when'   => wp_date( 'l, g:i A', $wessci_s->getTimestamp() ),
				'anchor' => 'event-' . $wessci_first->ID,
			);
		}
	}
}
$wessci_n = count( $wessci_sky );
foreach ( $wessci_sky as $wessci_i => &$wessci_star ) {
	// 12 to 40 degrees keeps the back of the tube above the floor at every aim.
	$wessci_star['angle'] = round( $wessci_n > 1 ? 12 + $wessci_i * 28 / ( $wessci_n - 1 ) : 22, 2 );
	$wessci_rad           = deg2rad( $wessci_star['angle'] );
	$wessci_star['x']     = 215 + 262 * cos( $wessci_rad );
	$wessci_star['y']     = 150 - 262 * sin( $wessci_rad );
}
unset( $wessci_star );
?>

<main class="site-main" id="main" tabindex="-1">

	<?php echo file_get_contents( get_theme_file_path( '/assets/instruments.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-owned SVG sprite. ?>
	<div class="calendar">
		<header class="calendar__head">
			<div class="calendar__intro">
			<h1 class="calendar__title"><?php the_title(); ?></h1>
			<p class="calendar__lede">Upcoming science events at Wesleyan, listed by day.</p>
			<?php if ( $wessci_sky ) : ?>
				<p class="calendar__hint calendar__hint--pointer">Point at an event, or click a star, to aim the telescope at its night. Click the star again to go to the event.</p>
				<p class="calendar__hint calendar__hint--touch">Tap a star to aim the telescope at its night, and tap it again to go to the event.</p>
			<?php endif; ?>
			<?php if ( count( $wessci_months ) > 1 ) : ?>
				<nav class="calendar__months" aria-label="Jump to a month">
					<ul>
						<?php foreach ( $wessci_months as $wessci_key => $wessci_days ) : ?>
							<li><a href="#month-<?php echo esc_attr( $wessci_key ); ?>"><?php echo esc_html( wp_date( 'F Y', strtotime( $wessci_key . '-01 12:00:00' ) ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
			</div>
			<figure class="calendar__scope">
				<?php echo wesscijo_refractor_svg( $wessci_sky ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-owned SVG, values escaped inside. ?>
				<?php if ( $wessci_sky ) : ?>
					<figcaption class="eyepiece" aria-live="polite">
						<span class="eyepiece__date"><?php echo esc_html( $wessci_sky[0]['label'] ); ?></span>
						<a class="eyepiece__title" href="#<?php echo esc_attr( $wessci_sky[0]['anchor'] ); ?>"><?php echo esc_html( $wessci_sky[0]['title'] ); ?></a>
						<span class="eyepiece__when"><?php echo esc_html( $wessci_sky[0]['when'] ); ?></span>
					</figcaption>
				<?php endif; ?>
			</figure>
		</header>

		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( get_the_content() ) :
					?>
					<div class="prose calendar__note"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
		<?php endif; ?>

		<?php if ( empty( $wessci_months ) ) : ?>
			<p class="empty">No upcoming events are listed yet. Email the editors to suggest one.</p>
		<?php endif; ?>

		<?php foreach ( $wessci_months as $wessci_key => $wessci_days ) : ?>
			<section class="month" aria-labelledby="month-<?php echo esc_attr( $wessci_key ); ?>">
				<h2 class="month__title" id="month-<?php echo esc_attr( $wessci_key ); ?>">
					<?php echo esc_html( wp_date( 'F', strtotime( $wessci_key . '-01 12:00:00' ) ) ); ?>
					<span class="month__year"><?php echo esc_html( wp_date( 'Y', strtotime( $wessci_key . '-01 12:00:00' ) ) ); ?></span>
				</h2>

				<?php foreach ( $wessci_days as $wessci_day => $wessci_list ) : ?>
					<?php $wessci_noon = strtotime( $wessci_day . ' 12:00:00' ); ?>
					<div class="day">
						<h3 class="day__date">
							<span class="day__weekday"><?php echo esc_html( wp_date( 'D', $wessci_noon ) ); ?></span>
							<span class="day__number"><?php echo esc_html( wp_date( 'j', $wessci_noon ) ); ?></span>
							<?php if ( $wessci_day === $wessci_today ) : ?>
								<span class="day__today">Today</span>
							<?php endif; ?>
						</h3>

						<ul class="day__events">
							<?php
							foreach ( $wessci_list as $post ) :
								setup_postdata( $post );
								$event      = $post;
								$start      = wessci_site_get_event_datetime( $event->ID, 'start' );
								$end        = wessci_site_get_event_datetime( $event->ID, 'end' );
								$department = get_post_meta( $event->ID, '_wessci_event_department', true );
								$location   = get_post_meta( $event->ID, '_wessci_event_location', true );
								$url        = get_post_meta( $event->ID, '_wessci_event_url', true );
								$summary    = wp_trim_words( wp_strip_all_tags( str_replace( '<', ' <', $event->post_content ) ), 26 );
								?>
								<li class="event" id="event-<?php echo esc_attr( $event->ID ); ?>" data-day="<?php echo esc_attr( $wessci_day ); ?>">
									<?php echo wesscijo_instrument_icon( wesscijo_event_instrument( $department . ' ' . get_the_title() . ' ' . $event->post_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from a fixed list. ?>
									<p class="event__time">
										<time datetime="<?php echo esc_attr( $start->format( DATE_ATOM ) ); ?>"><?php echo esc_html( wp_date( 'g:i A', $start->getTimestamp() ) ); ?></time>
										<?php if ( $end && $end->format( 'Y-m-d' ) === $start->format( 'Y-m-d' ) ) : ?>
											<time class="event__until" datetime="<?php echo esc_attr( $end->format( DATE_ATOM ) ); ?>"><?php echo esc_html( wp_date( 'g:i A', $end->getTimestamp() ) ); ?></time>
										<?php endif; ?>
									</p>
									<div class="event__body">
										<h4 class="event__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
										<?php if ( $department || $location ) : ?>
											<p class="event__meta">
												<?php if ( $department ) : ?>
													<span class="event__dept"><?php echo esc_html( $department ); ?></span>
												<?php endif; ?>
												<?php if ( $location ) : ?>
													<span class="event__place"><?php echo esc_html( $location ); ?></span>
												<?php endif; ?>
											</p>
										<?php endif; ?>
										<?php if ( $summary ) : ?>
											<p class="event__summary"><?php echo esc_html( $summary ); ?></p>
										<?php endif; ?>
									</div>
									<?php if ( $url ) : ?>
										<p class="event__link">
											<a href="<?php echo esc_url( $url ); ?>" rel="noopener noreferrer">Details<?php echo 'weswaves.com' === wp_parse_url( $url, PHP_URL_HOST ) ? ' on WesWAVES' : ''; ?><span class="u-visually-hidden">: <?php the_title(); ?></span></a>
										</p>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</section>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>

		<aside class="calendar__suggest" aria-labelledby="suggest-title">
			<h2 class="calendar__suggest-title" id="suggest-title">Suggest an event</h2>
			<p>Wesleyan organizers can email the editors with the department or group, date, location, and an official event link.</p>
			<a class="btn" href="mailto:wesscijo@wesleyan.edu?subject=Calendar%20event%20suggestion">Email the editors</a>
		</aside>
	</div>

</main>

<?php get_footer(); ?>
