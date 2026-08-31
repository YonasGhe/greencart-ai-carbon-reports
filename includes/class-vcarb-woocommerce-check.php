<?php
/**
 * VerdantCart — WooCommerce dependency check
 *
 * VerdantCart's core reporting engine reads WooCommerce orders and products.
 * Without WooCommerce installed and active, the plugin has nothing to work
 * with and every screen would show empty dashboards, which reads as
 * "the plugin is broken." Merchants who installed to try VerdantCart then
 * uninstalled without understanding the actual requirement — a big source
 * of the retention gap we observed in wp.org stats (721 downloads,
 * <10 active installs).
 *
 * v1.4.0 adds:
 *   - `Requires Plugins: woocommerce` in the plugin header
 *     (WordPress 6.5+ handles this natively — shows a warning on the
 *     Plugins page, may offer to install WooCommerce automatically).
 *   - This class as a runtime fallback for older WP or for cases where
 *     WooCommerce is deactivated after VerdantCart is already installed.
 *
 * When WooCommerce is missing/inactive, we do NOT deactivate VerdantCart
 * automatically — that would surprise merchants and lose their settings.
 * Instead we render a clear, friendly notice with a one-click path to
 * install WooCommerce, and switch our own admin pages to a helpful empty
 * state instead of broken dashboards.
 *
 * @package VerdantCart_Carbon_Reports
 * @since   1.4.0
 */

defined('ABSPATH') || exit;

/**
 * Class VCARB_WooCommerce_Check
 */
final class VCARB_WooCommerce_Check
{
    /**
     * WordPress-standard basename that identifies WooCommerce in
     * `is_plugin_active()` calls.
     */
    const WC_PLUGIN_BASENAME = 'woocommerce/woocommerce.php';

    /**
     * User-meta key that hides the missing-WC notice for the current user
     * once they've dismissed it. We still keep the empty state on our own
     * pages — dismissal only silences the site-wide notice.
     */
    const META_NOTICE_DISMISSED = 'vcarb_wc_missing_notice_dismissed';

    /**
     * admin-post action name for the dismiss button.
     */
    const ACTION_DISMISS_NOTICE = 'vcarb_wc_missing_notice_dismiss';

    /**
     * Nonce action shared by the dismiss handler.
     */
    const NONCE_ACTION = 'vcarb_wc_missing_notice';

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Get the singleton instance.
     *
     * @return self
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Private constructor — use ::instance().
     */
    private function __construct()
    {
    }

    /**
     * Wire hooks. Called once from the plugin bootstrap.
     *
     * @return void
     */
    public function init(): void
    {
        // Notice appears on every wp-admin page.
        add_action('admin_notices', [$this, 'maybe_render_notice']);

        // Handle dismiss button clicks.
        add_action('admin_post_' . self::ACTION_DISMISS_NOTICE, [$this, 'handle_dismiss']);
    }

    /**
     * Whether WooCommerce is currently installed and active on this site.
     * Loads the plugin.php helper on-demand since we may be called before
     * WordPress has done it for us.
     *
     * @return bool
     */
    public static function is_woocommerce_active(): bool
    {
        // Fast path: WooCommerce declares this global function when active.
        if (function_exists('WC')) {
            return true;
        }

        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active(self::WC_PLUGIN_BASENAME);
    }

    /**
     * Whether WooCommerce is installed on the filesystem, regardless of
     * whether it's currently active. Used to pick the right CTA text
     * (Activate vs Install).
     *
     * @return bool
     */
    public static function is_woocommerce_installed(): bool
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        return isset($plugins[self::WC_PLUGIN_BASENAME]);
    }

    /**
     * Render the missing-WooCommerce notice at the top of every admin page
     * unless the current user has dismissed it or WooCommerce is active.
     *
     * @return void
     */
    public function maybe_render_notice(): void
    {
        if (self::is_woocommerce_active()) {
            return;
        }

        if (!current_user_can('activate_plugins')) {
            return;
        }

        $user_id = get_current_user_id();

        if ($user_id > 0 && (bool) get_user_meta($user_id, self::META_NOTICE_DISMISSED, true)) {
            return;
        }

        $this->render_notice_html();
    }

    /**
     * Print the notice markup. Broken out so it can be reused elsewhere
     * (e.g. as an empty state on VerdantCart admin pages).
     *
     * @return void
     */
    public function render_notice_html(): void
    {
        $is_installed = self::is_woocommerce_installed();

        $cta_url = $is_installed
            ? wp_nonce_url(
                self_admin_url('plugins.php?action=activate&plugin=' . self::WC_PLUGIN_BASENAME),
                'activate-plugin_' . self::WC_PLUGIN_BASENAME
            )
            : self_admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=577');

        $cta_label = $is_installed
            ? __('Activate WooCommerce', 'verdantcart-ai-reports')
            : __('Install WooCommerce', 'verdantcart-ai-reports');

        $cta_target = $is_installed ? '' : 'class="thickbox"';

        $dismiss_url = wp_nonce_url(
            add_query_arg(['action' => self::ACTION_DISMISS_NOTICE], admin_url('admin-post.php')),
            self::NONCE_ACTION
        );

        ?>
        <div class="notice notice-warning" style="border-left-color:#2e7d32;padding:14px 16px;">
            <p style="margin:0 0 8px;font-size:14px;">
                🌱 <strong><?php esc_html_e('VerdantCart Carbon Reports needs WooCommerce', 'verdantcart-ai-reports'); ?></strong>
            </p>
            <p style="margin:0 0 12px;">
                <?php
                if ($is_installed) {
                    esc_html_e(
                        'WooCommerce is installed on this site but not active. VerdantCart reads WooCommerce orders to calculate carbon emissions — please activate WooCommerce to get started.',
                        'verdantcart-ai-reports'
                    );
                } else {
                    esc_html_e(
                        'VerdantCart reads WooCommerce orders to calculate carbon emissions. Please install WooCommerce (free) to get started. If your store does not sell physical or shippable products, you can safely deactivate VerdantCart instead.',
                        'verdantcart-ai-reports'
                    );
                }
                ?>
            </p>
            <p style="margin:0;">
                <a href="<?php echo esc_url($cta_url); ?>" class="button button-primary" style="margin-right:8px;" <?php echo $cta_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute string. ?>>
                    <?php echo esc_html($cta_label); ?>
                </a>
                <a href="https://wordpress.org/plugins/woocommerce/" class="button" target="_blank" rel="noopener" style="margin-right:8px;">
                    <?php esc_html_e('Learn about WooCommerce', 'verdantcart-ai-reports'); ?>
                </a>
                <a href="<?php echo esc_url($dismiss_url); ?>" class="button-link" style="color:#646970;">
                    <?php esc_html_e('Dismiss', 'verdantcart-ai-reports'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * admin-post handler: hide the notice for this user until they clear
     * their user_meta manually. WooCommerce becoming active later doesn't
     * automatically restore the dismissed state — that would show the
     * notice for no reason.
     *
     * @return void
     */
    public function handle_dismiss(): void
    {
        if (!current_user_can('activate_plugins')) {
            wp_die(
                esc_html__('Permission denied.', 'verdantcart-ai-reports'),
                esc_html__('Forbidden', 'verdantcart-ai-reports'),
                ['response' => 403]
            );
        }

        check_admin_referer(self::NONCE_ACTION);

        update_user_meta(get_current_user_id(), self::META_NOTICE_DISMISSED, '1');

        wp_safe_redirect(wp_get_referer() ?: admin_url());
        exit;
    }
}
