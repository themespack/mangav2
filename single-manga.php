<?php
/**
 * Template untuk menampilkan halaman manga tunggal atau pembaca chapter.
 * Versi ini berisi logika yang telah diperbaiki sepenuhnya.
 */

get_header();

$chapter_slug = get_query_var('chapter');

if ($chapter_slug) {
    // Ini adalah halaman chapter. Kita harus membangun ulang konteks dari URL.
    
    // 1. Dapatkan slug manga dari query var 'name' yang kita atur di rewrite rule.
    $manga_slug = get_query_var('name');
    $manga = get_page_by_path($manga_slug, OBJECT, 'manga');

    if ($manga) {
        // 2. Manga ditemukan. Sekarang validasi apakah chapter yang diminta benar-benar milik manga ini.
        $chapter_post_args = array(
            'post_type'      => 'chapter',
            'name'           => $chapter_slug,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => 'manga_id',
                    'value'   => $manga->ID, // Gunakan ID manga yang benar
                    'compare' => '=',
                ),
            ),
        );
        $chapter_post_query = new WP_Query($chapter_post_args);

        if ($chapter_post_query->have_posts()) {
            // 3. Sukses! Chapter valid dan milik manga yang benar. Muat halaman pembaca.
            get_template_part('template-parts/manga-reader');
        } else {
            // Chapter ada tapi bukan milik manga ini. Tampilkan 404.
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            get_template_part('404');
        }
        wp_reset_postdata();

    } else {
        // Slug manga di URL tidak valid. Tampilkan 404.
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        get_template_part('404');
    }

} else {
    // Ini adalah halaman info manga biasa (tidak ada slug chapter di URL).
    get_template_part('template-parts/manga-single');
}

get_footer();
?>