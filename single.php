<?php
/**
 * Template utama untuk semua postingan tunggal.
 */

get_header();

// Logika yang Diperbaiki:
// Tampilkan template manga jika tipe post adalah 'manga' ATAU jika ada variabel 'chapter' di URL.
if (get_post_type() === 'manga' || get_query_var('chapter')) {
    get_template_part('single-manga');
} else {
    // Logika standar untuk menampilkan post biasa
    if (have_posts()) :
        while (have_posts()) : the_post();
            get_template_part('template-parts/content', get_post_format());

            // Jika komentar diizinkan, tampilkan template komentar.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
        endwhile;
    endif;
}

get_footer();
?>