<?php
function mangastream_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('post-formats', array('aside', 'gallery', 'quote', 'image', 'video'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu'
    ));
    
    // Add image sizes
    add_image_size('manga-thumbnail', 200, 280, true);
    add_image_size('manga-large', 800, 1200, true);
}
add_action('after_setup_theme', 'mangastream_theme_setup');

// Enqueue scripts and styles
function mangastream_scripts() {
    wp_enqueue_style('mangastream-style', get_stylesheet_uri());
    wp_enqueue_style('mangastream-dark', get_template_directory_uri() . '/css/dark-mode.css');
    wp_enqueue_style('mangastream-responsive', get_template_directory_uri() . '/css/responsive.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    
    wp_enqueue_script('mangastream-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0', true);
    wp_enqueue_script('mangastream-reader', get_template_directory_uri() . '/js/reader.js', array('jquery'), '1.0', true);
    
    // Localize script for AJAX
    wp_localize_script('mangastream-main', 'mangastream_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mangastream_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'mangastream_scripts');

// Register sidebar
function mangastream_widgets_init() {
    register_sidebar(array(
        'name' => 'Main Sidebar',
        'id' => 'main-sidebar',
        'description' => 'Appears on the right side of the site',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}
add_action('widgets_init', 'mangastream_widgets_init');

// Custom post type for Manga
function create_manga_post_type() {
    register_post_type('manga', array(
        'labels' => array(
            'name' => 'Manga',
            'singular_name' => 'Manga',
            'add_new' => 'Add New Manga',
            'add_new_item' => 'Add New Manga',
            'edit_item' => 'Edit Manga',
            'new_item' => 'New Manga',
            'view_item' => 'View Manga',
            'search_items' => 'Search Manga',
            'not_found' => 'No manga found',
            'not_found_in_trash' => 'No manga found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-book-alt',
        'rewrite' => array('slug' => 'manga'),
        'show_in_rest' => true
    ));
    
    // Register Chapter post type
    register_post_type('chapter', array(
        'labels' => array(
            'name' => 'Chapters',
            'singular_name' => 'Chapter',
            'add_new' => 'Add New Chapter',
            'add_new_item' => 'Add New Chapter',
            'edit_item' => 'Edit Chapter',
            'new_item' => 'New Chapter',
            'view_item' => 'View Chapter',
            'search_items' => 'Search Chapters',
            'not_found' => 'No chapters found',
            'not_found_in_trash' => 'No chapters found in trash'
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-media-document',
        'rewrite' => array('slug' => 'chapter'),
        'show_in_rest' => true
    ));
}
add_action('init', 'create_manga_post_type');

// Custom taxonomies
function create_manga_taxonomies() {
    // Genre taxonomy
    register_taxonomy('manga_genre', 'manga', array(
        'labels' => array(
            'name' => 'Genres',
            'singular_name' => 'Genre',
            'search_items' => 'Search Genres',
            'all_items' => 'All Genres',
            'parent_item' => 'Parent Genre',
            'parent_item_colon' => 'Parent Genre:',
            'edit_item' => 'Edit Genre',
            'update_item' => 'Update Genre',
            'add_new_item' => 'Add New Genre',
            'new_item_name' => 'New Genre Name',
            'menu_name' => 'Genres'
        ),
        'hierarchical' => true,
        'public' => true,
        'rewrite' => array('slug' => 'genre'),
        'show_in_rest' => true
    ));
    
    // Status taxonomy
    register_taxonomy('manga_status', 'manga', array(
        'labels' => array(
            'name' => 'Status',
            'singular_name' => 'Status',
            'search_items' => 'Search Status',
            'all_items' => 'All Status',
            'edit_item' => 'Edit Status',
            'update_item' => 'Update Status',
            'add_new_item' => 'Add New Status',
            'new_item_name' => 'New Status Name',
            'menu_name' => 'Status'
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true
    ));
}
add_action('init', 'create_manga_taxonomies');

// Custom rewrite rules untuk manga dan chapter
function manga_rewrite_rules() {
    // Manga chapter reader dengan page number
    add_rewrite_rule(
        '^manga/([^/]+)/chapter/([^/]+)/page/([0-9]+)/?$',
        'index.php?post_type=manga&name=$matches[1]&chapter=$matches[2]&page_num=$matches[3]',
        'top'
    );
    
    // Manga chapter reader
    add_rewrite_rule(
        '^manga/([^/]+)/chapter/([^/]+)/?$',
        'index.php?post_type=manga&name=$matches[1]&chapter=$matches[2]',
        'top'
    );
    
    // Manga info page (default single manga)
    add_rewrite_rule(
        '^manga/([^/]+)/?$',
        'index.php?post_type=manga&name=$matches[1]',
        'top'
    );
}
add_action('init', 'manga_rewrite_rules');

// Add custom query vars
function manga_query_vars($vars) {
    $vars[] = 'chapter';
    $vars[] = 'page_num';
    return $vars;
}
add_filter('query_vars', 'manga_query_vars');

// Template redirect untuk handling custom URLs
function manga_template_redirect() {
    global $wp_query;
    
    if (get_query_var('chapter') && is_singular('manga')) {
        // Set proper template for chapter reading
        $wp_query->is_singular = true;
        $wp_query->is_single = true;
        
        // Load single-manga.php template
        include(get_template_directory() . '/single-manga.php');
        exit;
    }
}
add_action('template_redirect', 'manga_template_redirect');

// Flush rewrite rules on theme activation
function manga_flush_rewrite_rules() {
    create_manga_post_type();
    create_manga_taxonomies();
    manga_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'manga_flush_rewrite_rules');

// AJAX search functionality
function mangastream_ajax_search() {
    check_ajax_referer('mangastream_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    $args = array(
        'post_type' => 'manga',
        's' => $search_term,
        'posts_per_page' => 10,
        'post_status' => 'publish'
    );
    
    $query = new WP_Query($args);
    $results = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = array(
                'title' => get_the_title(),
                'url' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'manga-thumbnail') ?: ''
            );
        }
    }
    
    wp_reset_postdata();
    wp_send_json($results);
}
add_action('wp_ajax_mangastream_search', 'mangastream_ajax_search');
add_action('wp_ajax_nopriv_mangastream_search', 'mangastream_ajax_search');

// Comment callback function
function mangastream_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, 60); ?>
                <cite class="fn"><?php comment_author_link(); ?></cite>
                <span class="comment-date"><?php comment_date(); ?> at <?php comment_time(); ?></span>
            </div>
            
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
            
            <div class="comment-reply">
                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
            </div>
        </div>
    <?php
}

// Custom excerpt length
function mangastream_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'mangastream_excerpt_length');

// Custom excerpt more
function mangastream_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'mangastream_excerpt_more');

// Add body classes for better styling
function mangastream_body_classes($classes) {
    if (is_singular('manga')) {
        $classes[] = 'single-manga';
        
        if (get_query_var('chapter')) {
            $classes[] = 'manga-reader-page';
        } else {
            $classes[] = 'manga-info-page';
        }
    }
    
    if (is_post_type_archive('manga')) {
        $classes[] = 'manga-archive';
    }
    
    return $classes;
}
add_filter('body_class', 'mangastream_body_classes');

// Include additional files
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/manga-functions.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/view-counter.php';
require_once get_template_directory() . '/admin/manga-meta-boxes.php';
?>
