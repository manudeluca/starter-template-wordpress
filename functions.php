<?php
add_action('after_setup_theme', 'startertemplate_setup');
function startertemplate_setup()
{
    load_theme_textdomain('startertemplate', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form'));
    add_theme_support( 'custom-logo' );
    add_theme_support( 'custom-background' );
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 1920;
    }
    register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'startertemplate')));
}

// add_action('admin_notices', 'startertemplate_admin_notice');
function startertemplate_admin_notice()
{
    $user_id = get_current_user_id();
    if (!get_user_meta($user_id, 'startertemplate_notice_dismissed_3') && current_user_can('manage_options'))
        echo '<div class="notice notice-info"><p>' . __('<big><strong>startertemplate</strong>:</big> Help keep the project alive! <a href="?notice-dismiss" class="alignright">Dismiss</a> <a href="https://calmestghost.com/donate" class="button-primary" target="_blank">Make a Donation</a>', 'startertemplate') . '</p></div>';
}

// add_action('admin_init', 'startertemplate_notice_dismissed');
function startertemplate_notice_dismissed()
{
    $user_id = get_current_user_id();
    if (isset($_GET['notice-dismiss']))
        add_user_meta($user_id, 'startertemplate_notice_dismissed_3', 'true', true);
}

add_action('wp_enqueue_scripts', 'startertemplate_enqueue');
function startertemplate_enqueue()
{
    wp_enqueue_style('startertemplate-style', get_stylesheet_uri());
    wp_enqueue_script('jquery');
    wp_enqueue_style('startertemplate-style-main', get_template_directory_uri() . '/assets/css/style.css', array(), filemtime(get_template_directory() . '/assets/css/style.css'), false);
    wp_enqueue_script( 'startertemplate-script-main', get_template_directory_uri() . '/assets/js/main.js', array(), filemtime(get_template_directory() . '/assets/js/main.js'), true );

    if (is_plugin_active('gravityforms/gravityforms.php')) {
        wp_enqueue_style('startertemplate-gravityforms', get_template_directory_uri() . '/assets/css/gravityforms.css', array('gform_basic', 'gform_theme_ie11', 'gform_theme'), filemtime(get_template_directory() . '/assets/css/gravityforms.css'), false);
    }
}

add_action('wp_head', 'startertemplate_font');
function startertemplate_font()
{
    echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Barlow">';
}

add_action('wp_footer', 'startertemplate_footer');
function startertemplate_footer()
{
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var deviceAgent = navigator.userAgent.toLowerCase();
            if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
                $("html").addClass("ios");
            }
            if (navigator.userAgent.search("MSIE") >= 0) {
                $("html").addClass("ie");
            } else if (navigator.userAgent.search("Chrome") >= 0) {
                $("html").addClass("chrome");
            } else if (navigator.userAgent.search("Firefox") >= 0) {
                $("html").addClass("firefox");
            } else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
                $("html").addClass("safari");
            } else if (navigator.userAgent.search("Opera") >= 0) {
                $("html").addClass("opera");
            }
        });
    </script>
    <?php
}

add_filter('document_title_separator', 'startertemplate_document_title_separator');
function startertemplate_document_title_separator($sep)
{
    $sep = '|';
    return $sep;
}

add_filter('the_title', 'startertemplate_title');
function startertemplate_title($title)
{
    if ($title == '') {
        return '...';
    } else {
        return $title;
    }
}

add_filter('nav_menu_link_attributes', 'startertemplate_schema_url', 10);
function startertemplate_schema_url($atts)
{
    $atts['itemprop'] = 'url';
    return $atts;
}

if (!function_exists('startertemplate_wp_body_open')) {
    function startertemplate_wp_body_open()
    {
        do_action('wp_body_open');
    }
}
add_action('wp_body_open', 'startertemplate_skip_link', 5);
function startertemplate_skip_link()
{
    echo '<a href="#content" class="skip-link screen-reader-text">' . esc_html__('Skip to the content', 'startertemplate') . '</a>';
}

