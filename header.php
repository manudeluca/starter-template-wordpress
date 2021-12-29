<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width"/>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="wrapper" class="hfeed">
    <header id="header" role="banner">
        <nav id="menu" class="nav" role="navigation">
            <?php
            echo '<a href="' . esc_url(home_url('/')) . '" title="' . esc_attr(get_bloginfo('name')) . '" rel="home" class="nav__logo" itemprop="url">';
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
            if ( has_custom_logo() ) {
                echo '<img src="' . esc_url( $logo[0] ) . '" alt="' . get_bloginfo( 'name' ) . '">';
            } else {
                echo '<h1>' . esc_html(get_bloginfo('name')) . '</h1>';
            }
            echo '</a>';

            wp_nav_menu( array(
                'theme_location' => 'main-menu',
                'link_before' => '<span itemprop="name" class="nav__name">',
                'link_after' => '</span>',
                'menu_class' => 'nav__list',
                'container_class' => 'nav__menu',
                'container_id' => 'nav-menu'
            ));
            ?>

            <?php if (is_active_sidebar('menu-widget-area')) : ?>
                <div id="menu-widget" class="widget-area">
                    <?php dynamic_sidebar('menu-widget-area'); ?>
                </div>
            <?php endif; ?>
            <!--<div id="search"><?php get_search_form(); ?></div>-->
        </nav>
    </header>
    <div id="container">
