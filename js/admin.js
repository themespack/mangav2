jQuery(document).ready(function($) {
    // Manga admin enhancements
    
    // Chapter management
    $('#add-chapter-btn').click(function() {
        const chapterTemplate = `
            <div class="chapter-row">
                <input type="text" name="chapter_numbers[]" placeholder="Chapter Number" class="chapter-number">
                <input type="text" name="chapter_titles[]" placeholder="Chapter Title" class="chapter-title">
                <input type="url" name="chapter_urls[]" placeholder="Chapter URL" class="chapter-url">
                <button type="button" class="remove-chapter">Remove</button>
            </div>
        `;
        $('#chapters-container').append(chapterTemplate);
    });
    
    $(document).on('click', '.remove-chapter', function() {
        $(this).closest('.chapter-row').remove();
    });
    
    // Image upload for manga pages
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
