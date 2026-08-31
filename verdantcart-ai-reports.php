<?php

/**
 * Plugin Name: VerdantCart Carbon Reports
 * Plugin URI: https://verdantcart.ai/
 * Description: Estimate WooCommerce order emissions and review carbon reports with dashboards, trends, exports, and product insights — all inside WordPress.
 * Version: 1.4.0
 * Author: VerdantCart
 * Support: support@verdantcart.ai
 * Author URI: https://verdantcart.ai/
 * Text Domain: verdantcart-ai-reports
 * Domain Path: /languages
 * Requires at least: 6.4
 * Tested up to: 7.1
 * Requires PHP: 8.0
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0
 * WC tested up to: 10.6
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;

/**
 * ------------------------------------------------------------
 * WooCommerce compatibility
 * ------------------------------------------------------------
 */
function vcarb_declare_wc_compatibility(): void
{
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
}
add_action('before_woocommerce_init', 'vcarb_declare_wc_compatibility');

/**
 * ------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------
 */
defined('VCARB_VERSION') || define('VCARB_VERSION', '1.4.0');
defined('VCARB_DB_VERSION') || define('VCARB_DB_VERSION', '1.2.1');
defined('VCARB_PLUGIN_FILE') || define('VCARB_PLUGIN_FILE', __FILE__);
defined('VCARB_PLUGIN_DIR') || define('VCARB_PLUGIN_DIR', plugin_dir_path(__FILE__));
defined('VCARB_PLUGIN_URL') || define('VCARB_PLUGIN_URL', plugin_dir_url(__FILE__));
defined('VCARB_TEXT_DOMAIN') || define('VCARB_TEXT_DOMAIN', 'verdantcart-ai-reports');

/**
 * ------------------------------------------------------------
 * Central table helpers
 * ------------------------------------------------------------
 */
if (!function_exists('vcarb_logs_table')) {
    function vcarb_logs_table(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'vcarb_logs';
    }
}

if (!function_exists('vcarb_product_logs_table')) {
    function vcarb_product_logs_table(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'vcarb_product_logs';
    }
}

if (!function_exists('vcarb_export_audit_table')) {
    function vcarb_export_audit_table(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'vcarb_export_audit';
    }
}

/**
 * ------------------------------------------------------------
 * Includes
 * ------------------------------------------------------------
 */
function vcarb_require_files(): void
{
    $files = [
        'includes/helpers.php',

        'includes/trait-vcarb-ajax-helpers.php',
        'includes/trait-vcarb-snapshot.php',
        'includes/trait-vcarb-period-trait.php',

        'includes/class-vcarb-reports-activator.php',
        'includes/class-vcarb-plugin-pages-admin.php',
        'includes/class-vcarb-reports-deactivator.php',

        'includes/class-vcarb-access.php',
        'includes/class-vcarb-capabilities.php',

        'includes/class-vcarb-insights.php',
        'includes/class-vcarb-insights-renderer.php',
        'includes/class-vcarb-export-audit.php',
        'includes/class-vcarb-calculator.php',
        'includes/class-vcarb-scheduler.php',
        'includes/class-vcarb-product-insights.php',
        'includes/class-vcarb-order-tracker.php',

        'includes/class-vcarb-dataset-service.php',
        'includes/class-vcarb-sustainability-summary.php',
        'includes/class-vcarb-admin-report-ajax.php',
        'includes/class-vcarb-insights-ajax.php',
        'includes/class-vcarb-backfill-ajax.php',

        'includes/vcarb-admin-guard.php',
        'includes/vcarb-access-guards.php',

        'includes/class-vcarb-exports.php',
        'includes/class-vcarb-reports-admin.php',
        'includes/class-vcarb-front-context.php',

        'includes/class-vcarb-pro-upsell.php',
        'includes/class-vcarb-engagement-notices.php',
        'includes/class-vcarb-woocommerce-check.php',

        'public/class-vcarb-dashboard.php',
    ];

    foreach ($files as $file) {
        $path = VCARB_PLUGIN_DIR . $file;

        if (!is_readable($path)) {
            continue;
        }

        require_once $path;
    }
}
vcarb_require_files();

