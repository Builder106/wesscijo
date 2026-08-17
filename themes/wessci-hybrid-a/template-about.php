<?php
/**
 * Template Name: About
 *
 * Assign this to the site's About page in wp-admin (Page Attributes ->
 * Template). The description above the masthead comes from that page's
 * own editor content, same as any other page.
 */
get_header();

// Fall 2026 masthead, from Shriya's 2026-07-09 email. No bios yet — the
// Argus-style "About Me" blurbs need real text from each person before
// they can be added, so this is name + role only for now.
$wessci_masthead = array(
	array(
		'section' => 'Editorial',
		'people'  => array(
			array( 'name' => 'Aryia Banihashem-Ahmad', 'role' => 'Editor-in-Chief' ),
			array( 'name' => 'Shriya Sakalkale', 'role' => 'Editor-in-Chief' ),
			array( 'name' => 'Elena Mente', 'role' => 'Editor-in-Chief' ),
		),
	),
	array(
		'section' => 'Life Sciences',
		'people'  => array(
			array( 'name' => 'Lorraine Hillgen-Santa', 'role' => 'Lead Life Science Editor' ),
			array( 'name' => 'Maia Feik Reinhart', 'role' => 'Lead Life Science Editor' ),
			array( 'name' => 'Feyza Horuz', 'role' => 'Biology Editor' ),
			array( 'name' => 'Kitty Edwards', 'role' => 'Biology Editor' ),
			array( 'name' => 'Claire Farina', 'role' => 'Biology Editor' ),
			array( 'name' => 'Sophie Lambert', 'role' => 'Assistant Biology Editor' ),
			array( 'name' => 'Saara Saini', 'role' => 'Assistant Biology Editor' ),
			array( 'name' => 'Hannah Zullow', 'role' => 'Neuroscience & Psychology Editor' ),
			array( 'name' => 'Gaby Sorin', 'role' => 'Neuroscience & Psychology Editor' ),
			array( 'name' => 'Olivia Oliveira', 'role' => 'Assistant Neuroscience & Psychology Editor' ),
			array( 'name' => 'Rhea Ashish Kothari', 'role' => 'Assistant Neuroscience & Psychology Editor' ),
		),
	),
	array(
		'section' => 'Physical Science',
		'people'  => array(
			array( 'name' => 'Natalie Price', 'role' => 'Lead Physical Science Editor' ),
			array( 'name' => 'Hamza Habib', 'role' => 'Lead Physical Science Editor' ),
			array( 'name' => 'Zesun Hossain', 'role' => 'Astronomy Editor' ),
			array( 'name' => 'Ella Stricker', 'role' => 'Physics Editor' ),
			array( 'name' => 'Rhea Ashish Kothari', 'role' => 'Assistant Physics Editor' ),
			array( 'name' => 'Ellen Gudiksen', 'role' => 'Chemistry Editor' ),
			array( 'name' => 'Saara Saini', 'role' => 'Assistant Chemistry Editor' ),
			array( 'name' => 'Aniana Garciano', 'role' => 'Earth & Environmental Editor' ),
			array( 'name' => 'Ella Stricker', 'role' => 'Earth & Environmental Editor' ),
		),
	),
	array(
		'section' => 'Quantitative & Computational Science',
		'people'  => array(
			array( 'name' => 'Shloka Bhattacharyya', 'role' => 'Lead Quantitative and Computational Editor' ),
			array( 'name' => 'Gillian Churchland', 'role' => 'Math Editor' ),
			array( 'name' => 'Calvin Chiu', 'role' => 'Math Editor' ),
			array( 'name' => 'Sangye Sherpa', 'role' => 'Math Editor' ),
			array( 'name' => 'Samantha Sheahan', 'role' => 'Assistant Computer Science Editor' ),
		),
	),
	array(
		'section' => 'Science, Technology & Society',
		'people'  => array(
			array( 'name' => 'Sangye Sherpa', 'role' => 'Lead Science, Technology & Society Editor' ),
			array( 'name' => 'Tessa Higgins', 'role' => 'Lead Science, Technology & Society Editor' ),
			array( 'name' => 'Maddy Marx', 'role' => 'Science, Technology & Society Editor' ),
			array( 'name' => 'Dahlia Cedarbaum', 'role' => 'Science, Technology & Society Editor' ),
			array( 'name' => 'Sarah Toolan', 'role' => 'Science, Technology & Society Editor' ),
		),
	),
	array(
		'section' => 'Graphic Design',
		'people'  => array(
			array( 'name' => 'Sorielis Paulino Polanco', 'role' => 'Graphic Designer' ),
			array( 'name' => 'Minaal Khwaja', 'role' => 'Graphic Designer' ),
			array( 'name' => 'Hannah Russak', 'role' => 'Graphic Designer' ),
			array( 'name' => 'Ali Eckstein', 'role' => 'Graphic Designer' ),
			array( 'name' => 'Olivia Oliveira', 'role' => 'Graphic Designer' ),
			array( 'name' => 'Kitty Edwards', 'role' => 'Graphic Designer' ),
		),
	),
	array(
		'section' => 'Web Design',
		'people'  => array(
			array( 'name' => 'Olayinka Vaughan', 'role' => 'Lead Web Designer' ),
			array( 'name' => 'Giancarlo Fedolfi', 'role' => 'Web Designer' ),
		),
	),
);
?>

<main class="site-main" id="main">

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

	<?php foreach ( $wessci_masthead as $wessci_group ) : ?>
		<section class="division">
			<h2 class="division__title"><?php echo esc_html( $wessci_group['section'] ); ?></h2>
			<div class="masthead-grid">
				<?php foreach ( $wessci_group['people'] as $wessci_person ) : ?>
					<div class="person">
						<div class="person__avatar" aria-hidden="true"><?php echo esc_html( wessci_initials( $wessci_person['name'] ) ); ?></div>
						<p class="person__name"><?php echo esc_html( $wessci_person['name'] ); ?></p>
						<p class="person__role"><?php echo esc_html( $wessci_person['role'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endforeach; ?>

</main>

<?php get_footer(); ?>
