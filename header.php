<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="header-container">
            <a href="<?php echo home_url(); ?>" class="site-logo">
                <?php 
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    bloginfo('name');
                }
                ?>
            </a>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'fallback_cb' => false
                ));
                ?>
            </nav>
            
            <div class="search-container">
                <form role="search" method="get" action="<?php echo home_url('/'); ?>">
                    <input type="search" class="search-input" placeholder="Cari manga..." 
                           value="<?php echo get_search_query(); ?>" name="s" id="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div id="search-results" class="search-results"></div>
            </div>
            
            <button id="dark-mode-toggle" class="dark-mode-toggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>
    
    <main class="main-content">
