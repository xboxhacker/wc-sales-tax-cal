# WC Sales Tax Cal

**Contributors**: William Hare, Grok3.0  
**Tags**: woocommerce, sales tax, calculator, tax reporting  
**Requires at least**: WordPress 5.0  
**Tested up to**: WordPress 6.6  
**Requires PHP**: 7.2  
**Stable tag**: 1.6  
**License**: GPLv2 or later  
**License URI**: http://www.gnu.org/licenses/gpl-2.0.html  
**Plugin Site**: https://github.com/xboxhacker/

A WordPress plugin for WooCommerce that calculates sales tax figures (Gross Sales, Tax Due, Taxable Amount, Exempt Sales, and Surtax Due) for a selected month, with a configurable notification system and notes feature.

## Description

**WC Sales Tax Cal** is a WooCommerce-integrated plugin designed to help store owners calculate and report sales tax figures for a specific month. It retrieves gross sales and tax data from WooCommerce orders, allowing users to input tax and surtax rates to compute Taxable Amount, Exempt Sales, and Surtax Due. The plugin includes a user-friendly admin interface, a customizable notification system to remind users to file taxes, and a notes section for record-keeping.

### Key Features
- **Tax Calculations**:
  - **Gross Sales**: Total sales from WooCommerce orders.
  - **Tax Due**: Total sales tax collected.
  - **Taxable Amount**: Calculated as Tax Due ÷ (tax rate / 100).
  - **Exempt Sales**: Calculated as Gross Sales - Taxable Amount - Tax Due.
  - **Surtax Due**: Calculated as Taxable Amount × (surtax rate / 100), with support for 0% surtax.
- **Admin Interface**:
  - Accessible via a sub-menu under WooCommerce.
  - Dropdowns for selecting year (defaults to current year) and month.
  - Input fields for tax rate, surtax rate (0% allowed), and notification start day (1–28).
  - Textarea for saving notes, displayed only in the input field.
  - Option to save rates, notification day, and notes as defaults.
- **Notification System**:
  - Displays a reminder starting on a user-selected day each month (1–28).
  - Includes a link to the plugin page and a dismiss button (hides until next month).
  - Notification text styled as an H3 heading for emphasis.
- **Robust and Secure**:
  - Validates inputs to prevent errors.
  - Uses sanitized inputs and secure AJAX for notification dismissal.
  - Logs menu registration for debugging.

## Installation

1. **Download the Plugin**:
   - Download the plugin files from [GitHub](https://github.com/xboxhacker/) or the WordPress plugin repository.

2. **Upload to WordPress**:
   - Navigate to **Plugins > Add New** in your WordPress admin dashboard.
   - Click **Upload Plugin** and select the `wc-sales-tax-cal.zip` file, or:
   - Unzip the plugin and upload the `wc-sales-tax-cal` folder to `wp-content/plugins/` via FTP.

3. **Activate the Plugin**:
   - Go to **Plugins > Installed Plugins**.
   - Locate "WC Sales Tax Cal" and click **Activate**.

4. **Verify Files**:
   - Ensure the `assets` folder contains `script.js` at `wp-content/plugins/wc-sales-tax-calრ: **Confirm WooCommerce is installed and active**:
   - The plugin requires WooCommerce to function. If WooCommerce is not active, an admin notice will prompt you to install and activate it.

## Usage

1. **Access the Plugin**:
   - Go to **WooCommerce > Sales Tax Cal** in the WordPress admin sidebar.

2. **Calculate Tax Figures**:
   - **Select Year and Month**: Choose a year (defaults to current year) and month from the dropdowns.
   - **Enter Rates**: Input the tax rate (%) and surtax rate (%) (0% allowed).
   - **Set Notification Day**: Select a day (1–28) for the monthly tax filing reminder.
   - **Add Notes**: Enter notes in the textarea for record-keeping.
   - **Save Defaults**: Check "Save these rates, notification day, and notes as default" to retain settings.
   - Click **Calculate** to display results in a table.

3. **Manage Notifications**:
   - A notification appears on the selected day each month with a link to the plugin page.
   - Click the dismiss button to hide it until the next month.

## Frequently Asked Questions

### What are the system requirements?
- WordPress 5.0 or higher.
- WooCommerce 3.0 or higher.
- PHP 7.2 or higher.

### Why do I see "WooCommerce is required" even though WooCommerce is installed?
- Ensure WooCommerce is active in **Plugins > Installed Plugins**.
- Check for plugin/theme conflicts by deactivating other plugins or switching to a default theme (e.g., Twenty Twenty-One).
- Enable debugging in `wp-config.php` to identify errors:
  ```php
  define('WP_DEBUG', true);
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', false);
  ```

### Can I use a 0% surtax rate?
Yes, the surtax rate field accepts 0%, and the Surtax Due will be calculated as $0.

### How do I troubleshoot issues?
- Verify file permissions: directories (755), files (644).
- Check `wp-content/debug.log` for errors.
- Deactivate and reactivate the plugin to refresh hooks.
- Contact support via the [plugin site](https://github.com/xboxhacker/).

## Changelog

### 1.6
- Removed display of saved notes at the bottom of the GUI; notes remain in the textarea.

### 1.5
- Updated year dropdown to default to the current year on page load while retaining all order years.

### 1.4
- Added notification start day selector (1–28).
- Changed notification text to H3.
- Allowed 0% surtax rate.
- Updated author to "William Hare & Grok3.0".
- Added plugin site link: https://github.com/xboxhacker/.
- Added savable notes section.
- Fixed Surtax Due calculation: Taxable Amount × (surtax rate / 100).

### 1.3
- Fixed WooCommerce detection using active plugins check.
- Fixed AJAX JSON error for notification dismissal.

### 1.2
- Moved to WooCommerce sub-menu.
- Added monthly notification with dismiss button and link to plugin page.

### 1.1
- Added error logging for menu registration.
- Improved WooCommerce dependency check.

### 1.0
- Initial release with tax calculation and admin interface.

## Upgrade Notice

### 1.6
Update to remove saved notes display from the GUI bottom, keeping notes in the textarea only.

## License

This plugin is licensed under the GPLv2 or later. See [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html) for details.