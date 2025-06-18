<?php
global $post;

// Get current manga and chapter data
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

// Get chapter data
$chapter_number = get_post_meta($chapter->ID, 'chapter_number', true);
$pages = get_chapter_pages($chapter->ID);
$total_pages = count($pages);
$navigation = get_chapter_navigation($chapter->ID);

// Update reading progress
if (is_user_logged_in()) {
    update_manga_reading_progress($manga_id, $chapter_number);
}

// Update view count
$total_views = get_post_meta($manga_id, 'total_views', true) ?: 0;
update_post_meta($manga_id, 'total_views', $total_views + 1);
?>

<div class="manga-reader" 
     data-manga-id="<?php echo $manga_id; ?>" 
     data-chapter-id="<?php echo $chapter->ID; ?>" 
     data-chapter-number="<?php echo $chapter_number; ?>"
     data-manga-slug="<?php echo $manga_slug; ?>"
     data-chapter-slug="<?php echo $chapter_slug; ?>">
     
    <div class="progress-bar"></div>
    
    <div class="reader-container">
        <!-- Breadcrumb Navigation -->
        <?php mangastream_breadcrumb(); ?>
        
        <!-- Reader Navigation Top -->
        <div class="reader-navigation">
            <div class="nav-left">
                <?php if ($navigation['prev']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['prev']->post_name); ?>" 
                       class="nav-btn prev-chapter">
                        <i class="fas fa-chevron-left"></i> Chapter Sebelumnya
                    </a>
                <?php else: ?>
                    <button class="nav-btn" disabled>
                        <i class="fas fa-chevron-left"></i> Chapter Sebelumnya
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="nav-center">
                <select class="chapter-selector">
                    <?php foreach ($navigation['all'] as $ch): ?>
                        <?php $ch_number = get_post_meta($ch->ID, 'chapter_number', true); ?>
                        <option value="<?php echo get_chapter_url($manga_slug, $ch->post_name); ?>" 
                                <?php selected($ch->ID, $chapter->ID); ?>>
                            Chapter <?php echo $ch_number; ?>
                            <?php if ($ch->post_title && $ch->post_title !== 'Chapter ' . $ch_number): ?>
                                - <?php echo esc_html($ch->post_title); ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <span class="page-info">
                    Halaman <span class="current-page" data-page="<?php echo $page_num; ?>"><?php echo $page_num; ?></span> 
                    dari <span class="total-pages"><?php echo $total_pages; ?></span>
                </span>
                
                <button id="reading-mode-toggle" class="nav-btn">
                    <i class="fas fa-list"></i> Mode Halaman Tunggal
                </button>
                
                <button id="reading-settings-btn" class="nav-btn">
                    <i class="fas fa-cog"></i> Pengaturan
                </button>
            </div>
            
            <div class="nav-right">
                <?php if ($navigation['next']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['next']->post_name); ?>" 
                       class="nav-btn next-chapter">
                        Chapter Selanjutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <button class="nav-btn" disabled>
                        Chapter Selanjutnya <i class="fas fa-chevron-right"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Manga Pages -->
        <div class="manga-pages">
            <?php if ($pages): ?>
                <?php foreach ($pages as $index => $page): ?>
                    <img src="<?php echo esc_url($page['url']); ?>" 
                         alt="<?php echo esc_attr($page['alt']); ?>" 
                         class="manga-page"
                         data-page="<?php echo $index + 1; ?>"
                         data-width="<?php echo $page['width']; ?>"
                         data-height="<?php echo $page['height']; ?>"
                         loading="<?php echo $index < 3 ? 'eager' : 'lazy'; ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-pages">
                    <h3>Tidak ada halaman tersedia</h3>
                    <p>Chapter ini belum memiliki halaman yang dapat dibaca.</p>
                    <a href="<?php echo get_manga_url($manga_slug); ?>" class="btn btn-primary">
                        Kembali ke Info Manga
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Reader Navigation Bottom -->
        <div class="reader-navigation">
            <div class="nav-left">
                <?php if ($navigation['prev']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['prev']->post_name); ?>" 
                       class="nav-btn prev-chapter">
                        <i class="fas fa-chevron-left"></i> Chapter Sebelumnya
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="nav-center">
                <a href="<?php echo get_manga_url($manga_slug); ?>" class="nav-btn">
                    <i class="fas fa-info-circle"></i> Info Manga
                </a>
                
                <?php if (is_user_logged_in()): ?>
                    <button id="bookmark-chapter-btn" class="nav-btn <?php echo is_manga_bookmarked($manga_id) ? 'bookmarked' : ''; ?>">
                        <i class="<?php echo is_manga_bookmarked($manga_id) ? 'fas' : 'far'; ?> fa-bookmark"></i> 
                        Bookmark
                    </button>
                <?php endif; ?>
                
                <button class="nav-btn share-btn">
                    <i class="fas fa-share"></i> Bagikan
                </button>
            </div>
            
            <div class="nav-right">
                <?php if ($navigation['next']): ?>
                    <a href="<?php echo get_chapter_url($manga_slug, $navigation['next']->post_name); ?>" 
                       class="nav-btn next-chapter">
                        Chapter Selanjutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Chapter Info -->
        <div class="chapter-info">
            <h2 class="chapter-title">
                <a href="<?php echo get_manga_url($manga_slug); ?>">
                    <?php echo get_the_title($manga_id); ?>
                </a>
                - Chapter <?php echo $chapter_number; ?>
            </h2>
            
            <?php if ($chapter->post_title && $chapter->post_title !== 'Chapter ' . $chapter_number): ?>
                <h3 class="chapter-subtitle"><?php echo esc_html($chapter->post_title); ?></h3>
            <?php endif; ?>
            
            <div class="chapter-meta">
                <span class="chapter-date">
                    <i class="fas fa-calendar"></i> 
                    <?php echo get_the_date('', $chapter->ID); ?>
                </span>
                
                <span class="chapter-views">
                    <i class="fas fa-eye"></i> 
                    <?php echo format_view_count(get_post_meta($manga_id, 'total_views', true) ?: 0); ?> views
                </span>
                
                <span class="chapter-pages">
                    <i class="fas fa-images"></i> 
                    <?php echo $total_pages; ?> halaman
                </span>
            </div>
        </div>
        
        <!-- Comments Section (Optional) -->
        <?php if (comments_open($chapter->ID) || get_comments_number($chapter->ID)): ?>
            <div class="chapter-comments-section">
                <h3>Komentar Chapter</h3>
                <div id="chapter-comments-container">
                    <!-- Comments will be loaded via AJAX -->
                    <button id="load-comments-btn" class="btn btn-secondary">
                        <i class="fas fa-comments"></i> Muat Komentar
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo esc_js(get_the_title($manga_id) . ' Chapter ' . $chapter_number); ?>",
    "description": "Baca <?php echo esc_js(get_the_title($manga_id)); ?> Chapter <?php echo $chapter_number; ?> online gratis",
    "image": "<?php echo $pages ? esc_url($pages[0]['url']) : ''; ?>",
    "author": {
        "@type": "Organization",
        "name": "<?php bloginfo('name'); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php bloginfo('name'); ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo get_site_icon_url(); ?>"
        }
    },
    "datePublished": "<?php echo get_the_date('c', $chapter->ID); ?>",
    "dateModified": "<?php echo get_the_modified_date('c', $chapter->ID); ?>",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo get_chapter_url($manga_slug, $chapter_slug); ?>"
    }
}
</script>
