<?php
//* The Forest Scout functions.php
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'The Forest Scout' );
define( 'CHILD_THEME_URL', 'http://www.theforestscout.com/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );



function fs_enqueue_style_gridlex() {
   wp_enqueue_style( 'gridlex', get_stylesheet_directory_uri() . '/css/gridlex.css' );
   wp_enqueue_style( 'spacing', get_stylesheet_directory_uri() . '/css/spacing.css' ); 
   wp_register_script( 'sticky', get_stylesheet_directory_uri() . '/js/vendor/sticky/jquery.sticky.js', array('jquery'), '', true );
}
add_action( 'wp_enqueue_scripts', 'fs_enqueue_style_gridlex', 99 );



//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add Accessibility support
add_theme_support( 'genesis-accessibility', array( 'headings', 'drop-down-menu',  'search-form', 'skip-links', 'rems' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

add_image_size( 'blog-thumbs', 1024, 600, true );
add_image_size( 'blog-thumbs-small', 600, 300, true );


//* Custom Excerpt Lenth
function wp_trim_all_excerpt($text) {
global $post;
  $raw_excerpt = $text;
  if ( '' == $text ) {
    $text = get_the_content('');
    $text = strip_shortcodes( $text );
    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]&gt;', $text);
  }
$text = strip_tags($text);
$excerpt_length = apply_filters('excerpt_length', 30);
$excerpt_more = apply_filters('excerpt_more', ' ' . '...');
$text = wp_trim_words( $text, $excerpt_length, $excerpt_more ); 
/*$words = explode(' ', $text, $excerpt_length + 1);
  if (count($words)> $excerpt_length) {
    array_pop($words);
    $text = implode(' ', $words);
    $text = $text . $excerpt_more;
  } else {
    $text = implode(' ', $words);
  }
return $text;*/
return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wp_trim_all_excerpt');

function excerpt($limit) {
	$excerpt = explode(' ', get_the_excerpt() , $limit);
	if (count($excerpt) >= $limit) {
		array_pop($excerpt);
		$excerpt = implode(" ", $excerpt) . '...';
	}
	else {
		$excerpt = implode(" ", $excerpt);
	}
	$excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);
	return $excerpt;
}
function content($limit)
{
	$content = explode(' ', get_the_content() , $limit);
	if (count($content) >= $limit) {
		array_pop($content);
		$content = implode(" ", $content) . '...';
	}
	else {
		$content = implode(" ", $content);
	}
	$content = preg_replace('/\[.+\]/', '', $content);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}



//* Disable Category Deletion
add_action( 'delete_term_taxonomy', 'wpse_70758_del_tax', 10, 1 );
add_action( 'edit_term_taxonomies', 'wpse_70758_del_child_tax', 10, 1 );
add_filter( 'manage_edit-category_columns', 'wpse_70758_cat_edit_columns' );
add_filter( 'manage_category_custom_column', 'wpse_70758_cat_custom_columns', 10, 3 );
$undeletable = array( 
		'features'
	,	'in-between-the-lines'
	, 	'in-hollywood'
	,	'in-lfhs'
	, 	'in-music' 
	,	'in-our-opinion'
	, 	'in-style' 
    ,   'in-the-news'
	);
function wpse_70758_del_tax( $tt_id )
{
    global $undeletable;
    $term = get_term_by( 'id', $tt_id, 'category' );
    if( in_array( $term->slug, $undeletable ) ) 
        wp_die( 'cant delete' );
}
function wpse_70758_del_child_tax( $arr_ids )
{
    global $undeletable;
    foreach( $arr_ids as $id )
    {
        $term   = get_term_by( 'id', $id, 'category' );
        $parent = get_term_by( 'id', $term->parent, 'category' );
        if( in_array( $parent->slug, $undeletable ) ) 
            wp_die( 'cant delete' );        
    }
}
function wpse_70758_cat_edit_columns( $columns )
{
    $columns['tt_id'] = 'ID';   
    $columns['undeletable'] = 'Undeletable';   
    return $columns;
}   
function wpse_70758_cat_custom_columns( $value, $name, $tt_id )
{
    if( 'tt_id' == $name ) 
        echo $tt_id;
    global $undeletable;
    $term = get_term_by( 'id', $tt_id, 'category' );
    if( 'undeletable' == $name && in_array( $term->slug, $undeletable ) )
        echo '<span style="color:#f00;font-size:5em;line-height:.5em">&#149;</span>';
}

//* Remove the edit link
add_filter ( 'genesis_edit_post_link' , '__return_false' );

//* Remove Genesis Header
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

add_action( 'genesis_header', function() {
  get_template_part( 'templates/parts/header', 'main' );
});

add_action( 'genesis_footer', function() {
  get_template_part( 'templates/parts/footer', 'main' );
}, 8);

add_filter('genesis_footer_output', 'fs_genesis_footer_output', 10, 3);
function fs_genesis_footer_output( $output, $backtotop_text, $creds_text ) {

$backtotop_text ='<a id="gototop" href="#" class="btn btn-primary btn-sm back-to-top" role="button" title="Click to return on the top page" data-toggle="tooltip" data-placement="left"><i class="fa fa-chevron-up"></i></a>';

$creds_text = 'Copyright [footer_copyright] <a href="'. esc_url( home_url( '/' ) ) .'" title="'. esc_attr( get_bloginfo('name') ) .' rel="nofollow"">'.get_bloginfo('name').'</a> &middot; All Rights Reserved';
$creds_text = apply_filters( 'genesis_footer_creds_text', $creds_text );
$creds = $creds_text ? sprintf( '<div class="creds text-center">%s</div></div>', $creds_text ) : '';

$output  = '<div class="site-info">';
$output .= $creds;
$output .= '</div>';
$output .= $backtotop_text;
    return $output;
}

function fs_stickynavbar_29Aug2016() {
if ( is_admin_bar_showing() ) {
    $top = '32';
} else {
    $top = '0';
}
  ?>
  <style>
@media (min-width: 992px) {
   .admin-bar #sticky-navbar.affix {
    margin-top: 32px!important;
  }
}
@media (max-width: 992px) {
    .affix {
        position: relative!important;
    }
  body {
    margin-top: 0px!important;
  }
  
}
  </style>
  <script>
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    var slice = Array.prototype.slice; // save ref to original slice()
    var splice = Array.prototype.splice; // save ref to original slice()

  var defaults = {
      topSpacing: 0,
      bottomSpacing: 0,
      className: 'is-sticky',
      wrapperClassName: 'sticky-wrapper',
      center: false,
      getWidthFrom: '',
      widthFromWrapper: true, // works only when .getWidthFrom is empty
      responsiveWidth: false,
      zIndex: 'auto'
    },
    $window = $(window),
    $document = $(document),
    sticked = [],
    windowHeight = $window.height(),
    scroller = function() {
      var scrollTop = $window.scrollTop(),
        documentHeight = $document.height(),
        dwh = documentHeight - windowHeight,
        extra = (scrollTop > dwh) ? dwh - scrollTop : 0;

      for (var i = 0, l = sticked.length; i < l; i++) {
        var s = sticked[i],
          elementTop = s.stickyWrapper.offset().top,
          etse = elementTop - s.topSpacing - extra;

        //update height in case of dynamic content
        s.stickyWrapper.css('height', s.stickyElement.outerHeight());

        if (scrollTop <= etse) {
          if (s.currentTop !== null) {
            s.stickyElement
              .css({
                'width': '',
                'position': '',
                'top': '',
                'z-index': ''
              });
            s.stickyElement.parent().removeClass(s.className);
            s.stickyElement.trigger('sticky-end', [s]);
            s.currentTop = null;
          }
        }
        else {
          var newTop = documentHeight - s.stickyElement.outerHeight()
            - s.topSpacing - s.bottomSpacing - scrollTop - extra;
          if (newTop < 0) {
            newTop = newTop + s.topSpacing;
          } else {
            newTop = s.topSpacing;
          }
          if (s.currentTop !== newTop) {
            var newWidth;
            if (s.getWidthFrom) {
                newWidth = $(s.getWidthFrom).width() || null;
            } else if (s.widthFromWrapper) {
                newWidth = s.stickyWrapper.width();
            }
            if (newWidth == null) {
                newWidth = s.stickyElement.width();
            }
            s.stickyElement
              .css('width', newWidth)
              .css('position', 'fixed')
              .css('top', newTop)
              .css('z-index', s.zIndex);

            s.stickyElement.parent().addClass(s.className);

            if (s.currentTop === null) {
              s.stickyElement.trigger('sticky-start', [s]);
            } else {
              // sticky is started but it have to be repositioned
              s.stickyElement.trigger('sticky-update', [s]);
            }

            if (s.currentTop === s.topSpacing && s.currentTop > newTop || s.currentTop === null && newTop < s.topSpacing) {
              // just reached bottom || just started to stick but bottom is already reached
              s.stickyElement.trigger('sticky-bottom-reached', [s]);
            } else if(s.currentTop !== null && newTop === s.topSpacing && s.currentTop < newTop) {
              // sticky is started && sticked at topSpacing && overflowing from top just finished
              s.stickyElement.trigger('sticky-bottom-unreached', [s]);
            }

            s.currentTop = newTop;
          }

          // Check if sticky has reached end of container and stop sticking
          var stickyWrapperContainer = s.stickyWrapper.parent();
          var unstick = (s.stickyElement.offset().top + s.stickyElement.outerHeight() >= stickyWrapperContainer.offset().top + stickyWrapperContainer.outerHeight()) && (s.stickyElement.offset().top <= s.topSpacing);

          if( unstick ) {
            s.stickyElement
              .css('position', 'absolute')
              .css('top', '')
              .css('bottom', 0)
              .css('z-index', '');
          } else {
            s.stickyElement
              .css('position', 'fixed')
              .css('top', newTop)
              .css('bottom', '')
              .css('z-index', s.zIndex);
          }
        }
      }
    },
    resizer = function() {
      windowHeight = $window.height();

      for (var i = 0, l = sticked.length; i < l; i++) {
        var s = sticked[i];
        var newWidth = null;
        if (s.getWidthFrom) {
            if (s.responsiveWidth) {
                newWidth = $(s.getWidthFrom).width();
            }
        } else if(s.widthFromWrapper) {
            newWidth = s.stickyWrapper.width();
        }
        if (newWidth != null) {
            s.stickyElement.css('width', newWidth);
        }
      }
    },
    methods = {
      init: function(options) {
        var o = $.extend({}, defaults, options);
        return this.each(function() {
          var stickyElement = $(this);

          var stickyId = stickyElement.attr('id');
          var wrapperId = stickyId ? stickyId + '-' + defaults.wrapperClassName : defaults.wrapperClassName;
          var wrapper = $('<div></div>')
            .attr('id', wrapperId)
            .addClass(o.wrapperClassName);

          stickyElement.wrapAll(function() {
            if ($(this).parent("#" + wrapperId).length == 0) {
                    return wrapper;
            }
});

          var stickyWrapper = stickyElement.parent();

          if (o.center) {
            stickyWrapper.css({width:stickyElement.outerWidth(),marginLeft:"auto",marginRight:"auto"});
          }

          if (stickyElement.css("float") === "right") {
            stickyElement.css({"float":"none"}).parent().css({"float":"right"});
          }

          o.stickyElement = stickyElement;
          o.stickyWrapper = stickyWrapper;
          o.currentTop    = null;

          sticked.push(o);

          methods.setWrapperHeight(this);
          methods.setupChangeListeners(this);
        });
      },

      setWrapperHeight: function(stickyElement) {
        var element = $(stickyElement);
        var stickyWrapper = element.parent();
        if (stickyWrapper) {
          stickyWrapper.css('height', element.outerHeight());
        }
      },

      setupChangeListeners: function(stickyElement) {
        if (window.MutationObserver) {
          var mutationObserver = new window.MutationObserver(function(mutations) {
            if (mutations[0].addedNodes.length || mutations[0].removedNodes.length) {
              methods.setWrapperHeight(stickyElement);
            }
          });
          mutationObserver.observe(stickyElement, {subtree: true, childList: true});
        } else {
          if (window.addEventListener) {
            stickyElement.addEventListener('DOMNodeInserted', function() {
              methods.setWrapperHeight(stickyElement);
            }, false);
            stickyElement.addEventListener('DOMNodeRemoved', function() {
              methods.setWrapperHeight(stickyElement);
            }, false);
          } else if (window.attachEvent) {
            stickyElement.attachEvent('onDOMNodeInserted', function() {
              methods.setWrapperHeight(stickyElement);
            });
            stickyElement.attachEvent('onDOMNodeRemoved', function() {
              methods.setWrapperHeight(stickyElement);
            });
          }
        }
      },
      update: scroller,
      unstick: function(options) {
        return this.each(function() {
          var that = this;
          var unstickyElement = $(that);

          var removeIdx = -1;
          var i = sticked.length;
          while (i-- > 0) {
            if (sticked[i].stickyElement.get(0) === that) {
                splice.call(sticked,i,1);
                removeIdx = i;
            }
          }
          if(removeIdx !== -1) {
            unstickyElement.unwrap();
            unstickyElement
              .css({
                'width': '',
                'position': '',
                'top': '',
                'float': '',
                'z-index': ''
              })
            ;
          }
        });
      }
    };

  // should be more efficient than using $window.scroll(scroller) and $window.resize(resizer):
  if (window.addEventListener) {
    window.addEventListener('scroll', scroller, false);
    window.addEventListener('resize', resizer, false);
  } else if (window.attachEvent) {
    window.attachEvent('onscroll', scroller);
    window.attachEvent('onresize', resizer);
  }

  $.fn.sticky = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error('Method ' + method + ' does not exist on jQuery.sticky');
    }
  };

  $.fn.unstick = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method ) {
      return methods.unstick.apply( this, arguments );
    } else {
      $.error('Method ' + method + ' does not exist on jQuery.sticky');
    }
  };
  $(function() {
    setTimeout(scroller, 0);
  });
}));

  jQuery(document).ready(function($) {
      /* $("#crestashareiconincontent").sticky({bottomSpacing:1400, topSpacing:140}); */
      $("#sticky-navbar").css({
          "top": "0",
          "width": "100%",
          "left": "0",
          "zIndex": "60"
      });
      $("#sticky-navbar").affix({
          offset: {
              top: function() {
                  return this.top = $("#sticky-navbar").offset().top - <?php echo $top; ?>;
              }
          }
      });
      $("#sticky-navbar").on("affix.bs.affix", function() {
          $("body").css("margin-top", $("#sticky-navbar").outerHeight(!0));
      });
      $("#sticky-navbar").on("affix-top.bs.affix", function() {
          $("body").css({
              "margin-top": "0"
      });
      });
  });
  </script>
  <?php
}
add_action('wp_footer', 'fs_stickynavbar_29Aug2016', 99999);



add_action('after_switch_theme', 'brw_auto_set_license_keys');
function brw_auto_set_license_keys() {
  if ( !get_option('acf_pro_license') && defined('ACF_5_KEY') ) {
    $save = array(
    'key' => ACF_5_KEY,
    'url' => home_url()
  );
  $save = maybe_serialize($save);
  $save = base64_encode($save);
    update_option('acf_pro_license', $save);
  }
}


add_action( 'genesis_before', 'do_google_tag_manager', 99);
function do_google_tag_manager() {
if ( !is_user_logged_in() || current_user_can('subscriber') ) { ?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-MLSJCJ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MLSJCJ');</script>
<!-- End Google Tag Manager -->
<?php }
}
