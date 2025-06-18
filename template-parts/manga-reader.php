<?php
/**
 * Template untuk menampilkan halaman pembaca chapter.
 * Versi final dengan struktur HTML dan class yang benar.
 */

global $post;

$manga_id = get_the_ID();
$manga_slug = get_post_field('post_name', $manga_id);
$chapter_slug = get_query_var('chapter');
$page_num = get_query_var('page_num') ? intval(get_query_var('page_num')) : 1;

if (!$chapter_slug) {
    get_template_part('404');
    return;
}

$chapter = get_chapter_by_slug($chapter_slug, $manga_id);

if (!$chapter || !validate_chapter_access($chapter->ID, $manga_id)) {
    get_template_part('404');
    return;
}

// Mengambil data chapter
$chapter_number = get_post_meta($chapter->ID, 'chapter_number', true);
$pages = get_chapter_pages($chapter->ID); // Menggunakan fungsi helper jika ada, atau get_post_meta
$total_pages = count($pages);
$navigation = get_chapter_navigation($chapter->ID);

// Update reading progress & view count
if (is_user_logged_in()) {
    update_manga_reading_progress($manga_id, $chapter_number);
}
increment_manga_views($manga_id); // Menggunakan fungsi increment yang lebih aman

?>

<div class="manga-reader" data-manga-id="<?php echo esc_attr($manga_id); ?>" data-chapter-id="<?php echo esc_attr($chapter->ID); ?>">
     
    <div class="reader-container">
        <?php mangastream_breadcrumb(); ?>
        
        <div class="reader-navigation">
            <div class="nav-left">
                <?php if ($navigation['prev']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['prev']->post_name); ?>" class="nav-btn prev-chapter">
                        <i class="fas fa-chevron-left"></i> Chapter Sebelumnya
                    </a>
                <?php endif; ?>
            </div>
            <div class="nav-center">
                <select class="chapter-selector" onchange="location = this.value;">
                    <?php foreach ($navigation['all'] as $ch): ?>
                        <?php $ch_number = get_post_meta($ch->ID, 'chapter_number', true); ?>
                        <option value="<?php echo get_chapter_url($manga_slug, $ch->post_name); ?>" <?php selected($ch->ID, $chapter->ID); ?>>
                            Chapter <?php echo esc_html($ch_number); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="page-info">Halaman <span class="current-page"><?php echo esc_html($page_num); ?></span> dari <?php echo esc_html($total_pages); ?></span>
            </div>
            <div class="nav-right">
                <?php if ($navigation['next']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['next']->post_name); ?>" class="nav-btn next-chapter">
                        Chapter Selanjutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="manga-pages">
            <?php if (!empty($pages)): ?>
                <?php foreach ($pages as $index => $page): ?>
                    <img src="<?php echo esc_url($page['url']); ?>" 
                         alt="<?php echo esc_attr($page['alt'] ?: 'Page ' . ($index + 1)); ?>" 
                         class="manga-page"
                         data-page-num="<?php echo $index + 1; ?>"
                         loading="lazy">
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-pages">
                    <p>Tidak ada gambar yang ditemukan untuk chapter ini. Mungkin sedang dalam proses upload.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="reader-navigation">
            <div class="nav-left">
                <?php if ($navigation['prev']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['prev']->post_name); ?>" class="nav-btn prev-chapter">
                        <i class="fas fa-chevron-left"></i> Chapter Sebelumnya
                    </a>
                <?php endif; ?>
            </div>
            <div class="nav-center">
                 <a href="<?php echo get_permalink($manga_id); ?>" class="nav-btn">
                    <i class="fas fa-list"></i> Daftar Chapter
                </a>
            </div>
            <div class="nav-right">
                <?php if ($navigation['next']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['next']->post_name); ?>" class="nav-btn next-chapter">
                        Chapter Selanjutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>