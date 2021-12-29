<?php get_header(); ?>
    <main id="content" role="main">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php if ( ! is_front_page() ) : ?>
                <!--
                <header class="header">
                    <h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1> <?php edit_post_link(); ?>
                </header>
                -->
                <?php endif; ?>
                <div class="entry-content" itemprop="mainContentOfPage">
                    <?php
                    if (has_post_thumbnail()) {
                        echo '<section class="container">';
                        the_post_thumbnail('full', array('itemprop' => 'image'));
                        echo '</section>';
                    }
                    ?>
                    <?php the_content(); ?>
                    <div class="entry-links"><?php wp_link_pages(); ?></div>
                </div>
            </article>
            <?php if (comments_open() && !post_password_required()) {
                comments_template('', true);
            } ?>
        <?php endwhile; endif; ?>
    </main>
<?php get_footer(); ?>
