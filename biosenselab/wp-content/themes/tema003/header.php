<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset') ?>" />
<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
<!-- Created by Artisteer v4.0.0.58475 -->
<meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url') ?>" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
remove_action('wp_head', 'wp_generator');
if (is_singular() && get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
}
wp_head();
?>
</head>
<body <?php body_class(); ?>>

<div id="art-main">
    <div id="art-header-bg" class="clearfix">
            </div>
    <div id="art-hmenu-bg" class="art-bar art-nav clearfix">
    </div>
    <div class="art-sheet clearfix">

<?php if(theme_has_layout_part("header")) : ?>
<header class="clearfix art-header<?php echo (theme_get_option('theme_header_clickable') ? ' clickable' : ''); ?>"><?php get_sidebar('header'); ?>



    <div class="art-shapes">


            </div>

<nav class="art-nav clearfix">
    <div class="art-nav-inner">
    <?php
	echo theme_get_menu(array(
			'source' => theme_get_option('theme_menu_source'),
			'depth' => theme_get_option('theme_menu_depth'),
			'menu' => 'primary-menu',
			'class' => 'art-hmenu'
		)
	);
?> 
        </div>
    </nav>

                    
</header>
<?php endif; ?>

<div class="art-layout-wrapper clearfix">
                <div class="art-content-layout">
                    <div class="art-content-layout-row">
                        <?php get_sidebar(); ?>
                        <div class="art-layout-cell art-content clearfix">
