<?php get_header(); ?>

<div class="container">
    <div class="content-area">
        <?php if (is_post_type_archive('manga')): ?>
            <header class="archive-header">
                <h1 class="archive-title">Semua Manga</h1>
                <p class="archive-description">Jelajahi koleksi lengkap manga kami</p>
            </header>
            
            <!-- Manga grid dan filter -->
            <?php get_template_part('template-parts/manga-list'); ?>
            
        <?php else: ?>
            <!-- Archive lainnya -->
            <!-- ... kode archive yang sudah ada ... -->
        <?php endif; ?>
    </div>
    
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