add_filter('the_content_more_link', 'startertemplate_read_more_link');
function startertemplate_read_more_link()
{
    if (!is_admin()) {
        return ' <a href="' . esc_url(get_permalink()) . '" class="more-link">' . sprintf(__('...%s', 'startertemplate'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}

add_filter('excerpt_more', 'startertemplate_excerpt_read_more_link');
function startertemplate_excerpt_read_more_link($more)
{
    if (!is_admin()) {
        global $post;
        return ' <a href="' . esc_url(get_permalink($post->ID)) . '" class="more-link">' . sprintf(__('...%s', 'startertemplate'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}

function startertemplate__breadcrumb()
{
    // Set variables for later use
    $home_link        = home_url('/');
    $home_text        = __( 'Home' );
    $link_before      = '<span typeof="v:Breadcrumb">';
    $link_after       = '</span>';
    $link_attr        = ' rel="v:url" property="v:title"';
    $link             = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
    $delimiter        = ' / ';              // Delimiter between crumbs
    $before           = '<span class="current">'; // Tag before the current crumb
    $after            = '</span>';                // Tag after the current crumb
    $page_addon       = '';                       // Adds the page number if the query is paged
    $breadcrumb_trail = '';
    $category_links   = '';

    /**
     * Set our own $wp_the_query variable. Do not use the global variable version due to
     * reliability
     */
    $wp_the_query   = $GLOBALS['wp_the_query'];
    $queried_object = $wp_the_query->get_queried_object();

    // Handle single post requests which includes single pages, posts and attatchments
    if ( is_singular() )
    {
        /**
         * Set our own $post variable. Do not use the global variable version due to
         * reliability. We will set $post_object variable to $GLOBALS['wp_the_query']
         */
        $post_object = sanitize_post( $queried_object );

        // Set variables
        $title          = apply_filters( 'the_title', $post_object->post_title );
        $parent         = $post_object->post_parent;
        $post_type      = $post_object->post_type;
        $post_id        = $post_object->ID;
        $post_link      = $before . $title . $after;
        $parent_string  = '';
        $post_type_link = '';

        if ( 'post' === $post_type )
        {
            // Get the post categories
            $categories = get_the_category( $post_id );
            if ( $categories ) {
                // Lets grab the first category
                $category  = $categories[0];

                $category_links = get_category_parents( $category, true, $delimiter );
                $category_links = str_replace( '<a',   $link_before . '<a' . $link_attr, $category_links );
                $category_links = str_replace( '</a>', '</a>' . $link_after,             $category_links );
            }
        }

        if ( !in_array( $post_type, ['post', 'page', 'attachment'] ) )
        {
            $post_type_object = get_post_type_object( $post_type );
            $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );

            $post_type_link   = sprintf( $link, $archive_link, $post_type_object->labels->singular_name );
        }

        // Get post parents if $parent !== 0
        if ( 0 !== $parent )
        {
            $parent_links = [];
            while ( $parent ) {
                $post_parent = get_post( $parent );

                $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), get_the_title( $post_parent->ID ) );

                $parent = $post_parent->post_parent;
            }

            $parent_links = array_reverse( $parent_links );

            $parent_string = implode( $delimiter, $parent_links );
        }

        // Lets build the breadcrumb trail
        if ( $parent_string ) {
            $breadcrumb_trail = $parent_string . $delimiter . $post_link;
        } else {
            $breadcrumb_trail = $post_link;
        }

        if ( $post_type_link )
            $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;

        if ( $category_links )
            $breadcrumb_trail = $category_links . $breadcrumb_trail;
    }

    // Handle archives which includes category-, tag-, taxonomy-, date-, custom post type archives and author archives
    if( is_archive() )
    {
        if (    is_category()
            || is_tag()
            || is_tax()
        ) {
            // Set the variables for this section
            $term_object        = get_term( $queried_object );
            $taxonomy           = $term_object->taxonomy;
            $term_id            = $term_object->term_id;
            $term_name          = $term_object->name;
            $term_parent        = $term_object->parent;
            $taxonomy_object    = get_taxonomy( $taxonomy );
            $current_term_link  = $before . $taxonomy_object->labels->singular_name . ': ' . $term_name . $after;
            $parent_term_string = '';

            if ( 0 !== $term_parent )
            {
                // Get all the current term ancestors
                $parent_term_links = [];
                while ( $term_parent ) {
                    $term = get_term( $term_parent, $taxonomy );

                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                    $term_parent = $term->parent;
                }

                $parent_term_links  = array_reverse( $parent_term_links );
                $parent_term_string = implode( $delimiter, $parent_term_links );
            }

            if ( $parent_term_string ) {
                $breadcrumb_trail = $parent_term_string . $delimiter . $current_term_link;
            } else {
                $breadcrumb_trail = $current_term_link;
            }

        } elseif ( is_author() ) {

            $breadcrumb_trail = __( 'Author archive for ') .  $before . $queried_object->data->display_name . $after;

        } elseif ( is_date() ) {
            // Set default variables
            $year     = $wp_the_query->query_vars['year'];
            $monthnum = $wp_the_query->query_vars['monthnum'];
            $day      = $wp_the_query->query_vars['day'];

            // Get the month name if $monthnum has a value
            if ( $monthnum ) {
                $date_time  = DateTime::createFromFormat( '!m', $monthnum );
                $month_name = $date_time->format( 'F' );
            }

            if ( is_year() ) {

                $breadcrumb_trail = $before . $year . $after;

            } elseif( is_month() ) {

                $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ), $year );

                $breadcrumb_trail = $year_link . $delimiter . $before . $month_name . $after;

            } elseif( is_day() ) {

                $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ),             $year       );
                $month_link       = sprintf( $link, esc_url( get_month_link( $year, $monthnum ) ), $month_name );

                $breadcrumb_trail = $year_link . $delimiter . $month_link . $delimiter . $before . $day . $after;
            }

        } elseif ( is_post_type_archive() ) {

            $post_type        = $wp_the_query->query_vars['post_type'];
            $post_type_object = get_post_type_object( $post_type );

            // $breadcrumb_trail = $before . $post_type_object->labels->singular_name . $after;
            $breadcrumb_trail = $before . $post_type_object->label . $after;

        }
    }

    // Handle the search page
    if ( is_search() ) {
        $breadcrumb_trail = __( 'Search query for: ' ) . $before . get_search_query() . $after;
    }

    // Handle 404's
    if ( is_404() ) {
        $breadcrumb_trail = $before . __( 'Error 404' ) . $after;
    }

    // Handle paged pages
    if ( is_paged() ) {
        $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
        $page_addon   = $before . sprintf( __( ' ( Page %s )' ), number_format_i18n( $current_page ) ) . $after;
    }

    $breadcrumb_output_link  = '';
    $breadcrumb_output_link .= '<div class="breadcrumb">';
    if (    is_home()
        || is_front_page()
    ) {
        // Do not show breadcrumbs on page one of home and frontpage
        if ( is_paged() ) {
            $breadcrumb_output_link .= '<a href="' . $home_link . '">' . $home_text . '</a>';
            $breadcrumb_output_link .= $page_addon;
        }
    } else {
        $breadcrumb_output_link .= '<a href="' . $home_link . '" rel="v:url" property="v:title">' . $home_text . '</a>';
        $breadcrumb_output_link .= $delimiter;
        $breadcrumb_output_link .= $breadcrumb_trail;
        $breadcrumb_output_link .= $page_addon;
    }
    $breadcrumb_output_link .= '</div><!-- .breadcrumbs -->';

    return $breadcrumb_output_link;
}

