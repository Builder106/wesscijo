<?php
/**
 * Template Name: About
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page" data-od-id="about-page">
		<div class="utility-inner">
			<h1 data-od-id="about-title">About WesSciJo</h1>
			<p class="utility-lead">WesSciJo is Wesleyan University’s undergraduate science journal, showcasing research, field studies, and perspectives across the sciences.</p>

			<div class="about-hero-seal">
				<img class="official-seal" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/logo.png" width="220" height="220" alt="Official Seal of the Wesleyan Science Journal: red cardinal in laboratory safety goggles holding a micropipette.">
				<div class="seal-caption">
					<strong>Official Seal &amp; Insignia</strong>
					<p>The cardinal in safety goggles with micropipette symbolizes student-driven empirical inquiry, laboratory rigor, and field observation across Wesleyan University.</p>
				</div>
			</div>

			<h2>Mission &amp; Scope</h2>
			<p>The Wesleyan Science Journal is an undergraduate-led scientific publication celebrating scholarship, research, and science communication across Wesleyan University. We publish peer-reviewed student research, in-depth scientific features, and interdisciplinary essays spanning biology, chemistry, physics, mathematics, computer science, and earth sciences.</p>

			<figure class="utility-art" data-od-id="about-nest-identity">
				<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/nest.svg" width="1500" height="1000" alt="WesSciJo identity motif: a cardinal above a woven nest holding laboratory glassware." loading="lazy" data-od-id="about-nest-artwork">
				<figcaption>Scientific inquiry grounded in natural observation.</figcaption>
			</figure>

			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					if ( get_the_content() ) :
						?>
						<div class="prose"><?php the_content(); ?></div>
						<?php
					endif;
				endwhile;
			endif;
			?>

			<h2>Editorial Board &amp; Masthead</h2>
			<p>The journal is edited and produced by undergraduate researchers across Wesleyan University in collaboration with faculty advisors throughout the Natural Sciences and Mathematics Division.</p>

			<?php foreach ( wessci_editorial_board() as $key => $section ) : ?>
				<section class="masthead-division" data-od-id="masthead-<?php echo esc_attr( $key ); ?>">
					<h3 class="masthead-division-title"><?php echo esc_html( $section['title'] ); ?></h3>
					<div class="masthead-grid">
						<?php foreach ( $section['members'] as $member ) : ?>
							<div class="person-card">
								<div class="person-avatar" aria-hidden="true"><?php echo esc_html( $member['initials'] ); ?></div>
								<div class="person-info">
									<strong class="person-name"><?php echo esc_html( $member['name'] ); ?></strong>
									<span class="person-role"><?php echo esc_html( $member['role'] ); ?></span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>

			<nav class="filter-nav" aria-label="Journal pages" data-od-id="about-journal-routes" style="margin-top: 48px;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<a href="<?php echo esc_url( home_url( '/archive/' ) ); ?>">Archives / issue</a>
				<a href="<?php echo esc_url( home_url( '/search/' ) ); ?>">Search</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" aria-current="page">About</a>
				<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a>
				<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
			</nav>
		</div>
	</section>
</main>

<?php
get_footer();
