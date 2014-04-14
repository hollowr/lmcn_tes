<?php
/**
 * lmcn functions file
 *
 * @package WordPress
 * @subpackage lmcn
 * @since lmcn 1.0
 */


/******************************************************************************\
	Theme support, standard settings, menus and widgets
\******************************************************************************/

add_theme_support( 'post-formats', array( 'image', 'quote', 'status', 'link' ) );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );

$custom_header_args = array(
	'width'         => 200,
	'height'        => 113,
	'default-image' => get_template_directory_uri() . '/images/logo.png',
);
add_theme_support( 'custom-header', $custom_header_args );
if (function_exists('add_image_size')) {
   add_image_size( 'dummy-1', 200, 113, true );
   add_image_size( 'dummy-2', 480, 0 );
}
 
global $_wp_additional_image_sizes;
foreach ( $_wp_additional_image_sizes as $name => $image_size ){
    update_option( $name."_size_w", $image_size['width'] );
    update_option( $name."_size_h", $image_size['height'] );
    update_option( $name."_crop", $image_size['crop'] );
}
 
add_filter( 'intermediate_image_sizes', 'regenerate_custom_image_sizes' );
function regenerate_custom_image_sizes( $sizes ){
    global $_wp_additional_image_sizes;
    foreach ( $_wp_additional_image_sizes as $name => $size ){
        $sizes[] = $name;
    }
    return $sizes;
}
/**
 * Print custom header styles
 * @return void
 */
function lmcn_custom_header() {
	$styles = '';
	if ( $color = get_header_textcolor() ) {
		echo '<style type="text/css"> ' .
				'.site-header .logo .blog-name, .site-header .logo .blog-description {' .
					'color: #' . $color . ';' .
				'}' .
			 '</style>';
		echo "<link href='http://fonts.googleapis.com/css?family=Prata' rel='stylesheet' type='text/css'>";
	}
}
add_action( 'wp_head', 'lmcn_custom_header', 11 );

$custom_bg_args = array(
	'default-color' => 'fba919',
	'default-image' => '',
);
add_theme_support( 'custom-background', $custom_bg_args );

register_nav_menu( 'main-menu', __( 'Your sites main menu', 'lmcn' ) );
if ( !is_nav_menu('Navigation') || !is_nav_menu('Top menu') ) {
	$menu_id1 = wp_create_nav_menu('Navigation');
	$menu_id2 = wp_create_nav_menu('Top menu');
	wp_update_nav_menu_item($menu_id1, 1);
	wp_update_nav_menu_item($menu_id2, 1);
}

class description_walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth, $args)
	{
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		$prepend = '<strong>';
		$append = '</strong>';
		$description = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

		if($depth != 0)
			{
				$description = $append = $prepend = "";
			}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
		$item_output .= $description.$args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
remove_filter('nav_menu_description', 'strip_tags');
if ( function_exists( 'register_sidebars' ) ) {
	register_sidebar(
		array(
			'id' => 'home-sidebar',
			'name' => __( 'Home widgets', 'lmcn' ),
			'description' => __( 'Shows on home page', 'lmcn' )
		)
	);

	register_sidebar(
		array(
			'id' => 'footer-sidebar',
			'name' => __( 'Footer widgets', 'lmcn' ),
			'description' => __( 'Shows in the sites footer', 'lmcn' )
		)
	);
}

if ( ! isset( $content_width ) ) $content_width = 650;

/**
 * Include editor stylesheets
 * @return void
 */
function lmcn_editor_style() {
    add_editor_style( 'css/wp-editor-style.css' );
}
add_action( 'init', 'lmcn_editor_style' );


/******************************************************************************\
	Scripts and Styles
\******************************************************************************/

/**
 * Enqueue lmcn scripts
 * @return void
 */
function lmcn_enqueue_scripts() {
	wp_enqueue_style( 'lmcn-styles', get_stylesheet_uri(), array(), '1.0' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'default-scripts', get_template_directory_uri() . '/js/scripts.min.js', array(), '1.0', true );
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'lmcn_enqueue_scripts' );


/******************************************************************************\
	Content functions
\******************************************************************************/

/**
 * Displays meta information for a post
 * @return void
 */
function lmcn_post_meta() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			__( 'Posted %s in %s%s by %s. ', 'lmcn' ),
			get_the_time( get_option( 'date_format' ) ),
			get_the_category_list( ', ' ),
			get_the_tag_list( __( ', <b>Tags</b>: ', 'lmcn' ), ', ' ),
			get_the_author_link()
		);
	}
	edit_post_link( __( ' (edit)', 'lmcn' ), '<span class="edit-link">', '</span>' );
}
function lmcn_post_category() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			strtoupper(get_the_category_list( ', ' ))
		);
	}
}
function lmcn_post_date() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			get_the_time( get_option( 'date_format' ) )
		);
	}
	edit_post_link( __( ' (edit)', 'lmcn' ), '<span class="edit-link">', '</span>' );
}
function new_excerpt_length($length) {
	return substr($excerpt,0,strpos($excerpt,'.')+1);
}

function multi_strpos($haystack, $needles, $offset = 0) {
 
         foreach ($needles as $n) {
                 if (strpos($haystack, $n, $offset) !== false)
                         return strpos($haystack, $n, $offset);
         }
         return false;
 } 
add_filter(
  'the_excerpt',@
  function ($excerpt) {
  	$findMe = array('.', '!', '?');
    return substr($excerpt,0,multi_strpos($excerpt, $findMe)+1);
  }
);
/*** Slideshow ***/

$prefix = 'sgt_';