add_filter('big_image_size_threshold', '__return_false');
add_filter('intermediate_image_sizes_advanced', 'startertemplate_image_insert_override');
function startertemplate_image_insert_override($sizes)
{
    unset($sizes['medium_large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}

add_action('widgets_init', 'startertemplate_widgets_init');
function startertemplate_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar Widget Area', 'startertemplate'),
        'id' => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => esc_html__('Menu Widget Area', 'startertemplate'),
        'id' => 'menu-widget-area',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ));

    register_sidebar(array(
        'name' => esc_html__('Footer 1 Widget Area', 'startertemplate'),
        'id' => 'footer-1-widget-area',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ));

    register_sidebar(array(
        'name' => esc_html__('Footer 2 Widget Area', 'startertemplate'),
        'id' => 'footer-2-widget-area',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ));

    register_sidebar(array(
        'name' => esc_html__('Copyright Footer Widget Area', 'startertemplate'),
        'id' => 'copyright-footer-widget-area',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ));
}

add_action('wp_head', 'startertemplate_pingback_header');
function startertemplate_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s" />' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}

add_action('comment_form_before', 'startertemplate_enqueue_comment_reply_script');
function startertemplate_enqueue_comment_reply_script()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

function startertemplate_custom_pings($comment)
{
    ?>
    <li <?php comment_class(); ?>
            id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url(comment_author_link()); ?></li>
    <?php
}

