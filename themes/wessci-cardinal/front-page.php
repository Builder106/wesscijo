<?php
/**
 * The template for displaying the front page / home view
 *
 * @package WesSciJo_Cardinal
 */

get_header();

// Fetch stories dynamically for the tree explorer & reading directory
$feature_query = new WP_Query( array(
	'posts_per_page'      => 1,
	'ignore_sticky_posts' => true,
	'category_name'       => 'field-features,features,news-features-perspectives',
) );
if ( ! $feature_query->have_posts() ) {
	$feature_query = new WP_Query( array( 'posts_per_page' => 1, 'ignore_sticky_posts' => true ) );
}

$research_query = new WP_Query( array(
	'posts_per_page'      => 1,
	'ignore_sticky_posts' => true,
	'category_name'       => 'journal-articles,research,research-reviews',
) );

$essay_query = new WP_Query( array(
	'posts_per_page'      => 1,
	'ignore_sticky_posts' => true,
	'category_name'       => 'perspective-essays,essays,op-ed',
) );

// Lead story defaults / extraction
$lead_title   = 'The Birds of Wesleyan and How To Find Them';
$lead_type    = 'Feature';
$lead_excerpt = 'Begin with observation. A field-guided exploration of avian ecology across campus, from campus canopies to the Wadsworth arboretum border.';
$lead_url     = home_url( '/birds-of-wesleyan/' );

if ( $feature_query->have_posts() ) {
	$feature_query->the_post();
	$lead_title   = get_the_title();
	$type_obj     = wessci_article_type( get_the_ID() );
	$lead_type    = $type_obj ? wessci_term_name( $type_obj ) : 'Feature';
	$lead_excerpt = get_the_excerpt();
	$lead_url     = get_permalink();
	wp_reset_postdata();
}
?>