/**
 * ------------------------------------------------------------
 * Register activator/deactivator hooks
 * ------------------------------------------------------------
 */
if (class_exists('VCARB_Reports_Activator')) {
    VCARB_Reports_Activator::register_hooks(VCARB_PLUGIN_FILE);
}

/**
 * ------------------------------------------------------------
 * Dependency helpers
 * ------------------------------------------------------------
 */
function vcarb_wc_active(): bool
{
    return class_exists('WooCommerce') || function_exists('WC');
}

if (class_exists('VCARB_Reports_Deactivator')) {
    register_deactivation_hook(
        VCARB_PLUGIN_FILE,
        ['VCARB_Reports_Deactivator', 'deactivate']
    );
}

function vcarb_admin_notice_wc_missing(): void
{
    if (vcarb_wc_active()) {
        return;
    }

    echo '<div class="notice notice-error"><p><strong>' .
        esc_html__('VerdantCart Carbon Reports:', 'verdantcart-ai-reports') .
        '</strong> ' .
        esc_html__('WooCommerce is required for this plugin to run.', 'verdantcart-ai-reports') .
        '</p></div>';
}

/**
 * ------------------------------------------------------------
 * DB helpers
 * ------------------------------------------------------------
 */
function vcarb_table_exists(string $table): bool
{
    global $wpdb;

    $table = trim($table);

    if ($table === '') {
        return false;
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Checking plugin-owned table existence.
    $found = $wpdb->get_var(
        $wpdb->prepare(
            'SHOW TABLES LIKE %s',
            $wpdb->esc_like($table)
        )
    );

    return is_string($found) && $found === $table;
}

function vcarb_install_logs_table(): void
{
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table           = esc_sql(vcarb_logs_table());
    $charset_collate = $wpdb->get_charset_collate();

    dbDelta("
        CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            view_type VARCHAR(10) NOT NULL DEFAULT 'month',
            period VARCHAR(20) NOT NULL,
            orders INT UNSIGNED NOT NULL DEFAULT 0,
            total_co2 DECIMAL(10,2) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_user_view_period (user_id, view_type, period),
            KEY view_period (view_type, period),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) {$charset_collate};
    ");
}

function vcarb_install_product_logs_table(): void
{
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table           = esc_sql(vcarb_product_logs_table());
    $charset_collate = $wpdb->get_charset_collate();

    dbDelta("
        CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            period VARCHAR(20) NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            orders INT UNSIGNED NOT NULL DEFAULT 0,
            total_co2 DECIMAL(10,2) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_period_product (period, product_id),
            KEY period (period),
            KEY product_id (product_id)
        ) {$charset_collate};
    ");
}

/**
 * ------------------------------------------------------------
 * DB migration
 * ------------------------------------------------------------
 */
function vcarb_migrate_db(): void
{
    global $wpdb;
    vcarb_install_logs_table();
    vcarb_install_product_logs_table();
   

    /*
     * Repair old imported rows that may not have view_type set.
     */
    $logs_table = esc_sql(vcarb_logs_table());

    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $wpdb->query(
        "UPDATE `{$logs_table}`
         SET view_type = 'week'
         WHERE (view_type = '' OR view_type IS NULL)
           AND period REGEXP '^[0-9]{4}-W[0-9]{2}$'"
    );

    $wpdb->query(
        "UPDATE `{$logs_table}`
         SET view_type = 'year'
         WHERE (view_type = '' OR view_type IS NULL)
           AND period REGEXP '^[0-9]{4}$'"
    );

    $wpdb->query(
        "UPDATE `{$logs_table}`
         SET view_type = 'month'
         WHERE (view_type = '' OR view_type IS NULL)
           AND period REGEXP '^[0-9]{4}-[0-9]{2}$'"
    );
    // phpcs:enable

    update_option('vcarb_db_version', VCARB_DB_VERSION, false);

}

/**
 * ------------------------------------------------------------
 * Cron
 * ------------------------------------------------------------
 */
function vcarb_register_cron_schedules(array $schedules): array
{
    if (!isset($schedules['weekly'])) {
        $schedules['weekly'] = [
            'interval' => 7 * DAY_IN_SECONDS,
            'display'  => __('Once Weekly', 'verdantcart-ai-reports'),
        ];
    }

    if (!isset($schedules['monthly'])) {
        $schedules['monthly'] = [
            'interval' => defined('MONTH_IN_SECONDS') ? MONTH_IN_SECONDS : (30 * DAY_IN_SECONDS),
            'display'  => __('Once Monthly', 'verdantcart-ai-reports'),
        ];
    }

    if (!isset($schedules['yearly'])) {
        $schedules['yearly'] = [
            'interval' => defined('YEAR_IN_SECONDS') ? YEAR_IN_SECONDS : (365 * DAY_IN_SECONDS),
            'display'  => __('Once Yearly', 'verdantcart-ai-reports'),
        ];
    }

    return $schedules;
}

function vcarb_schedule_cron_events(): void
{
    $now = time();

    if (!wp_next_scheduled('vcarb_weekly_event')) {
        wp_schedule_event($now + HOUR_IN_SECONDS, 'weekly', 'vcarb_weekly_event');
    }

    if (!wp_next_scheduled('vcarb_monthly_event')) {
        wp_schedule_event($now + (2 * HOUR_IN_SECONDS), 'monthly', 'vcarb_monthly_event');
    }

    if (!wp_next_scheduled('vcarb_yearly_event')) {
        wp_schedule_event($now + (3 * HOUR_IN_SECONDS), 'yearly', 'vcarb_yearly_event');
    }
}

function vcarb_handle_periodic_event(string $view): void
{
    if (!class_exists('VCARB_Calculator') || !class_exists('VCARB_Scheduler')) {
        return;
    }

    $view = in_array($view, ['week', 'month', 'year'], true) ? $view : 'month';

    $periods = VCARB_Calculator::get_period_keys($view);
    $current = isset($periods['current']) ? (string) $periods['current'] : '';

    if ($current !== '') {
        VCARB_Scheduler::schedule_aggregate_debounced($view, $current);
    }
}

function vcarb_handle_weekly_event(): void
{
    vcarb_handle_periodic_event('week');
}

function vcarb_handle_monthly_event(): void
{
    vcarb_handle_periodic_event('month');
}

function vcarb_handle_yearly_event(): void
{
    vcarb_handle_periodic_event('year');
}

/**
 * ------------------------------------------------------------
 * DB upgrade check
 * ------------------------------------------------------------
 */
function vcarb_maybe_upgrade_db(): void
{
    $installed = (string) get_option('vcarb_db_version', '');

    if ($installed !== VCARB_DB_VERSION) {
        vcarb_migrate_db();
    }
}
add_action('plugins_loaded', 'vcarb_maybe_upgrade_db', 5);

/**
 * ------------------------------------------------------------
 * Bootstrap
 * ------------------------------------------------------------
 */
function vcarb_register_default_filters(): void
{
    /*
     * No forced defaults here.
     * Calculation classes already pass safe default values to apply_filters().
     */
}

function vcarb_bootstrap(): void
{
    static $booted = false;

    if ($booted) {
        return;
    }

    $booted = true;

    vcarb_register_default_filters();

    if (!vcarb_wc_active()) {
        if (is_admin()) {
            add_action('admin_notices', 'vcarb_admin_notice_wc_missing');
        }

        return;
    }
    
    vcarb_register_default_filters();

    if (!vcarb_wc_active()) {
        if (is_admin()) {
            add_action('admin_notices', 'vcarb_admin_notice_wc_missing');
        }

        return;
    }

    if (class_exists('VCARB_Plugin_Pages_Admin')) {
        VCARB_Plugin_Pages_Admin::init();
    }

    if (class_exists('VCARB_Admin_Guard')) {
        VCARB_Admin_Guard::boot();
    }

    if (class_exists('VCARB_Order_Tracker')) {
        VCARB_Order_Tracker::register();
    }

    if (class_exists('VCARB_Scheduler') && method_exists('VCARB_Scheduler', 'register_hooks')) {
        VCARB_Scheduler::register_hooks();
    }

    if (class_exists('VCARB_Admin_Report_Ajax') && method_exists('VCARB_Admin_Report_Ajax', 'register')) {
        VCARB_Admin_Report_Ajax::register();
    }

    if (class_exists('VCARB_Insights_Ajax') && method_exists('VCARB_Insights_Ajax', 'register')) {
        VCARB_Insights_Ajax::register();
    }

    if (class_exists('VCARB_Backfill_Ajax') && method_exists('VCARB_Backfill_Ajax', 'register')) {
        VCARB_Backfill_Ajax::register();
    }

    if (class_exists('VCARB_Front_Context') && method_exists('VCARB_Front_Context', 'register_hooks')) {
        VCARB_Front_Context::register_hooks();
    }

    if (class_exists('VCARB_Dashboard')) {
        $GLOBALS['vcarb_reports_dashboard'] = $GLOBALS['vcarb_reports_dashboard'] ?? new VCARB_Dashboard();
    }

    if (class_exists('VCARB_Exports')) {
        $GLOBALS['vcarb_reports_exports'] = $GLOBALS['vcarb_reports_exports'] ?? new VCARB_Exports();
    }

    // Paid-extension discovery is handled only on the Advanced Tools page.
    // The legacy upsell helper is not initialized on normal admin screens.

    // v1.3.3 — Engagement notices (feedback + Pro upgrade prompts). These
    // register `admin_notices` and `admin_post_*` hooks internally and are
    // safe to call on the frontend too (they no-op outside wp-admin).
    if (class_exists('VCARB_Engagement_Notices')) {
        VCARB_Engagement_Notices::instance()->init();
    }

    // v1.4.0 — WooCommerce dependency check. Shows a friendly notice with
    // Install/Activate CTA when WooCommerce is missing, since VerdantCart
    // has nothing to work with in that state. This addresses the biggest
    // retention gap identified in wp.org stats (721 downloads,
    // <10 active installs — most likely cause is "installed on a WP site
    // without WooCommerce, saw empty dashboards, uninstalled").
    if (class_exists('VCARB_WooCommerce_Check')) {
        VCARB_WooCommerce_Check::instance()->init();
    }
}
add_action('plugins_loaded', 'vcarb_bootstrap', 20);

/**
 * ------------------------------------------------------------
 * Admin menu
 * ------------------------------------------------------------
 */
function vcarb_register_admin_menu(): void
{
    if (!class_exists('VCARB_Reports_Admin')) {
        return;
    }

    if (
        !isset($GLOBALS['vcarb_reports_admin']) ||
        !($GLOBALS['vcarb_reports_admin'] instanceof VCARB_Reports_Admin)
    ) {
        $GLOBALS['vcarb_reports_admin'] = new VCARB_Reports_Admin();
    }

    $admin = $GLOBALS['vcarb_reports_admin'];

    add_menu_page(
        __('VerdantCart Carbon Reports', 'verdantcart-ai-reports'),
        __('VerdantCart', 'verdantcart-ai-reports'),
        'manage_options',
        'verdantcart-carbon-reports',
        ['VCARB_Reports_Admin', 'render_settings_page'],
        'dashicons-leaf',
        56
    );

    add_submenu_page(
        'verdantcart-carbon-reports',
        __('Overview', 'verdantcart-ai-reports'),
        __('Overview', 'verdantcart-ai-reports'),
        'manage_options',
        'verdantcart-carbon-reports',
        ['VCARB_Reports_Admin', 'render_settings_page']
    );

    add_submenu_page(
        'verdantcart-carbon-reports',
        __('All Customers', 'verdantcart-ai-reports'),
        __('All Customers', 'verdantcart-ai-reports'),
        'manage_options',
        'vcarb-all-customers',
        [$admin, 'render_all_customers_page']
    );

    add_submenu_page(
        'verdantcart-carbon-reports',
        __('Dashboard', 'verdantcart-ai-reports'),
        __('Dashboard', 'verdantcart-ai-reports'),
        'manage_options',
        'vcarb-front-dashboard',
        ['VCARB_Reports_Admin', 'render_front_dashboard_menu_page']
    );

    add_submenu_page(
        'verdantcart-carbon-reports',
        __('Sustainability Report', 'verdantcart-ai-reports'),
        __('Report', 'verdantcart-ai-reports'),
        'manage_options',
        'vcarb-sustainability-summary',
        [$admin, 'render_sustainability_summary_page']
    );

    add_submenu_page(
        'verdantcart-carbon-reports',
        __('Backfill', 'verdantcart-ai-reports'),
        __('Backfill', 'verdantcart-ai-reports'),
        'manage_options',
        'vcarb-backfill',
        [$admin, 'render_backfill_page']
    );

    add_submenu_page(
        'verdantcart-carbon-reports',
        __('Advanced', 'verdantcart-ai-reports'),
        __('Advanced', 'verdantcart-ai-reports'),
        'manage_options',
        'vcarb-advanced',
        [$admin, 'render_advanced_page']
    );
}

/**
 * ------------------------------------------------------------
 * Plugin row action links (Plugins screen)
 * ------------------------------------------------------------
 *
 * Adds "Settings" + "Explore features" links next to the "Deactivate" link
 * on the WordPress Plugins admin page. The second link points back into the
 * plugin's own Advanced Tools page, where extension discovery is contextual.
 */
function vcarb_plugin_action_links(array $links): array
{
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('admin.php?page=verdantcart-carbon-reports')),
        esc_html__('Settings', 'verdantcart-ai-reports')
    );

    $features_link = sprintf(
        '<a href="%s" style="color:#15803d;font-weight:600;">%s</a>',
        esc_url(admin_url('admin.php?page=vcarb-advanced')),
        esc_html__('Explore features', 'verdantcart-ai-reports')
    );

    array_unshift($links, $settings_link, $features_link);

    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'vcarb_plugin_action_links');

