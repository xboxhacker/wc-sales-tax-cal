jQuery(document).ready(function($) {
    $('#wc-sales-tax-cal-notice .notice-dismiss').on('click', function() {
        $.post(wcSalesTaxCal.ajax_url, {
            action: 'wc_sales_tax_cal_dismiss',
            nonce: wcSalesTaxCal.nonce
        }, function(response) {
            if (response.success) {
                $('#wc-sales-tax-cal-notice').fadeOut();
            }
        });
    });
});