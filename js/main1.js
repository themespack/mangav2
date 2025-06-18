jQuery(document).ready(function($) {
    // Dark mode toggle
    $('#dark-mode-toggle').click(function() {
        $('body').toggleClass('dark-mode');
        localStorage.setItem('darkMode', $('body').hasClass('dark-mode'));
    });
    
    // Load dark mode preference
    if (localStorage.getItem('darkMode') === 'true') {
        $('body').addClass('dark-mode');
    }
    
    // Back to top functionality
$(window).scroll(function() {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').addClass('show');
    } else {
        $('.back-to-top').removeClass('show');
    }
});

$('.back-to-top').click(function() {
    $('html, body').animate({scrollTop: 0}, 600);
    return false;
});

// Random manga button
$('#random-manga-btn').click(function() {
    $.ajax({
        url: mangastream_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'random_manga',
            nonce: mangastream_ajax.nonce
        },
        success: function(response) {
            if (response.success) {
                const manga = response.data;
                $('.random-manga-card img').attr('src', manga.thumbnail);
                $('.random-manga-title').text(manga.title).attr('href', manga.url);
                $('.random-manga-excerpt').text(manga.excerpt);
                $('.read-now-btn').attr('href', manga.url);
            }
        }
    });
});

// Load more manga
$('#load-more-manga').click(function() {
    const button = $(this);
    const currentPage = parseInt(button.data('page') || 2);
    
    button.text('Memuat...').prop('disabled', true);
    
    $.ajax({
        url: mangastream_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'load_more_manga',
            page: currentPage,
            nonce: mangastream_ajax.nonce
        },
        success: function(response) {
            if (response.trim()) {
                $('#manga-results').append(response);
                button.data('page', currentPage + 1);
                button.text('Muat Lebih Banyak').prop('disabled', false);
            } else {
                button.text('Tidak ada lagi').prop('disabled', true);
            }
        },
        error: function() {
            button.text('Error - Coba Lagi').prop('disabled', false);
        }
    });
});

    // AJAX search
    let searchTimeout;
    $('#search-input').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        if (searchTerm.length < 3) {
            $('#search-results').hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: mangastream_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mangastream_search',
                    search_term: searchTerm,
                    nonce: mangastream_ajax.nonce
                },
                success: function(response) {
                    let resultsHtml = '';
                    if (response.length > 0) {
                        response.forEach(function(item) {
                            resultsHtml += `
                                <div class="search-result-item">
                                    <img src="${item.thumbnail}" alt="${item.title}">
                                    <a href="${item.url}">${item.title}</a>
                                </div>
                            `;
                        });
                    } else {
                        resultsHtml = '<div class="no-results">Tidak ada hasil ditemukan</div>';
                    }
                    $('#search-results').html(resultsHtml).show();
                }
            });
        }, 300);
    });
    
    // Hide search results when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('#search-results').hide();
        }
    });
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});