add_filter('get_comments_number', 'startertemplate_comment_count', 0);
function startertemplate_comment_count($count)
{
    if (!is_admin()) {
        global $id;
        $get_comments = get_comments('status=approve&post_id=' . $id);
        $comments_by_type = separate_comments($get_comments);
        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop', 99 );
add_filter( 'the_content', 'shortcode_unautop', 100 );

function startertemplate_core_get_shortcode_module_template_part( $template, $slug = '', $params = array() )
{
    //HTML Content from template
    $html          = '';
    // $template_path = EDGE_CORE_SHORTCODES_PATH . '/' . $shortcode;

    // $temp = $template_path . '/' . $template;
    $temp = $template;

    if ( is_array( $params ) && count( $params ) ) {
        extract( $params );
    }

    $template = '';

    if ( ! empty( $temp ) ) {
        if ( ! empty( $slug ) ) {
            $template = "{$temp}-{$slug}.php";

            if ( ! file_exists( $template ) ) {
                $template = $temp . '.php';
            }
        } else {
            $template = $temp . '.php';
        }
    }

    if ( $template ) {
        ob_start();
        include( $template );
        $html = ob_get_clean();
    }

    return $html;
}

add_shortcode('caisection', 'create_section');
function create_section($atts, $content = null)
{
    $default_atts = array(
        'class' => '',
        'id' => '',
        'title' => '',
        'title_font_size' => '',
        'title_color' => '',
        'title_font_weight' => '',
        'title_margin' => '',
        'title_align' => '',
        'subtitle' => '',
        'subtitle_font_size' => '',
        'subtitle_color' => '',
        'subtitle_font_weight' => '',
        'subtitle_margin' => '',
        'subtitle_align' => '',
        'background_color' => '#ffffff',
        'background_image' => '',
        'padding' => '',
    );

    $params = shortcode_atts( $default_atts, $atts );
    $params['content'] = $content;

    return startertemplate_core_get_shortcode_module_template_part('shortcodes/templates/bc_section', '', $params );
}

add_shortcode('caicontainer', 'create_container');
function create_container($atts, $content = null) {
    $default_atts = array(
        'class' => '',
        'id' => '',
        'padding' => '',
        'margin' => '',
    );

    $params = shortcode_atts( $default_atts, $atts );
    $params['content'] = $content;

    return startertemplate_core_get_shortcode_module_template_part('shortcodes/templates/bc_container', '', $params );
}

add_shortcode('cairow', 'create_rows');
function create_rows($atts, $content = null) {
    $default_atts = array(
        'class' => 'row',
        'id' => '',
        'padding' => '',
        'margin' => '',
    );

    $params = shortcode_atts( $default_atts, $atts );
    $params['content'] = $content;

    return startertemplate_core_get_shortcode_module_template_part('shortcodes/templates/bc_rows', '', $params );
}

add_shortcode('caicolumn', 'create_cols');
function create_cols($atts, $content = null) {
    $default_atts = array(
        'class' => 'col-md-auto',
        'id' => '',
        'title' => '',
        'padding' => '',
        'margin' => '',
        'color' => '',
        'background_image' => '',
        'background_color' => '',
        'title_font_size' => '',
        'title_color' => '',
        'title_font_weight' => '',
        'title_margin' => '',
        'title_align' => '',
        'subtitle' => '',
        'subtitle_font_size' => '',
        'subtitle_color' => '',
        'subtitle_font_weight' => '',
        'subtitle_margin' => '',
        'subtitle_align' => '',
    );

    $params = shortcode_atts( $default_atts, $atts );
    $params['content'] = $content;

    return startertemplate_core_get_shortcode_module_template_part('shortcodes/templates/bc_columns', '', $params );
}
