<?php 
/**
 * Plugin Name:       Ajax CPT and Taxonomy Filter  
 * Plugin URI:        #
 * Description:       Handle Ajax Search and Filter For CPT and Taxonomy
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shahala Anjum
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-cpt-taxonomy-filter
 */






 //enque scripts

function ajax_filter_enqueues() {
   
    wp_enqueue_script( 'filter', plugins_url( 'filter.js', __FILE__ ));
    wp_localize_script( 'ajax-search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

add_action( 'wp_enqueue_scripts', 'ajax_filter_enqueues' );

$atts = shortcode_atts(
	array(
		'postype' => 'post',
		'taxtype' => 'category',
	), $atts, 'ajax_posts_filter' );

function ajax_sht($atts){

		 ?>
	<div class="container">
		
		<div class="portfolio-categories-list">
			
			
			<?php 
			if(!empty($atts['taxtype']) ){

				//passing more than one value with comma seperated
				$no_whitespaces = preg_replace( '/\s*,\s*/', ',', filter_var( $atts['taxtype'], FILTER_SANITIZE_STRING ) ); 
    			$tax_array = explode( ',', $no_whitespaces );

				foreach($tax_array as $onetax){
					$categories = get_terms( $onetax, 'hide_empty=0'); 
					// echo '<pre>';
					// print_r($categories);
					echo '<input type="hidden" id="filters-'.$onetax.'" />
					<ul class="'.$onetax.'-list ">' ;  ?>
						<li><a href="javascript:;" class="filter-link portfolio-cat-item cat-list_item active" data-slug="" data-id="">All </a></li>
						<?php foreach($categories as $category) : ?>
							<li>
								<a href="javascript:;" class="filter-link cat-list_item" data-slug="<?= $category->slug; ?>" data-type="<?php echo $onetax; ?>" data-id="<?= $category->term_id; ?>">
									<?= $category->name; ?>
								</a>
								<span class="remove"><i class="fas fa-times"></i></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php }
			?>
			
			<?php } ?>
		</div>
		<div class="projects-grid">
			<?php 
			$projects = new WP_Query([
				'post_type' => $atts['postype'],
				'posts_per_page' => 4,
				'order_by' => 'name',
			]);
			?>
			<div class="count" id="result-count"></div>
			<?php if($projects->have_posts()): ?>
				<ul class="project-tiles-portfolio project-tiles">
					<?php
					while($projects->have_posts()) : $projects->the_post();
						echo '<li>';
							the_title();
						echo '</li>';
					endwhile;
					?>
				</ul>
				<?php wp_reset_postdata(); ?>
				<div id="more_blog_posts">Load More</div>
				
			<?php 
			else :
				echo 'No posts found';
			endif; ?>
		</div>
	</div>
<?php 
}

add_shortcode( 'ajax_posts_filter', 'ajax_sht' );
 //The PHP WordPress Filter,


 function filter_blogs() {
	$catIds = $_POST['catIds'];
	$tagIds = $_POST['tagIds'];

	$page = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
	$args = [
		'post_type' => 'post',
		'posts_per_page' => 8,
		'post_status'  => 'publish',
		'orderby'        => 'publish_date',
		'paged'    => $page,
	];
	// project Category
	if (!empty($catIds)) {
		$args['tax_query'][] = [
			'taxonomy'      => 'category',
			'field'			=> 'term_id',
			'terms'         => $catIds,
			'operator'      => 'IN'
		];
	}
	// project tag 
	
	if (!empty($tagIds)) {
		$args['tax_query'][] = [
			'taxonomy'      => 'post_tag',
			'field'			=> 'term_id',
			'terms'         => $tagIds,
			'operator'      => 'IN'
		];
	}
	$response = '';
	$ajaxproducts = new WP_Query($args);
	if ( $ajaxproducts->have_posts() ) {
		ob_start();
		while ( $ajaxproducts->have_posts() ) : $ajaxproducts->the_post();
			// $response = get_template_part('page-templates/page/content/content', 'page');
            //echo $response = get_the_title().','; ?>
				<div class="news_blog_module_list">
					<figure class="news_blog_img" style="background-image: url(<?php the_field('featured_image'); ?>);">
						<!-- IMage define in BG -->
						<a href="<?php echo get_permalink(); ?>" title=""><!--link Define Here --></a>
					</figure>
					<?php
					$postcategories = get_the_category();
					$category_list = join( ', ', wp_list_pluck( $postcategories, 'name' ) ); 
					?>
					<!-- <h5><?php //echo wp_kses_post( $category_list );  ?></h5> -->
					<h3><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
				</div>
		<?php
		endwhile;
		$output = ob_get_contents();
		ob_end_clean();
	} else {
        $output = '<h1 class="no-posts">No posts found</h1>' ;
		$no_projects = 1;
	}
	$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;
	$total   = isset( $ajaxproducts->max_num_pages ) ? $ajaxproducts->max_num_pages : 1;
    $counter = $ajaxproducts->max_num_pages;
	$result = [
		'current' => $current,
		'total'   => $total,
		'html' => $output,
		'no_projects' => $no_projects,
	];
	
	echo json_encode($result);
	wp_reset_postdata();
	exit;
}
add_action('wp_ajax_filter_blogs', 'filter_blogs');
add_action('wp_ajax_nopriv_filter_blogs', 'filter_blogs');
 




