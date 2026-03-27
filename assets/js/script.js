jQuery(document).ready(function($) {
    $('#wwc-floating-button').on('click', function(e) {
        e.preventDefault();

        const phone = wwc_ajax.phone;
        const message = encodeURIComponent(wwc_ajax.message);
        const whatsappUrl = `https://wa.me/${phone}?text=${message}`;

        // Get current page URL
        const currentPage = window.location.href;

        // Perform AJAX tracking
        $.ajax({
            url: wwc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wwc_track_lead',
                nonce: wwc_ajax.nonce,
                phone: phone,
                page: currentPage
            },
            complete: function() {
                // Redirect regardless of success or failure to ensure user gets to WhatsApp
                window.open(whatsappUrl, '_blank');
            }
        });
    });
});
