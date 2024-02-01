<?php
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles()
{
	$parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
	$theme        = wp_get_theme();

	// Enqueue parent theme style
	wp_enqueue_style(
		$parenthandle,
		get_template_directory_uri() . '/style.css',
		array(),  // If the parent theme code has a dependency, copy it to here.
		$theme->parent()->get('Version')
	);

	// Enqueue child theme style
	wp_enqueue_style(
		'child-style',
		get_stylesheet_uri(),
		array($parenthandle),
		$theme->get('Version') // This only works if you have Version defined in the style header.
	);
	// Enqueue additional child theme style (replace 'child-theme-extra' with your desired handle)
	wp_enqueue_style(
		'child-theme-extra',
		get_stylesheet_directory_uri() . '/theme.css',
		array('child-style'),  // 'child-style'은 이전에 enqueue한 스타일의 handle입니다.
		$theme->get('Version')
	);
}

add_filter( 'wp_nav_menu_items','add_admin_link', 10, 2 );
function add_admin_link( $items, $args ) {
    if (is_user_logged_in()) {
        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page">
		<a class="menu-link" href="'. get_admin_url() .'">Admin</a></li>';
    }
    return $items;
}