/**
 * ------------------------------------------------------------
 * Hooks
 * ------------------------------------------------------------
 */
add_filter('cron_schedules', 'vcarb_register_cron_schedules');

add_action('vcarb_weekly_event', 'vcarb_handle_weekly_event');
add_action('vcarb_monthly_event', 'vcarb_handle_monthly_event');
add_action('vcarb_yearly_event', 'vcarb_handle_yearly_event');

add_action('admin_menu', 'vcarb_register_admin_menu');

/**
 * Pro discovery hint dismiss handler must be registered EARLY (not inside
 * admin_menu) because admin-post.php — the endpoint the dismiss button hits —
 * does not fire admin_menu. Without this early hook registration the X button
 * would render a blank page instead of dismissing the notice and redirecting
 * back to the previous screen.
 */
add_action('admin_init', 'vcarb_bootstrap_admin_post_handlers');

function vcarb_bootstrap_admin_post_handlers(): void
{
    if (!class_exists('VCARB_Reports_Admin')) {
        return;
    }

    if (
        !isset($GLOBALS['vcarb_reports_admin']) ||
        !($GLOBALS['vcarb_reports_admin'] instanceof VCARB_Reports_Admin)
    ) {
        $GLOBALS['vcarb_reports_admin'] = new VCARB_Reports_Admin();
    }

    // Defensive: re-register the hook if instantiation happened here (constructor
    // already adds it via init(), but instantiation may be skipped if the class
    // has self::$did_hooks set from an earlier admin_menu call).
    if (!has_action('admin_post_vcarb_dismiss_pro_hint')) {
        add_action(
            'admin_post_vcarb_dismiss_pro_hint',
            [$GLOBALS['vcarb_reports_admin'], 'handle_pro_hint_dismiss']
        );
    }

    // Same defensive pattern for the v1.4.1 launch-notice dismiss handler — it
    // also hits admin-post.php and must be registered even when admin_menu
    // didn't fire.
    if (!has_action('admin_post_vcarb_dismiss_launch_v141')) {
        add_action(
            'admin_post_vcarb_dismiss_launch_v141',
            [$GLOBALS['vcarb_reports_admin'], 'handle_v141_launch_dismiss']
        );
    }
}
