<?php
// Template helper functions

function get_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed
    return $reading_time . ' min read';
}

function get_manga_status_color($status_slug) {
    $colors = array(
        'ongoing' => '#28a745',
        'completed' => '#dc3545',
        'hiatus' => '#ffc107',
        'dropped' => '#6c757d'
    );
    
    return isset($colors[$status_slug]) ? $colors[$status_slug] : '#007cba';
}

function get_user_bookmarks($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return array();
    }
    
    return get_user_meta($user_id, 'manga_bookmarks', true) ?: array();
}

function get_manga_progress($manga_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return null;
    }
    
    $progress = get_user_meta($user_id, 'manga_progress_' . $manga_id, true);
    return $progress ?: array('last_chapter' => 0, 'last_read' => '');
}

function update_manga_progress($manga_id, $chapter_number, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return false;
    }
    
    $progress = array(
        'last_chapter' => $chapter_number,
        'last_read' => current_time('mysql')
    );
    
    return update_user_meta($user_id, 'manga_progress_' . $manga_id, $progress);
}

?>
