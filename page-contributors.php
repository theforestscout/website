<?php 
/**
 * Template Name: Contributors
 * Template Description: Displays the site Authors/Editors/Contributors with userdata and latest posts
 *
 * @package TheForestScout
 * @version 1.0.0
 * @link https://github.com/theforestscout/website/ TheForestScout Theme
 * @author Bryan Willis <mail@bryanwillis.me>
 * @copyright   2016 Bryan Willis
 * @license MIT License
 */


remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'fs_do_author_loop');
function fs_do_author_loop(){ ?>
<h1 class="page-header text-center mb-4"><?php the_title(); ?></h1>

<div class="authorsContainer">

<?php

// number of users to return
$howManyAuthors = 500;

// Get users and count of posts put into array

$authorsArray = array();
$blogusers = get_users(['role__in' => ['editor', 'author']]);

if ($blogusers) {
	foreach($blogusers as $bloguser) {
		$post_count = count_user_posts($bloguser->ID);
		$authorsArray[$bloguser->ID] = $post_count;
	}

	arsort($authorsArray);
	$maxauthor = $howManyAuthors;
	$count = 0;
	foreach($authorsArray as $key => $value) {
		$count++;
		if ($count <= $maxauthor) {
			$user = get_userdata($key);
			$username = $user->user_login;
			$displayname = $user->display_name;
			$url = $user->user_url;
			$email = $user->user_email;
			$id = $user->ID;
			$bio = $user->description;
			$twitter = $user->twitter;
			$facebook = $user->facebook;
			$gplus = $user->googleplus;
			$author_posts_url = get_author_posts_url($key);
			$post_count = $value;
<div class="media author-bio">
    <div class="pull-left" style="width: 100px; height: 100px;">
        <div class="border-right"><?php echo get_avatar( $id, 96, '', '', null ); ?></div>
    </div>
    <div class="bio-data media-body">
            <p class="bio">
                <a class="text-primary" href="<?php echo $author_posts_url; ?>"><strong class="displayname"><?php echo $displayname; ?></strong></a> <span class="bio-description"><?php echo $bio; ?></span>
            </p>
        <div class="flex_grid recent-contributions">
        <div class="flex_col-9_xs-12"> 
                    <?php
                    //*
                          $args=array(
                            'showposts'=>3,
                            'numberposts'=>3,
                            'author'=>$user->ID,
                            'caller_get_posts'=>3
                          );
                         // */



                          $my_query = new WP_Query($args);
                          if( $my_query->have_posts() ) {
                            ?>
                            <strong><em>Latest Contributions:</em></strong><br>
                            <?php
                            while ($my_query->have_posts()) : $my_query->the_post();
                        ?>
                        <span style="display: block; margin-top: 5px;"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permenent Link To <?php the_title_attribute(); ?>"><?php the_title(); ?><small class="latest-sm">&nbsp;(<?php the_time('m.d.y') ?>)</small></a></span>
                      <?php
                            endwhile;
                         }
                      ?>
        </div>
        <div class="buttons flex_col-3_xs-12"> 
            <div class="social">
            <?php 
                if ( $email ) {
                    echo '<a class="icon-email-black" href="mailto:'.$email.'"><i class="fa fa-envelope"></i></a>';
                }

                if ( $facebook ) {
                    echo '<a class="text-btn icon-facebook-black" href="'.$facebook.'"><i class="fa fa-facebook"></i></a>';
                }
                if ( $gplus ) {
                    echo '<a class="text-btn icon-google-plus-black" href="'.$gplus.'"><i class="fa fa-google-plus"></i></a>';
                }
                if ( $twitter ) {
                    echo '<a href="https://twitter.com/'.$twitter.'" class="text-btn icon-twitter-black"><i class="fa fa-twitter"></i></a>';
                }
            ?>
            </div>
        </div>
        </div>


    </div>
</div>

<?php 

                }
              }
            }
            ?>

</div>
   <?php 
}

genesis();
