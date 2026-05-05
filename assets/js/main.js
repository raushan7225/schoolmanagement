$(document).ready(function() {
    // Gallery Filtering
    $('.filter-btn').on('click', function() {
        const filterValue = $(this).attr('data-filter');
        
        // Update Active Button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        if (filterValue === 'all') {
            $('.gallery-item').fadeIn(300);
        } else {
            $('.gallery-item').hide();
            $(`.gallery-item[data-category="${filterValue}"]`).fadeIn(300);
        }
    });

    // Simple Lightbox
    $('.gallery-thumb').on('click', function() {
        const imgSrc = $(this).find('img').attr('src');
        $('body').append(`
            <div class="lightbox-modal" id="lightbox">
                <span class="lightbox-close">&times;</span>
                <img src="${imgSrc}" class="lightbox-content animate__animated animate__zoomIn">
            </div>
        `);
        $('#lightbox').css('display', 'flex').fadeIn(300);
    });

    $(document).on('click', '.lightbox-close, #lightbox', function(e) {
        if (e.target !== this && !$(e.target).hasClass('lightbox-close')) return;
        $('#lightbox').fadeOut(300, function() {
            $(this).remove();
        });
    });

    // Hero Slider
    $(".hero-slider").owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 7000,
        nav: true,
        dots: true,
        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        animateOut: 'fadeOut',
        animateIn: 'fadeIn'
    });

    // Dropdown Hover for Desktop
    if ($(window).width() > 991) {
        $('.navbar .dropdown').hover(function() {
            $(this).addClass('hover-show');
        }, function() {
            $(this).removeClass('hover-show');
        });
    }

    // Ensure click works even with hover
    $('.navbar .dropdown-toggle').on('click', function(e) {
        if ($(window).width() > 991) {
            // On desktop, clicking should still toggle the Bootstrap 'show' class
            // even if hover-show is active.
            let $el = $(this).parent();
            if ($el.hasClass('hover-show')) {
                // If it's open via hover, and we click, let's keep it open or navigate?
                // Usually, the user wants the click to toggle the state.
            }
        }
    });
});
