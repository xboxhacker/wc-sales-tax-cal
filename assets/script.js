jQuery(document).ready(function($) {
    console.log('WC Sales Tax Cal script loaded.');
    $(document).on('click', '#wc-sales-tax-cal-notice .notice-dismiss', function(e) {
        e.preventDefault();
        console.log('Dismiss button clicked.');
        var notice = $(this).closest('#wc-sales-tax-cal-notice');
        $.post(wcSalesTaxCal.ajax_url, {
            action: 'wc_sales_tax_cal_dismiss',
            nonce: wcSalesTaxCal.nonce
        }, function(response) {
            console.log('AJAX response:', response);
            if (response.success) {
                console.log('Dismiss successful.');
                notice.fadeOut(400, function() {
                    notice.remove();
                });
            } else {
                console.error('Dismiss failed:', response);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
        });
    });
});
