<?php get_header(); ?>

<div class="container">
    <div class="content-area">
        <header class="search-header">
            <h1 class="search-title">
                Hasil Pencarian untuk: "<?php echo get_search_query(); ?>"
            </h1>
            <p class="search-results-count">
                <?php
                global $wp_query;
                $total = $wp_query->found_posts;
                echo "Ditemukan {$total} hasil";
                ?>
            </p>
        </header>
        
        <!-- Advanced Search Form -->
        <div class="advanced-search">
            <form class="search-form-advanced" method="get" action="<?php echo home_url('/'); ?>">
                <div class="search-fields">
                    <input type="text" name="s" value="<?php echo get_search_query(); ?>" 
                           placeholder="Cari manga, chapter, atau kata kunci..." class="search-input-advanced">
                    
                    <select name="post_type" class="search-select">
                        <option value="">Semua Tipe</option>
                        <option value="manga" <?php selected(get_query_var('post_type'), 'manga'); ?>>Manga</option>
                        <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>>Artikel</option>
                    </select>
                    
                    <select name="manga_genre" class="search-select">
                        <option value="">Semua Genre</option>
                        <?php
                        $genres = get_terms(array(
                            'taxonomy' => 'manga_genre',
                            'hide_empty' => true
                        ));
                        foreach ($genres as $genre):
                        ?>
                            <option value="<?php echo $genre->slug; ?>" 
                                    <?php selected(get_query_var('manga_genre'), $genre->slug); ?>>
                                <?php echo $genre->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" class="search-btn-advanced">Cari</button>
                </div>
            </form>
        </div>
        
        <?php if (have_posts()): ?>
            <div class="search-results">
                <?php while (have_posts()): the_post(); ?>
                    <?php if (get_post_type() == 'manga'): ?>
                        <?php get_template_part('template-parts/manga-card'); ?>
                    <?php else: ?>
                        <article class="search-result-item">
                            <h2 class="result-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="result-meta">
                                <span class="result-type"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                                <span class="result-date"><?php echo get_the_date(); ?></span>
                            </div>
                            <div class="result-excerpt">
                                <?php
                                $excerpt = get_the_excerpt();
                                $search_term = get_search_query();
                                if ($search_term) {
                                    $excerpt = preg_replace('/(' . preg_quote($search_term, '/') . ')/i', '<mark>$1</mark>', $excerpt);
                                }
                                echo $excerpt;
                                ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="result-link">Baca Selengkapnya</a>
                        </article>
                    <?php endif; ?>
                <?php endwhile; ?>
                
                <?php
                // Pagination
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '&laquo; Sebelumnya',
                    'next_text' => 'Selanjutnya &raquo;'
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="no-search-results">
                <h2>Tidak ada hasil ditemukan</h2>
                <p>Maaf, pencarian untuk "<strong><?php echo get_search_query(); ?></strong>" tidak menghasilkan apapun.</p>
                
                <div class="search-suggestions">
                    <h3>Saran:</h3>
                    <ul>
                        <li>Periksa ejaan kata kunci</li>
                        <li>Gunakan kata kunci yang lebih umum</li>
                        <li>Coba kata kunci yang berbeda</li>
                        <li>Gunakan lebih sedikit kata kunci</li>
                    </ul>
                </div>
                
                <div class="popular-searches">
                    <h3>Pencarian Populer:</h3>
                    <div class="popular-tags">
                        <?php
                        $popular_terms = get_terms(array(
                            'taxonomy' => 'manga_genre',
                            'number' => 10,
                            'orderby' => 'count',
                            'order' => 'DESC'
                        ));
                        
                        foreach ($popular_terms as $term):
                        ?>
                            <a href="<?php echo get_term_link($term); ?>" class="popular-tag">
                                <?php echo $term->name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
