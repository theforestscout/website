<?php
 /**
  * Category Template: FS Categories
  * Template Description: The main template that shows the top level categories
  *
  * @package TheForestScout
  * @version 1.0.0
  * @link https://github.com/theforestscout/website/ TheForestScout Theme
  * @author Bryan Willis <mail@bryanwillis.me>
  * @copyright   2016 Bryan Willis
  * @license MIT License
  */
 
function featured_banner() {
$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;  
// load thumbnail for this taxonomy term (term object)
$thumbnail = get_field('department_featured_banner', $queried_object);
// load thumbnail for this taxonomy term (term string)
$thumbnail = get_field('department_featured_banner', $taxonomy . '_' . $term_id);
if ( $thumbnail ) : ?>
<div class="container-fluid banner-container">
	<div class="background-wrapper">
		<div class="department-background-image">
			<img class="featured-banner-img" nopin ="nopin" src="<?php echo $thumbnail ?>">
		</div>
	</div>
</div>
<?php endif;
}
add_action ('genesis_after_header', 'featured_banner');
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'fs_do_in_between_the_lines_loop');
function fs_do_in_between_the_lines_loop() {
            $the_query = new WP_Query([
                'category_name' => get_query_var( 'category_name' ),
                'posts_per_page' => -1,
                'meta_query' => [
                'relation' => 'AND',
                 [
                    'key'     => '_thumbnail_id',
                    'compare' => 'EXISTS',
                 ]
                ],
                'post_status'    => 'publish',
                'orderby'        => 'date'
            ]);
if ( $the_query->have_posts() ) : ?>
  <div class="flex_grid">
  <?php while ( $the_query->have_posts() ) : $the_query->the_post(); 
            if ( is_sticky() ) : ?>
              <div id="post-<?php the_ID(); ?>" class="flex_col-12">
            <?php else: ?>
              <div id="post-<?php the_ID(); ?>" class="flex_col-6_sm-12">
            <?php endif; ?>
                <article class="thumbnail item" itemscope="" itemtype="http://schema.org/CreativeWork">
                    <a class="blog-thumb-img" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <?php the_post_thumbnail('blog-thumbs-small'); ?>
                    </a>
                    <?php 
                    if ( function_exists( "get_Yuzo_Views" ) ) {
                        $view_count = get_Yuzo_Views();
                        $trending = '1000';
                        if ( $view_count >= $trending ) {
                          echo '<p class="post-views text-muted pull-right" style="margin-top: 5px;"><i class="el el-fire color_flare_hot"></i> ';
                          echo get_Yuzo_Views(); 
                          echo '</p>';
                        } else {
                          echo '<p class="post-views text-muted pull-right" style="margin-top: 5px;"><i class="el el-fire color_flare_normal"></i> ';
                          echo get_Yuzo_Views(); 
                          echo '</p>';
                        }
                      }
                    ?>
                    <div class="caption">
                        <h2 itemprop="headline"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                        <p itemprop="text" class="flex-text text-muted"><?php echo excerpt(55); ?></p> 
                        <p class="post-date text-muted"><i class="fa fa-clock-o"></i> <?php echo get_the_date(); ?></p> 
                    </div>
                    <!-- /.caption -->
                </article>
            </div>
  <?php endwhile; ?>

  </div>
  <?php wp_reset_postdata(); ?>
<?php endif;

}

genesis();
?>
