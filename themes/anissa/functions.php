<?php
/**
 * anissa functions and definitions
 *
 * @package anissa
 */

if ( ! function_exists( 'anissa_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function anissa_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on anissa, use a find and replace
	 * to change 'anissa' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'anissa', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );
	
	
	
	/**
	Custom Logo
	 */
	add_theme_support( 'custom-logo', array(
	'height'      => 300,
	'width'       => 600,
	'flex-height' => true,
	'flex-width'  => true,
	'header-text' => array( 'site-title', 'site-description' ),
) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'anissa-home', 900, 450, true );
	add_image_size( 'anissa-header', 1400, 400, true );
	add_image_size( 'anissa-carousel-pic', 480, 320, true ); 

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'anissa' ),
		'social'  => esc_html__( 'Social Links', 'anissa' ),
	) );
	
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'anissa_custom_background_args', array(
		'default-color' => 'ffffff',
	) ) );
	
	
}
endif; // anissa_setup
add_action( 'after_setup_theme', 'anissa_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function anissa_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'anissa_content_width', 900 );
}
add_action( 'after_setup_theme', 'anissa_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function anissa_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'anissa' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Sidebar 1', 'anissa' ),
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Sidebar 2', 'anissa' ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Sidebar 3', 'anissa' ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'anissa_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function anissa_scripts() {
	wp_enqueue_style( 'anissa-style', get_stylesheet_uri() );

	wp_enqueue_style( 'anissa-fonts', anissa_fonts_url(), array(), null );
	wp_enqueue_style( 'anissa-fontawesome', get_template_directory_uri() . '/fonts/font-awesome.css', array(), '4.3.0' );
	
	wp_enqueue_script( 'anissa-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'anissa-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'anissa_scripts' );



function anissa_carousel_scripts() {
   wp_enqueue_script( 'owl.carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'), '20120206', true );
    wp_enqueue_script( 'anissa-effects', get_template_directory_uri() . '/js/effects.js', array('jquery'), '20120206', true );
}
add_action( 'wp_enqueue_scripts', 'anissa_carousel_scripts' );

/**
 * Register Google Fonts
 */
function anissa_fonts_url() {
    $fonts_url = '';

   	/* Translators: If there are characters in your language that are not
	 * supported by Playfair, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$playfair = esc_html_x( 'on', 'Playfair font: on or off', 'anissa' );
	
	/* Translators: If there are characters in your language that are not
	 * supported by Montserrat, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$montserrat = esc_html_x( 'on', 'Montserrat font: on or off', 'anissa' );
	
	 /* Translators: If there are characters in your language that are not
	 * supported by Merriweather, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$merriweather = esc_html_x( 'on', 'Merriweather font: on or off', 'anissa' );
	

	if ( 'off' !== $playfair && 'off' !== $montserrat && 'off' !== $merriweather ) {
		$font_families = array();

		if ( 'off' !== $playfair ) {
			$font_families[] = 'Playfair Display:400,700';
		}
		
		if ( 'off' !== $montserrat ) {
			$font_families[] = 'Montserrat:400,700';
		}
		
		if ( 'off' !== $merriweather ) {
			$font_families[] = 'Merriweather:400,300,700';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;

}

/**
 * Enqueue Google Fonts for custom headers
 */
function anissa_admin_scripts( $hook_suffix ) {

	wp_enqueue_style( 'anissa-fonts', anissa_fonts_url(), array(), null );

}
add_action( 'admin_print_styles-appearance_page_custom-header', 'anissa_admin_scripts' );

if ( ! function_exists( 'anissa_continue_reading_link' ) ) :
/**
 * Returns an ellipsis and "Continue reading" plus off-screen title link for excerpts
 */
function anissa_continue_reading_link() {
	return '&hellip; <a class="more-link" href="'. esc_url( get_permalink() ) . '">' . sprintf( __( 'Read More <span class="screen-reader-text">%1$s</span>', 'anissa' ), esc_attr( strip_tags( get_the_title() ) ) ) . '</a>';
}
endif; // anissa_continue_reading_link


/**
 * Replaces "[...]" (appended to automatically generated excerpts) with anissa_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function anissa_auto_excerpt_more( $more ) {
	return anissa_continue_reading_link();
}
add_filter( 'excerpt_more', 'anissa_auto_excerpt_more' );


// Style the Tag Cloud
function anissa_tag_cloud_widget( $args )
{
	$args['largest'] = 12; //largest tag
	$args['smallest'] = 12; //smallest tag
	$args['unit'] = 'px'; //tag font unit
	$args['number'] = '18'; //number of tags
	return $args;
}

add_filter( 'widget_tag_cloud_args', 'anissa_tag_cloud_widget' );

// Declare WooCommerce Support
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function anissa_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= anissa_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'anissa_custom_excerpt_more' );


if ( ! function_exists( 'anissa_comments' ) ) :

/*
 * Custom comments display to move Reply link,
 * used in comments.php
 */
function anissa_comments( $comment, $args, $depth ) {
?>
		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
						<?php printf( '<b class="fn">%s</b>', get_comment_author_link() ); ?>
					</div><!-- .comment-author -->

					<div class="comment-metadata">
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID, $args ) ); ?>">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php printf( esc_html_x( '%1$s at %2$s', '1: date, 2: time', 'anissa' ), get_comment_date(), get_comment_time() ); ?>
							</time>
						</a>
						<?php edit_comment_link( esc_html__( 'Edit', 'anissa' ), '<span class="edit-link">', '</span>' ); ?>
						<?php
						comment_reply_link( array_merge( $args, array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<span class="reply">',
							'after'     => '</span>'
						) ) );
						?>
					</div><!-- .comment-metadata -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'anissa' ); ?></p>
					<?php endif; ?>
				</footer><!-- .comment-meta -->

				<div class="comment-content">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

			</article><!-- .comment-body -->
<?php
}

endif;

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';