<main id="main" tabindex="-1" data-od-id="main-content">
	<section class="environment" aria-labelledby="home-title" data-od-id="branching-environment">
		<div class="environment-title">
			<h1 id="home-title" data-od-id="home-title">Knowledge<br>takes root.</h1>
			<p>Wesleyan science, through a different lens.</p>

			<section class="selected-story" id="selected-story" aria-label="Selected story" aria-live="polite" aria-atomic="true" data-od-id="selected-story">
				<div class="selected-story-body">
					<h2 data-od-id="selected-story-title"><?php echo esc_html( $lead_title ); ?></h2>
					<p><?php echo esc_html( $lead_type ); ?></p>
					<a class="read-link" href="<?php echo esc_url( $lead_url ); ?>"><span class="btn-text">Read the story</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
				</div>
			</section>

			<p class="environment-note" data-od-id="exploration-note">Follow a branch to preview a story. The directory below provides direct reading access.</p>
		</div>

		<div class="tree-composition">
			<img class="tree" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/tree.svg' ); ?>" width="1600" height="1100" alt="Red science tree with laboratory glassware on branch supports." fetchpriority="high" data-od-id="tree-artwork">
			<svg class="tree-cardinal" id="cardinal-svg" data-od-id="cardinal-artwork" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 370 470" width="370" height="470" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" role="img" aria-label="Cardinal perched on science tree">
          <style>
            /* Cardinal SVGator Articulated Avian Kinematics */
            #cardinal-tail,
            #cardinal-feet,
            #cardinal-body,
            #cardinal-head,
            #cardinal-crest,
            #cardinal-beak,
            #beak-lower,
            #cardinal-eye,
            #cardinal-folded-wing {
              transform-box: view-box;
              transform-origin: initial;
              transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            #cardinal-body {
              transform-origin: 645px 360px;
              animation: cardinalBreathe 2.4s ease-in-out infinite;
            }

            #cardinal-tail {
              transform-origin: 678px 403px;
              animation: cardinalTailBob 3.0s cubic-bezier(0.25, 1, 0.5, 1) infinite;
            }

            #cardinal-head {
              transform-origin: 588px 272px;
              animation: cardinalHeadSnap 6.0s cubic-bezier(0.2, 0.9, 0.3, 1) infinite;
            }

            #cardinal-crest {
              transform-origin: 595px 208px;
              animation: cardinalCrestSnap 6.0s cubic-bezier(0.2, 0.9, 0.3, 1) infinite;
            }

            #beak-lower {
              transform-origin: 545px 257px;
              animation: cardinalBeakChirp 6.0s ease-in-out infinite;
            }

            #cardinal-eye {
              transform-origin: 570px 239px;
              animation: cardinalDoubleBlink 3.2s infinite;
            }

            #cardinal-feet {
              transform: none !important;
              transform-origin: 661px 455px;
            }

            /* Standalone hover */
            #cardinal-svg:hover #cardinal-head {
              animation: none !important;
              transform: rotate(12deg) translate(2px, -1px);
            }

            #cardinal-svg:hover #cardinal-crest {
              animation: none !important;
              transform: rotate(-16deg) translateY(-3px) scaleY(1.08);
            }

            #cardinal-svg:hover #cardinal-tail {
              animation: none !important;
              transform: rotate(-8deg);
            }

            #cardinal-svg:hover #cardinal-body {
              animation: none !important;
              transform: scale(1.025);
            }

            #cardinal-svg:hover #cardinal-feet {
              transform: none !important;
            }

            @keyframes cardinalBreathe {
              0%, 100% {
                transform: scale(1);
              }
              50% {
                transform: scale(1.025);
              }
            }

            @keyframes cardinalTailBob {
              0%, 100% {
                transform: rotate(0deg);
              }
              14% {
                transform: rotate(-7deg);
              }
              22% {
                transform: rotate(4.5deg);
              }
              30% {
                transform: rotate(0.5deg);
              }
              45%, 55% {
                transform: rotate(0deg);
              }
              62% {
                transform: rotate(-5.5deg);
              }
              70% {
                transform: rotate(3.5deg);
              }
              80% {
                transform: rotate(-1deg);
              }
            }

            @keyframes cardinalHeadSnap {
              0%, 18% {
                transform: rotate(0deg);
              }
              21%, 42% {
                transform: rotate(-7.5deg) translate(-2px, 3px);
              }
              45%, 68% {
                transform: rotate(9.5deg) translate(2px, -2px);
              }
              71%, 86% {
                transform: rotate(3.5deg) translate(1px, 0);
              }
              90%, 100% {
                transform: rotate(0deg);
              }
            }

            @keyframes cardinalCrestSnap {
              0%, 18% {
                transform: rotate(0deg);
              }
              21%, 42% {
                transform: rotate(3.5deg);
              }
              45%, 68% {
                transform: rotate(-14deg) translateY(-2px) scaleY(1.08);
              }
              71%, 86% {
                transform: rotate(-6deg);
              }
              90%, 100% {
                transform: rotate(0deg);
              }
            }

            @keyframes cardinalBeakChirp {
              0%, 46% {
                transform: rotate(0deg);
              }
              48%, 51% {
                transform: rotate(3.5deg);
              }
              53%, 100% {
                transform: rotate(0deg);
              }
            }

            @keyframes cardinalDoubleBlink {
              0%, 88% {
                transform: scaleY(1);
              }
              90% {
                transform: scaleY(0.05);
              }
              92% {
                transform: scaleY(1);
              }
              94% {
                transform: scaleY(0.05);
              }
              96%, 100% {
                transform: scaleY(1);
              }
            }

            @media (prefers-reduced-motion: reduce) {
              #cardinal-tail,
              #cardinal-head,
              #cardinal-crest,
              #cardinal-eye,
              #cardinal-body,
              #beak-lower {
                animation: none !important;
                transition: none !important;
                transform: none !important;
              }
            }
          </style>
          <g id="cardinal-in-knowledge-tree" transform="translate(-495 -165)">
            <g id="cardinal-tail">
              <path id="tail-long" d="M678,403c45,36,98,112,166,215-8,2-21-3-27-7-63-52-116-117-160-169l21-39" fill="#771a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="tail-second" d="M690,407c38,42,103,127,162,203h-12c-60-52-119-114-169-172l19-31" fill="#d82840" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="tail-leading" d="M699,421c40,48,100,125,153,189-25-16-50-44-75-71L699,421" fill="#ea3850" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="tail-shaft-0" d="M690,438c42,50,93,121,128,158" fill="none" stroke="#ead7bb" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
              <path id="tail-shaft-1" d="M695,443c43,49,93,119,129,156" fill="none" stroke="#ff94a4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
              <path id="tail-shaft-2" d="M700,448c44,48,93,117,130,154" fill="none" stroke="#ead7bb" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
              <path id="tail-shaft-3" d="M705,453c45,47,93,115,131,152" fill="none" stroke="#ff94a4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
              <path id="tail-shaft-4" d="M710,458c46,46,93,113,132,150" fill="none" stroke="#ead7bb" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
            </g>
            <g id="cardinal-feet">
              <path id="leg-back" d="M642,420l-8,30l11,27-18,10" fill="none" stroke="#a8737b" stroke-width="3.1" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="leg-front" d="M670,426l-10,30l15,24l20-1" fill="none" stroke="#d299a0" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="front-toes" d="M675,480c-7,0-16,4-20,9l11-1" fill="none" stroke="#d299a0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="rear-toe" d="M645,477l10,5l5,8" fill="none" stroke="#a8737b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <g id="cardinal-body">
              <path id="cardinal-body-silhouette" d="M572,265c21-12,52-5,75,16c21,19,45,42,69,71c28,34,21,73-6,93-30,20-78,2-104-30-25-33-35-78-42-110-4-17-1-29,8-40v0" fill="#cf2338" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-shade" d="M566,286c9,41,13,79,40,122c22,35,65,53,98,40-55-1-84-40-95-77-8-37-15-71-28-91l-15,6" fill="#ad2033" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="mantle" d="M609,269c48,11,91,61,111,96-37-25-67-38-100-41-15-19-16-37-11-55v0" fill="#de2a3e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-0" d="M580,318c2,6,7,11,11,13" fill="none" stroke="#d44c5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-1" d="M583.428571,325c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-2" d="M586.857143,332c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-3" d="M590.285714,339c2,6,7,11,11,13" fill="none" stroke="#d44c5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-4" d="M593.714286,346c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-5" d="M597.142857,353c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-6" d="M600.571429,360c2,6,7,11,11,13" fill="none" stroke="#d44c5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-7" d="M604,367c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-8" d="M607.428571,374c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-9" d="M610.857143,381c2,6,7,11,11,13" fill="none" stroke="#d44c5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-10" d="M614.285714,388c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-11" d="M617.714286,395c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-12" d="M621.142857,402c2,6,7,11,11,13" fill="none" stroke="#d44c5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-13" d="M624.571429,409c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="breast-feather-14" d="M628,416c2,6,7,11,11,13" fill="none" stroke="#be3447" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <g id="cardinal-head">
              <path id="head-silhouette" d="M553,229c5-19,24-30,42-32l38-23-9,27l9-4-6,18c18,17,24,37,11,56-10,14-30,25-50,20-24-6-40-19-45-37-2-9,4-19,10-25v0" fill="#cf2338" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <g id="cardinal-crest">
                <path id="crest-feather-main" d="M588,206l38-24-14,28-24-4" fill="#e93b4c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path id="crest-feather-dark" d="M612,210l18-12-5,21-13-9" fill="#b02034" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </g>
              <path id="cardinal-face" d="M554,227c10-5,20-7,30-1c6,8,7,18,13,25c7,8,16,15,13,29-15,6-35-1-48-10l-15-10-4-14l11-19" fill="#101010" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="brow-feather" d="M553,225c10-12,24-13,37-6-15-2-26,3-37,6v0" fill="#ef5665" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <g id="cardinal-beak">
                <path id="beak-upper" d="M548,244c-16-2-33,4-45,13h42l3-13" fill="#e36570" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path id="beak-lower" d="M503,257h42l5,10c-18,3-34-3-47-10v0" fill="#a53446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path id="beak-line" d="M506,257h39" fill="none" stroke="#602032" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
              </g>
              <g id="cardinal-eye">
                <ellipse id="eye-rim" rx="4.5" ry="4.5" transform="translate(570 239)" fill="#8e5f67"/>
                <ellipse id="eye-dark" rx="3.4" ry="3.4" transform="translate(570 239)" fill="#171116"/>
                <ellipse id="eye-light" rx="1.1" ry="1.1" transform="translate(569 238)" fill="#f1dce0"/>
              </g>
              <path id="nape-hatch-0" d="M629,235c6,4,6,11,1,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="nape-hatch-1" d="M629.9,240c5.6,4,5.1,11-.9,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="nape-hatch-2" d="M630.8,245c5.2,4,4.2,11-2.8,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="nape-hatch-3" d="M631.7,250c4.8,4,3.3,11-4.7,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="nape-hatch-4" d="M632.6,255c4.4,4,2.4,11-6.6,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="nape-hatch-5" d="M633.5,260c4,4,1.5,11-8.5,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="nape-hatch-6" d="M634.4,265c3.6,4,.6,11-10.4,15" fill="none" stroke="#e64a5b" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <g id="cardinal-folded-wing">
              <path id="wing-dark-mass" d="M613,295c24-9,55,9,78,36c30,34,44,72,42,106-23-2-46-18-70-40-31-29-56-64-50-102v0" fill="#651b2b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-0" d="M632,326c10,7,61,61,52,79-9-5-45-53-52-79v0" fill="#962137" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-0" d="M636,337c11,21,44,58,48,65" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="flight-feather-1" d="M640.5,331c10,7,60,60,51,78-9-5-44-52-51-78v0" fill="#801f32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-1" d="M644.5,342c11,21,43,57,47,64" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="flight-feather-2" d="M649,336c10,7,59,59,50,77-9-5-43-51-50-77v0" fill="#962137" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-2" d="M653,347c11,21,42,56,46,63" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="flight-feather-3" d="M657.5,341c10,7,58,58,49,76-9-5-42-50-49-76v0" fill="#801f32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-3" d="M661.5,352c11,21,41,55,45,62" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="flight-feather-4" d="M666,346c10,7,57,57,48,75-9-5-41-49-48-75v0" fill="#962137" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-4" d="M670,357c11,21,40,54,44,61" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="flight-feather-5" d="M674.5,351c10,7,56,56,47,74-9-5-40-48-47-74v0" fill="#801f32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-5" d="M678.5,362c11,21,39,53,43,60" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="flight-feather-6" d="M683,356c10,7,55,55,46,73-9-5-39-47-46-73v0" fill="#962137" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="flight-feather-rib-6" d="M687,367c11,21,38,52,42,59" fill="none" stroke="#ff8da0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
              <path id="wing-coverts" d="M611,294c28-10,54,11,69,33l-16,6-14-6-6-10-10,5-12-9-8,2c-5-7-6-15-3-21v0" fill="#c52037" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path id="covert-feather-0" d="M619,302c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-1" d="M625,304.6c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-2" d="M631,307.2c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-3" d="M637,309.8c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-4" d="M643,312.4c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-5" d="M649,315c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-6" d="M655,317.6c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-7" d="M661,320.2c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="covert-feather-8" d="M667,322.8c1,7,5,13,10,16" fill="none" stroke="#ff9eb0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
              <path id="shoulder-light" d="M612,294c16-3,29,1,38,10" fill="none" stroke="#ffccd3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
          </g>
        </svg>
			<div class="subject-controls" role="group" aria-label="Explore subjects in the tree" data-od-id="subject-explorer">
				<button class="subject subject-field" type="button" data-story="feature" aria-pressed="true" aria-controls="selected-story" data-od-id="subject-field-science"><span class="subject-number">01</span><span class="subject-text">Field science</span><span class="subject-arrow" aria-hidden="true">↗</span></button>
				<button class="subject subject-chemistry" type="button" data-story="research" aria-pressed="false" aria-controls="selected-story" data-od-id="subject-chemistry"><span class="subject-number">02</span><span class="subject-text">Chemistry</span><span class="subject-arrow" aria-hidden="true">↗</span></button>
				<button class="subject subject-math" type="button" data-story="nohero" aria-pressed="false" aria-controls="selected-story" data-od-id="subject-mathematics"><span class="subject-number">03</span><span class="subject-text">Mathematics /<br>technology</span><span class="subject-arrow" aria-hidden="true">↗</span></button>
			</div>
		</div>
	</section>

	<section class="reading-directory" aria-labelledby="directory-title" data-od-id="reading-directory">
		<div class="directory-heading">
			<h2 id="directory-title" data-od-id="directory-title">Three ways<br>into science.</h2>
			<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="read-link"><span class="btn-text">Search the reading directory</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
		</div>

		<!-- 01 Feature Story -->
		<?php
		if ( $feature_query->have_posts() ) :
			while ( $feature_query->have_posts() ) : $feature_query->the_post();
				$type = wessci_article_type( get_the_ID() );
				?>
				<article class="directory-feature" data-od-id="directory-feature">
					<h3 data-od-id="directory-feature-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<div class="directory-meta">
						<span>01 / <?php echo $type ? wessci_term_name( $type ) : 'Feature'; ?></span>
						<span>Field science</span>
					</div>
					<div class="feature-under">
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
						<a href="<?php the_permalink(); ?>" class="round-link" aria-label="Read <?php echo esc_attr( get_the_title() ); ?>">↗</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<article class="directory-feature" data-od-id="directory-feature">
				<h3 data-od-id="directory-feature-title"><a href="<?php echo esc_url( home_url( '/birds-of-wesleyan/' ) ); ?>">The Birds of Wesleyan<br>and How To Find Them</a></h3>
				<div class="directory-meta"><span>01 / Feature</span><span>Field science</span></div>
				<div class="feature-under">
					<p>Begin with observation. A field-guided exploration of avian ecology across campus, from campus canopies to the Wadsworth arboretum border.</p>
					<a href="<?php echo esc_url( home_url( '/birds-of-wesleyan/' ) ); ?>" class="round-link" aria-label="Read The Birds of Wesleyan and How To Find Them">↗</a>
				</div>
			</article>
		<?php endif; ?>

		<!-- 02 Research Story -->
		<?php
		if ( $research_query->have_posts() ) :
			while ( $research_query->have_posts() ) : $research_query->the_post();
				$type = wessci_article_type( get_the_ID() );
				?>
				<article class="directory-research" data-od-id="directory-research">
					<div class="research-entry">
						<h3 data-od-id="directory-research-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="directory-meta">02 / <?php echo $type ? wessci_term_name( $type ) : 'Research'; ?></p>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 32 ) ); ?></p>
						<a class="read-link" href="<?php the_permalink(); ?>"><span class="btn-text">Enter the research spread</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<article class="directory-research" data-od-id="directory-research">
				<div class="research-entry">
					<h3 data-od-id="directory-research-title"><a href="<?php echo esc_url( home_url( '/bile-salt-micelles/' ) ); ?>">Hydroxylation Pattern of Bile Salt Governs Mixed Micelle Architecture and Peptide Association: Insights from Molecular Dynamics</a></h3>
					<p class="directory-meta">02 / Research</p>
					<p>An evidence-led investigation into the structural reorganization, hydrodynamic dimensions, and association kinetics of therapeutic peptides in mixed bile salt assemblies.</p>
					<a class="read-link" href="<?php echo esc_url( home_url( '/bile-salt-micelles/' ) ); ?>"><span class="btn-text">Enter the research spread</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
				</div>
			</article>
		<?php endif; ?>

		<!-- 03 Essay Story -->
		<?php
		if ( $essay_query->have_posts() ) :
			while ( $essay_query->have_posts() ) : $essay_query->the_post();
				$type = wessci_article_type( get_the_ID() );
				?>
				<article class="directory-essay" data-od-id="directory-essay">
					<div>
						<h3 data-od-id="directory-essay-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="directory-meta">03 / <?php echo $type ? wessci_term_name( $type ) : 'Essay'; ?></p>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
						<a class="read-link" href="<?php the_permalink(); ?>"><span class="btn-text">Read the essay</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<article class="directory-essay" data-od-id="directory-essay">
				<div>
					<h3 data-od-id="directory-essay-title"><a href="<?php echo esc_url( home_url( '/math-behind-llms/' ) ); ?>">The Math<br>Behind LLMs</a></h3>
					<p class="directory-meta">03 / Mathematics &amp; technology</p>
					<p>A text-first inquiry examining the linear algebra and probabilistic geometry that power modern transformer attention mechanisms.</p>
					<a class="read-link" href="<?php echo esc_url( home_url( '/math-behind-llms/' ) ); ?>"><span class="btn-text">Read the essay</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
				</div>
			</article>
		<?php endif; ?>
	</section>
</main>

<?php get_footer(); ?>
