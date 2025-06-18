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
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'comments'),
        'menu_icon' => 'dashicons-book-alt',
        'rewrite' => array('slug' => 'manga'),
        'show_in_rest' => true
    ));
    
    // Register Chapter post type with corrected arguments
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
        'public'              => false,  // Tidak dapat diakses publik secara langsung
        'publicly_queryable'  => false,  // Tidak bisa di-query langsung dari URL
        'show_ui'             => true,   // Tetap tampil di menu admin
        'show_in_menu'        => true,   // Pastikan menu tetap ada
        'show_in_nav_menus'   => false,  // Jangan tampilkan di pilihan menu navigasi
        'exclude_from_search' => true,   // Jangan ikutkan dalam hasil pencarian situs
        'has_archive'         => false,  // Tidak punya halaman arsip sendiri
        'rewrite'             => false,  // PENTING: Matikan pembuatan aturan URL default
        'supports'            => array('title', 'editor', 'custom-fields', 'comments'),
        'menu_icon'           => 'dashicons-media-document',
        'show_in_rest'        => true,
    ));
}
add_action('init', 'create_manga_post_type');

// Custom taxonomies
function create_manga_taxonomies() {
    // Genre taxonomy
    register_taxonomy('manga_genre', 'manga', array(
        'labels' => array(
            'name' => 'Genres',
            'singular_name' => 'Genre'
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
            'singular_name' => 'Status'
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true
    ));
}
add_action('init', 'create_manga_taxonomies');

// Custom rewrite rules untuk manga dan chapter
function manga_rewrite_rules() {
    add_rewrite_rule(
        '^manga/([^/]+)/chapter/([^/]+)/page/([0-9]+)/?$',
        'index.php?post_type=manga&name=$matches[1]&chapter=$matches[2]&page_num=$matches[3]',
        'top'
    );
    
    add_rewrite_rule(
        '^manga/([^/]+)/chapter/([^/]+)/?$',
        'index.php?post_type=manga&name=$matches[1]&chapter=$matches[2]',
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


// Filter the permalink untuk memastikan URL Chapter selalu benar
function mangastream_custom_chapter_link($post_link, $post) {
    if ($post->post_type == 'chapter') {
        $manga_id = get_post_meta($post->ID, 'manga_id', true);
        if ($manga_id) {
            $manga = get_post($manga_id);
            if ($manga) {
                return home_url('/manga/' . $manga->post_name . '/chapter/' . $post->post_name . '/');
            }
        }
    }
    return $post_link;
}
add_filter('post_type_link', 'mangastream_custom_chapter_link', 10, 2);

// Flush rewrite rules on theme activation
function manga_flush_rewrite_rules() {
    create_manga_post_type();
    create_manga_taxonomies();
    manga_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'manga_flush_rewrite_rules');

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
if (is_admin()) {
    require_once get_template_directory() . '/admin/manga-meta-boxes.php';
}
?>