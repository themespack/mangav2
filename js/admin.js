// File: js/admin.js

jQuery(document).ready(function($) {
    // Manga admin enhancements
    
    // Chapter management
    $('#add-chapter-btn').click(function() {
        // Ambil HTML dari template yang sudah disiapkan di PHP
        const chapterTemplate = $('.chapter-template').html(); 
        
        // Tambahkan template baru ke dalam daftar chapter
        $('.chapters-list').append(chapterTemplate); 
        
        // Hapus pesan "Belum ada chapter" jika ada
        $('.no-chapters').remove(); 
    });
    
    $(document).on('click', '.remove-chapter', function() {
        $(this).closest('.chapter-row').remove();
    });
    
    // Image upload for manga pages (kode lainnya tetap sama)
    $('.upload-manga-pages').click(function(e) {
        e.preventDefault();
        
        const frame = wp.media({
            title: 'Select Manga Pages',
            multiple: true,
            library: {
                type: 'image'
            }
        });
        
        frame.on('select', function() {
            const attachments = frame.state().get('selection').toJSON();
            let imagesList = '';
            
            attachments.forEach(function(attachment) {
                imagesList += `
                    <div class="manga-page-item">
                        <img src="${attachment.url}" alt="Page" style="max-width: 100px;">
                        <input type="hidden" name="manga_pages[]" value="${attachment.id}">
                        <button type="button" class="remove-page">Remove</button>
                    </div>
                `;
            });
            
            $('#manga-pages-container').append(imagesList);
        });
        
        frame.open();
    });
    
    $(document).on('click', '.remove-page', function() {
        $(this).closest('.manga-page-item').remove();
    });
    
    // Sortable chapters
    if ($.fn.sortable) {
        $('#chapters-container').sortable({
            handle: '.chapter-handle',
            placeholder: 'chapter-placeholder'
        });
        
        $('#manga-pages-container').sortable({
            placeholder: 'page-placeholder'
        });
    }
    
    // Auto-save functionality
    let autoSaveTimer;
    $('.manga-form input, .manga-form textarea, .manga-form select').on('change input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Auto-save logic here
            console.log('Auto-saving...');
        }, 2000);
    });
});