$meta_box = array(
	'id' => 'slide',
	'title' => 'Slideshow Options',
	'page' => 'post',
	'context' => 'side',
	'priority' => 'low',
	'fields' => array(
		array(
			'name' => 'Show in slideshow',
			'id' => $prefix . 'slide',
			'type' => 'checkbox'
		)
	)
);
add_action('admin_menu', 'lmcn_add_box');

// Add meta box
function lmcn_add_box() {
	global $meta_box;
	add_meta_box($meta_box['id'], $meta_box['title'], 'lmcn_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

// Callback function to show fields in meta box
function lmcn_show_box() {
	global $meta_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="lmcn_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($meta_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);

		echo '<tr>',
				'<th style="width:50%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
		echo     '<td>',
			'</tr>';
	}
	echo '</table>';
}

add_action('save_post', 'lmcn_save_data');

// Save data from meta box
function lmcn_save_data($post_id) {
	global $meta_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['lmcn_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($meta_box['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];

		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}

/*** Options ***/

function options_admin_menu() {
	// here's where we add our theme options page link to the dashboard sidebar
	add_theme_page("lmcn Theme Options", "Theme Options", 'edit_themes', basename(__FILE__), 'options_page');
}
add_action('admin_menu', 'options_admin_menu');

function options_page() {
	if ( $_POST['update_options'] == 'true' ) { options_update(); }  //check options update
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>lmcn Theme Options</h2>

		<form method="post" action="">
			<input type="hidden" name="update_options" value="true" />

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="logo_url"><?php _e('Custom logo URL:'); ?></label></th>
					<td><input type="text" name="logo_url" id="logo_url" size="50" value="<?php echo get_option('logo_url'); ?>"/><br/><span
							class="description"> <a href="<?php bloginfo("url"); ?>/wp-admin/media-new.php" target="_blank">Upload your logo</a> (max 500px x 500px) using WordPress Media Library and insert its URL here </span><br/><br/><img src="<?php echo (get_option('logo_url')) ? get_option('logo_url') : bloginfo('template_url') . '/images/logo.png' ?>"
					 alt=""/></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bg_color"><?php _e('Custom background color:'); ?></label></th>
					<td><input type="text" name="bg_color" id="bg_color" size="20" value="<?php echo get_option('bg_color'); ?>"/><span
							class="description"> e.g., <strong>#27292a</strong> or <strong>black</strong></span></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ss_disable"><?php _e('Disable slideshow:'); ?></label></th>
					<td><input type="checkbox" name="ss_disable" id="ss_disable" <?php echo (get_option('ss_disable'))? 'checked="checked"' : ''; ?>/></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ss_timeout"><?php _e('Timeout for slideshow (ms):'); ?></label></th>
					<td><input type="text" name="ss_timeout" id="ss_timeout" size="20" value="<?php echo get_option('ss_timeout'); ?>"/><span
							class="description"> e.g., <strong>7000</strong></span></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Pagination:'); ?></label></th>
					<td>
						<input type="radio" name="paging_mode" value="default" <?php echo (get_option('paging_mode') == 'default')? 'checked="checked"' : ''; ?>/><span class="description">WP Page-Navi support</span><br/>
						<input type="radio" name="paging_mode" value="infiniteScroll" <?php echo (get_option('paging_mode') == 'infiniteScroll')? 'checked="checked"' : ''; ?>/><span class="description">Infinite Scroll</span><br/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ga"><?php _e('Google Analytics code:'); ?></label></th>
					<td><textarea name="ga" id="ga" cols="48" rows="18"><?php echo get_option('ga'); ?></textarea></td>
				</tr>
			</table>

			<p><input type="submit" value="Save Changes" class="button button-primary" /></p>
		</form>
	</div>
<?php
}

// Update options

function options_update() {
	update_option('logo_url', $_POST['logo_url']);
	update_option('bg_color', $_POST['bg_color']);
	update_option('ss_disable', $_POST['ss_disable']);
	update_option('ss_timeout', $_POST['ss_timeout']);
	update_option('paging_mode', $_POST['paging_mode']);
	update_option('ga', stripslashes_deep($_POST['ga']));
}

// function trim_excerpt($text) {
// 	return rtrim($text,'[Translate]');
// }
// //add_filter('get_the_excerpt', 'trim_excerpt');

// function custom_wp_trim_excerpt($text) {
// 	$raw_excerpt = $text;
// 	if ( '' == $text ) {
// 		//Retrieve the post content.
// 		$text = get_the_content('');

// 		//Delete all shortcode tags from the content.
// 		$text = strip_shortcodes( $text );

// 		$text = apply_filters('the_content', $text);
// 		$text = str_replace(']]>', ']]&gt;', $text);
// 		$text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
// 		$allowed_tags = ''; /*** MODIFY THIS. Add the allowed HTML tags separated by a comma.***/
// 		$text = strip_tags($text, $allowed_tags);

// 		$excerpt_word_count = 7; ** MODIFY THIS. change the excerpt word count to any integer you like.**
// 		$excerpt_length = apply_filters('excerpt_length', $excerpt_word_count);

// 		/***$excerpt_end = '[...]';  MODIFY THIS. change the excerpt endind to something else.***/
// 		$excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);

// 		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
// 		if ( count($words) > $excerpt_length ) {
// 			array_pop($words);
// 			$text = implode(' ', $words);
// 			$text = $text . $excerpt_more;
// 		} else {
// 			$text = implode(' ', $words);
// 		}
// 	}
// 	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
// }
// remove_filter('get_the_excerpt', 'wp_trim_excerpt');
// add_filter('get_the_excerpt', 'custom_wp_trim_excerpt');