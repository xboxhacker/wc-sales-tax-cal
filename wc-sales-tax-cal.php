<?php
/*
Plugin Name: WC Sales Tax Cal
Version: 1.7
Description: Calculate sales tax figures for WooCommerce. | Plugin Site: https://github.com/xboxhacker/
Author: William Hare & Grok3.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>WooCommerce is required for WC Sales Tax Cal to function. Please install and activate WooCommerce.</p></div>';
    });
    return;
}

// Register admin sub-menu under WooCommerce
add_action( 'admin_menu', 'wc_sales_tax_cal_add_menu' );

function wc_sales_tax_cal_add_menu() {
    // Check user capability
    if ( ! current_user_can( 'manage_options' ) ) {
        error_log( 'WC Sales Tax Cal: User lacks manage_options capability.' );
        return;
    }

    $hook = add_submenu_page(
        'woocommerce',
        'WC Sales Tax Cal',
        'Sales Tax Cal',
        'manage_options',
        'wc-sales-tax-calculator',
        'wc_sales_tax_cal_page'
    );

    // Log if menu was added
    if ( $hook ) {
        error_log( 'WC Sales Tax Cal: Admin sub-menu added successfully.' );
    } else {
        error_log( 'WC Sales Tax Cal: Failed to add admin sub-menu.' );
    }
}

// Admin page callback
function wc_sales_tax_cal_page() {
    // Handle form submission
    if ( isset( $_POST['wc_sales_tax_cal_submit'] ) ) {
        $year = intval( $_POST['year'] );
        $month = intval( $_POST['month'] );
        $tax_rate = floatval( $_POST['tax_rate'] );
        $surtax_rate = floatval( $_POST['surtax_rate'] );
        $save_rates = isset( $_POST['save_rates'] ) ? intval( $_POST['save_rates'] ) : 0;
        $notification_day = intval( $_POST['notification_day'] );
        $notes = sanitize_textarea_field( $_POST['notes'] );

        // Validate inputs
        if ( $year < 1900 || $year > 2100 || $month < 1 || $month > 12 || $tax_rate <= 0 || $surtax_rate < 0 || $notification_day < 1 || $notification_day > 28 ) {
            echo '<div class="error"><p>Invalid input. Please check the values.</p></div>';
        } else {
            // Save rates and notification day as defaults if checked
            if ( $save_rates ) {
                update_option( 'wc_sales_tax_cal_tax_rate', $tax_rate );
                update_option( 'wc_sales_tax_cal_surtax_rate', $surtax_rate );
                update_option( 'wc_sales_tax_cal_notification_day', $notification_day );
                update_option( 'wc_sales_tax_cal_notes', $notes );
            }

            // Calculate date range
            $start_date = date( 'Y-m-d', mktime(0, 0, 0, $month, 1, $year) );
            $end_date = date( 'Y-m-t', mktime(0, 0, 0, $month, 1, $year) );

            // Query WooCommerce orders
            $args = array(
                'date_created' => $start_date . '...' . $end_date,
                'limit' => -1,
                'status' => array( 'wc-completed', 'wc-processing' ),
            );
            $orders = wc_get_orders( $args );

            if ( empty( $orders ) ) {
                echo '<div class="notice notice-warning"><p>No orders found for the selected month.</p></div>';
            } else {
                $gross_sales = 0;
                $tax_due = 0;

                // Sum up gross sales (excluding tax) and tax due
                foreach ( $orders as $order ) {
                    $gross_sales += $order->get_total() - $order->get_total_tax();
                    $tax_due += $order->get_total_tax();
                }

                // Perform calculations
                $combined_rate = $tax_rate + $surtax_rate;
                $taxable_amount = $combined_rate > 0 ? $tax_due / ($combined_rate / 100) : 0;
                $exempt_sales = $gross_sales - $taxable_amount;
                $surtax_due = $taxable_amount * ($surtax_rate / 100);

                // Display results
                echo '<h2>Results for ' . date( 'F Y', mktime(0, 0, 0, $month, 1, $year) ) . '</h2>';
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<tr><td>GROSS SALES</td><td>' . wc_price( $gross_sales ) . '</td></tr>';
                echo '<tr><td>TAX DUE</td><td>' . wc_price( $tax_due ) . '</td></tr>';
                echo '<tr><td>TAXABLE AMOUNT</td><td>' . wc_price( $taxable_amount ) . '</td></tr>';
                echo '<tr><td>EXEMPT SALES</td><td>' . wc_price( $exempt_sales ) . '</td></tr>';
                echo '<tr><td>SURTAX DUE</td><td>' . wc_price( $surtax_due ) . '</td></tr>';
                echo '</table>';
            }
        }
    }

    // Load saved values
    $years = wc_sales_tax_cal_get_order_years();
    $months = range(1, 12);
    $selected_year = isset( $_POST['year'] ) ? intval( $_POST['year'] ) : intval(date('Y'));
    $selected_month = isset( $_POST['month'] ) ? intval( $_POST['month'] ) : '';
    $tax_rate = get_option( 'wc_sales_tax_cal_tax_rate', '' );
    $surtax_rate = get_option( 'wc_sales_tax_cal_surtax_rate', '' );
    $notification_day = get_option( 'wc_sales_tax_cal_notification_day', 1 );
    $notes = get_option( 'wc_sales_tax_cal_notes', '' );

    // Display form
    ?>
    <div class="wrap">
        <h1>WC Sales Tax Calculator</h1>
        <form method="post">
            <p>
                <label for="year">Year:</label>
                <select name="year" id="year">
                    <?php foreach ( $years as $year ) : ?>
                        <option value="<?php echo $year; ?>" <?php selected( $selected_year, $year ); ?>><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="month">Month:</label>
                <select name="month" id="month">
                    <?php for ( $m = 1; $m <= 12; $m++ ) : ?>
                        <option value="<?php echo $m; ?>" <?php selected( $selected_month, $m ); ?>><?php echo date( 'F', mktime(0, 0, 0, $m, 1) ); ?></option>
                    <?php endfor; ?>
                </select>
            </p>
            <p>
                <label for="tax_rate">Tax Rate (%):</label>
                <input type="number" step="0.01" name="tax_rate" id="tax_rate" value="<?php echo esc_attr( $tax_rate ); ?>" required />
            </p>
            <p>
                <label for="surtax_rate">Surtax Rate (%):</label>
                <input type="number" step="0.01" name="surtax_rate" id="surtax_rate" value="<?php echo esc_attr( $surtax_rate ); ?>" min="0" />
            </p>
            <p>
                <label for="notification_day">Notification Start Day (1-28):</label>
                <select name="notification_day" id="notification_day">
                    <?php for ( $d = 1; $d <= 28; $d++ ) : ?>
                        <option value="<?php echo $d; ?>" <?php selected( $notification_day, $d ); ?>><?php echo $d; ?></option>
                    <?php endfor; ?>
                </select>
            </p>
            <p>
                <label for="notes">Notes:</label><br>
                <textarea name="notes" id="notes" rows="5" cols="50"><?php echo esc_textarea( $notes ); ?></textarea>
            </p>
            <p>
                <label><input type="checkbox" name="save_rates" value="1" /> Save these rates, notification day, and notes as default</label>
            </p>
            <p>
                <input type="submit" name="wc_sales_tax_cal_submit" class="button button-primary" value="Calculate" />
            </p>
        </form>
    </div>
    <?php
}

// Function to get available order years
function wc_sales_tax_cal_get_order_years() {
    global $wpdb;
    $min_year = $wpdb->get_var( "SELECT MIN(YEAR(post_date)) FROM $wpdb->posts WHERE post_type = 'shop_order' AND post_status IN ('wc-completed', 'wc-processing')" );
    $max_year = $wpdb->get_var( "SELECT MAX(YEAR(post_date)) FROM $wpdb->posts WHERE post_type = 'shop_order' AND post_status IN ('wc-completed', 'wc-processing')" );
    $current_year = intval(date('Y'));
    if ( $min_year && $max_year ) {
        $years = range( $min_year, $max_year );
        if ( ! in_array( $current_year, $years ) ) {
            $years[] = $current_year;
        }
        sort( $years );
        return $years;
    } else {
        return array( $current_year );
    }
}

// Add admin notices for notification
add_action( 'admin_notices', 'wc_sales_tax_cal_show_notification' );

function wc_sales_tax_cal_show_notification() {
    $current_day = date( 'j' );
    $current_month = date( 'm' );
    $current_year = date( 'Y' );
    $notification_day = get_option( 'wc_sales_tax_cal_notification_day', 1 );
    $dismissed_month = get_option( 'wc_sales_tax_cal_dismissed_month', 0 );
    $dismissed_year = get_option( 'wc_sales_tax_cal_dismissed_year', 0 );

    if ( $current_day >= $notification_day && ( $current_year > $dismissed_year || ( $current_year == $dismissed_year && $current_month > $dismissed_month ) ) ) {
        $plugin_url = admin_url( 'admin.php?page=wc-sales-tax-calculator' );
        echo '<div class="notice notice-info is-dismissible" id="wc-sales-tax-cal-notice">';
        echo '<h3>It\'s time to file your sales tax.</h3>';
        echo '<p><a href="' . esc_url( $plugin_url ) . '">Calculate now</a>.</p>';
        echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        echo '</div>';
    }
}

// Enqueue script for dismissal
add_action( 'admin_enqueue_scripts', 'wc_sales_tax_cal_enqueue_scripts' );

function wc_sales_tax_cal_enqueue_scripts() {
    wp_enqueue_script( 'wc-sales-tax-cal-script', plugin_dir_url( __FILE__ ) . 'assets/script.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'wc-sales-tax-cal-script', 'wcSalesTaxCal', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'wc_sales_tax_cal_dismiss' ),
    ) );
}

// AJAX handler for dismissal
add_action( 'wp_ajax_wc_sales_tax_cal_dismiss', 'wc_sales_tax_cal_dismiss_notification' );

function wc_sales_tax_cal_dismiss_notification() {
    // Verify nonce without dying
    if ( ! check_ajax_referer( 'wc_sales_tax_cal_dismiss', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
    }

    // Update dismissal options
    $current_month = date( 'm' );
    $current_year = date( 'Y' );
    update_option( 'wc_sales_tax_cal_dismissed_month', $current_month );
    update_option( 'wc_sales_tax_cal_dismissed_year', $current_year );

    // Clear output buffer to prevent stray output
    ob_clean();

    // Send success response
    wp_send_json_success();
}
