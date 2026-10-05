<?php
/**
 * WesSciJo Scientific Article Meta Boxes
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WesSci_Meta_Boxes {

	/**
	 * Initialize meta box hooks.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_post', array( __CLASS__, 'save_meta_boxes' ) );
	}

	/**
	 * Register scientific publication meta boxes.
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'wessci_article_academic_meta',
			__( 'Manuscript details', 'wessci' ),
			array( __CLASS__, 'render_academic_meta_box' ),
			'post',
			'normal',
			'high'
		);

		add_meta_box(
			'wessci_figures_meta',
			__( 'Figures and captions', 'wessci' ),
			array( __CLASS__, 'render_figures_meta_box' ),
			'post',
			'normal',
			'default'
		);

		add_meta_box(
			'wessci_references_meta',
			__( 'References', 'wessci' ),
			array( __CLASS__, 'render_references_meta_box' ),
			'post',
			'normal',
			'default'
		);
	}

	/**
	 * Render academic details (Abstract, Format, Author credentials).
	 *
	 * @param WP_Post $post Current post.
	 */
	public static function render_academic_meta_box( $post ) {
		wp_nonce_field( 'wessci_save_academic_meta', 'wessci_academic_nonce' );

		$abstract   = get_post_meta( $post->ID, '_wessci_abstract', true );
		$format     = get_post_meta( $post->ID, '_wessci_format', true );
		$authors    = get_post_meta( $post->ID, '_wessci_authors', true );
		if ( ! is_array( $authors ) || empty( $authors ) ) {
			$authors = array( array( 'name' => '', 'year' => '', 'dept' => '', 'affiliation' => 'Wesleyan University' ) );
		}

		$formats = array(
			'research'    => __( 'Research Article (Peer-Reviewed / Original Data)', 'wessci' ),
			'review'      => __( 'Scientific Review', 'wessci' ),
			'feature'     => __( 'News & Feature Story', 'wessci' ),
			'perspective' => __( 'Perspective / Editorial Essay', 'wessci' ),
			'interview'   => __( 'Faculty / Researcher Interview', 'wessci' ),
		);
		?>
		<div class="wessci-meta-field">
			<label for="wessci_format"><strong><?php esc_html_e( 'Article Publication Format:', 'wessci' ); ?></strong></label>
			<select name="wessci_format" id="wessci_format" class="widefat" style="margin-top: 4px; max-width: 400px;">
				<?php foreach ( $formats as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $format, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Select whether this manuscript is primary research, a review, or an editorial feature.', 'wessci' ); ?></p>
		</div>

		<hr style="margin: 16px 0; border: 0; border-top: 1px solid #e2e2e5;">

		<div class="wessci-meta-field">
			<label for="wessci_abstract"><strong><?php esc_html_e( 'Abstract / Executive Deck:', 'wessci' ); ?></strong></label>
			<textarea name="wessci_abstract" id="wessci_abstract" rows="4" class="widefat" style="margin-top: 4px;" placeholder="<?php esc_attr_e( 'Provide the academic abstract (150–250 words) summarizing hypothesis, methodology, and key findings...', 'wessci' ); ?>"><?php echo esc_textarea( $abstract ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Displayed prominently as the academic abstract for research or introductory deck for features.', 'wessci' ); ?></p>
		</div>

		<hr style="margin: 16px 0; border: 0; border-top: 1px solid #e2e2e5;">

		<div class="wessci-meta-field">
			<label><strong><?php esc_html_e( 'Author Credentials & Affiliations:', 'wessci' ); ?></strong></label>
			<p class="description"><?php esc_html_e( 'List all student authors, co-investigators, and faculty advisors.', 'wessci' ); ?></p>
			
			<div id="wessci-authors-container" style="margin-top: 8px;">
				<?php foreach ( $authors as $index => $author ) : ?>
					<div class="wessci-author-row" style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
						<input type="text" name="wessci_authors[<?php echo (int) $index; ?>][name]" value="<?php echo esc_attr( $author['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Author Full Name', 'wessci' ); ?>" class="regular-text" style="flex: 2;">
						<input type="text" name="wessci_authors[<?php echo (int) $index; ?>][year]" value="<?php echo esc_attr( $author['year'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Class Year (e.g. \'26)', 'wessci' ); ?>" style="flex: 1; max-width: 130px;">
						<input type="text" name="wessci_authors[<?php echo (int) $index; ?>][dept]" value="<?php echo esc_attr( $author['dept'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Department / Major (e.g. MB&B)', 'wessci' ); ?>" style="flex: 2;">
						<input type="text" name="wessci_authors[<?php echo (int) $index; ?>][affiliation]" value="<?php echo esc_attr( $author['affiliation'] ?? 'Wesleyan University' ); ?>" placeholder="<?php esc_attr_e( 'Institution', 'wessci' ); ?>" style="flex: 2;">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render scientific figures metadata box.
	 *
	 * @param WP_Post $post Current post.
	 */
	public static function render_figures_meta_box( $post ) {
		$figures = get_post_meta( $post->ID, '_wessci_figures', true );
		if ( ! is_array( $figures ) || empty( $figures ) ) {
			$figures = array(
				array( 'label' => 'Figure 1', 'caption' => '', 'credit' => '', 'license' => 'Author Original' ),
			);
		}

		$licenses = array(
			'Author Original' => 'Author Original (Undergraduate research)',
			'CC BY 4.0'       => 'Creative Commons Attribution 4.0 (CC BY 4.0)',
			'CC BY-SA 4.0'    => 'Creative Commons ShareAlike (CC BY-SA)',
			'Public Domain'   => 'Public Domain / NASA / NIH Open Access',
			'Journal Fair Use'=> 'Scientific Fair Use / Review Quote',
		);
		?>
		<p class="description" style="margin-bottom: 12px;">
			<?php esc_html_e( 'Attach captions, citations, and licensing for figures and charts appearing in this paper. Check figure quality and rights against the journal\'s editorial guidance.', 'wessci' ); ?>
		</p>

		<div id="wessci-figures-container">
			<?php foreach ( $figures as $idx => $fig ) : ?>
				<div class="wessci-figure-item" style="border: 1px solid #e2e2e5; padding: 12px; margin-bottom: 12px; border-radius: 4px; background: #fafafa;">
					<div style="display: flex; gap: 12px; margin-bottom: 8px;">
						<input type="text" name="wessci_figures[<?php echo (int) $idx; ?>][label]" value="<?php echo esc_attr( $fig['label'] ?? 'Figure ' . ( $idx + 1 ) ); ?>" placeholder="Label (e.g. Figure 1)" style="flex: 1; font-weight: bold;">
						<select name="wessci_figures[<?php echo (int) $idx; ?>][license]" style="flex: 2;">
							<?php foreach ( $licenses as $lic_val => $lic_name ) : ?>
								<option value="<?php echo esc_attr( $lic_val ); ?>" <?php selected( $fig['license'] ?? '', $lic_val ); ?>><?php echo esc_html( $lic_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div style="margin-bottom: 8px;">
						<input type="text" name="wessci_figures[<?php echo (int) $idx; ?>][credit]" value="<?php echo esc_attr( $fig['credit'] ?? '' ); ?>" placeholder="Credit / Source (e.g. Confocal microscopy by Jane Doe, Wesleyan '26)" class="widefat">
					</div>
					<div>
						<textarea name="wessci_figures[<?php echo (int) $idx; ?>][caption]" rows="2" class="widefat" placeholder="Figure Caption / Descriptive note..."><?php echo esc_textarea( $fig['caption'] ?? '' ); ?></textarea>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render references / citations meta box.
	 *
	 * @param WP_Post $post Current post.
	 */
	public static function render_references_meta_box( $post ) {
		$references = get_post_meta( $post->ID, '_wessci_references', true );
		?>
		<p class="description" style="margin-bottom: 8px;">
			<?php esc_html_e( 'Enter one citation per line. If a citation includes a DOI or URL (e.g. https://doi.org/10.1038/...), it will automatically be converted to a link on the article page.', 'wessci' ); ?>
		</p>
		<textarea name="wessci_references" id="wessci_references" rows="6" class="widefat" style="font-family: monospace; font-size: 13px;" placeholder="1. Watson, J. D., & Crick, F. H. (1953). Molecular structure of nucleic acids. Nature, 171(4356), 737-738. https://doi.org/10.1038/171737a0
2. ..."><?php echo esc_textarea( $references ); ?></textarea>
		<?php
	}

	/**
	 * Save scientific article metadata.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['wessci_academic_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wessci_academic_nonce'] ) ), 'wessci_save_academic_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Abstract
		if ( isset( $_POST['wessci_abstract'] ) ) {
			update_post_meta( $post_id, '_wessci_abstract', sanitize_textarea_field( wp_unslash( $_POST['wessci_abstract'] ) ) );
		}

		// Save Format
		if ( isset( $_POST['wessci_format'] ) ) {
			update_post_meta( $post_id, '_wessci_format', sanitize_text_field( wp_unslash( $_POST['wessci_format'] ) ) );
		}

		// Save Authors
		if ( isset( $_POST['wessci_authors'] ) && is_array( $_POST['wessci_authors'] ) ) {
			$clean_authors = array();
			foreach ( $_POST['wessci_authors'] as $author ) {
				$name = sanitize_text_field( wp_unslash( $author['name'] ?? '' ) );
				if ( '' === $name ) {
					continue;
				}
				$clean_authors[] = array(
					'name'        => $name,
					'year'        => sanitize_text_field( wp_unslash( $author['year'] ?? '' ) ),
					'dept'        => sanitize_text_field( wp_unslash( $author['dept'] ?? '' ) ),
					'affiliation' => sanitize_text_field( wp_unslash( $author['affiliation'] ?? '' ) ),
				);
			}
			update_post_meta( $post_id, '_wessci_authors', $clean_authors );
		}

		// Save Figures
		if ( isset( $_POST['wessci_figures'] ) && is_array( $_POST['wessci_figures'] ) ) {
			$clean_figures = array();
			foreach ( $_POST['wessci_figures'] as $fig ) {
				$label = sanitize_text_field( wp_unslash( $fig['label'] ?? '' ) );
				if ( '' === $label ) {
					continue;
				}
				$clean_figures[] = array(
					'label'   => $label,
					'license' => sanitize_text_field( wp_unslash( $fig['license'] ?? '' ) ),
					'credit'  => sanitize_text_field( wp_unslash( $fig['credit'] ?? '' ) ),
					'caption' => sanitize_textarea_field( wp_unslash( $fig['caption'] ?? '' ) ),
				);
			}
			update_post_meta( $post_id, '_wessci_figures', $clean_figures );
		}

		// Save References
		if ( isset( $_POST['wessci_references'] ) ) {
			update_post_meta( $post_id, '_wessci_references', sanitize_textarea_field( wp_unslash( $_POST['wessci_references'] ) ) );
		}
	}
}
