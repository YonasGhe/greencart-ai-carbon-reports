=== VerdantCart Carbon Reports ===
Contributors: greencart2026
Tags: woocommerce, carbon footprint, emissions, sustainability, reporting
Author: VerdantCart
Support: support@verdantcart.ai
Author URI: https://verdantcart.ai/
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.0
Requires Plugins: woocommerce
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Estimate WooCommerce order emissions and review carbon reports with dashboards, trends, exports, and product insights.

== Description ==

VerdantCart Carbon Reports helps WooCommerce stores estimate carbon emissions from orders and review reporting data over time.

It turns completed WooCommerce orders into period-based carbon reporting snapshots, helping merchants understand trends, identify higher-impact products, and export reports for internal sustainability review.

The plugin calculates estimated emissions for eligible WooCommerce orders, aggregates reporting data by period, and displays results in dashboards for both store-level and customer-level reporting.

Main reporting features include:

* Estimated carbon emissions per WooCommerce order
* Monthly, weekly, and yearly reporting views
* Store-level and customer-level dashboards
* Trend comparison against previous periods
* Product hotspot reporting
* CSV and PDF exports
* Sustainability insights
* Snapshot-based reporting for stable historical results

VerdantCart Carbon Reports is useful for:

* WooCommerce stores that want visibility into estimated order emissions
* Merchants preparing internal sustainability summaries
* Brands monitoring emission trends over time
* Store owners who need exportable reporting data

This plugin provides operational sustainability reporting based on WooCommerce order data. It is not a certified ESG report, GHG Protocol report, legal compliance document, or verified carbon audit.

== Features ==

* Estimate emissions for WooCommerce orders
* View reporting by month, week, and year
* Review store-level reporting data
* Review customer-level reporting data
* Compare reporting periods
* Identify higher-impact products with hotspot reporting
* Use snapshot-based reporting for stable results
* Export reports as CSV or PDF
* View sustainability insights
* Run backfill for historical WooCommerce orders

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Make sure **WooCommerce** is installed and activated.
4. Open **VerdantCart Carbon Reports** from the WordPress admin menu.
5. Create a completed order or run **Backfill** to generate historical reporting data.

== Usage ==

After activation, open VerdantCart from the WordPress admin menu to view carbon reporting for your WooCommerce store.

The plugin can:

* Estimate emissions for eligible WooCommerce orders
* Aggregate reporting data by month, week, and year
* Display store-wide reporting and customer-facing dashboard data
* Highlight higher-impact products
* Export reports for further use

For stores with existing completed orders, use the Backfill screen once to build historical reporting data.

The customer-facing dashboard can be added to a WordPress page from the plugin setup flow or by using the dashboard shortcode.

== How It Works ==

VerdantCart Carbon Reports estimates emissions using internal calculation rules applied to WooCommerce order data.

Reporting data is stored inside your WordPress installation and organized into aggregated snapshots used by dashboards, comparisons, insights, hotspots, and exports.

The plugin uses snapshot-based reporting so historical reporting periods remain stable instead of being recalculated during normal dashboard page loads.

== Data & Privacy ==

All core calculations are performed locally in your WordPress installation.

The plugin does not require sending WooCommerce order data to an external service for its core reporting features.

== Requirements ==

* WordPress 6.4 or higher
* PHP 8.0 or higher
* WooCommerce 8.0 or higher

== Frequently Asked Questions ==

= Why does my dashboard show "No snapshot available"? =

That reporting period does not yet have generated snapshot data. Create a completed order or run the **Backfill** tool to generate reporting data.

= How are emissions calculated? =

Emissions are estimated using internal calculation rules applied to WooCommerce order data.

= Can I export reports? =

Yes. The plugin supports CSV and print-ready PDF exports.

= Which reporting periods are supported? =

The plugin supports month, week, and year reporting views.

= Does this plugin require WooCommerce? =

Yes. WooCommerce must be installed and active.

= Does the plugin send order data to an external service? =

No. Core reporting calculations are performed locally inside your WordPress installation.

= Is this a certified ESG or carbon audit report? =

No. VerdantCart Carbon Reports provides operational sustainability summaries and estimated emissions reporting. It is not a certified ESG report, GHG Protocol report, legal compliance document, or verified carbon audit.

== Screenshots ==

1. Store overview dashboard with emissions, orders, CO₂ per order, and period change.
2. Emissions trend chart comparing CO₂ and completed orders over time.
3. Product hotspot reporting showing higher-impact products for the selected period.
4. Sustainability insights with positives, warnings, risks, and recommendations.
5. CSV and PDF export actions for reporting data.

== Changelog ==

= 1.4.0 =
* NEW: WooCommerce dependency check with friendly one-click "Install/Activate WooCommerce" notice. Previously, sites without WooCommerce would install VerdantCart, see empty dashboards, and uninstall without knowing the requirement.
* NEW: `Requires Plugins: woocommerce` header added to plugin file — WordPress 6.5+ now enforces the dependency natively (may auto-install WooCommerce for you).
* FIX: `VCARB_VERSION` constant was stuck at 1.3.1 despite plugin header being 1.3.3. Now correctly reads 1.4.0.
* No functional changes to carbon calculations, dashboards, or reporting features.

= 1.3.3 =
* NEW: Optional in-admin feedback prompt (after 7 days of use) that lets merchants email the solo maintainer directly with feature ideas or issues — offering a free Pro+ license as thanks for thoughtful feedback.
* NEW: Optional in-admin prompt (after 14 days of use) describing what VerdantCart AI Pro adds — branded PDF reports, AI executive summaries, and one-click /carbon.txt publishing (Green Web Foundation v0.5 spec). Hidden automatically when the Pro plugin is active.
* Both prompts are per-user dismissible with "Remind me later" (30-day snooze) or "No thanks / Not interested" (permanent).
* No changes to carbon reporting, calculations, dashboards, exports, or product insights.

= 1.3.2 =
* Compat: Verified compatible with WordPress 7.1 (releasing August 19, 2026). "Tested up to" bumped from 7.0 to 7.1.
* No functional changes — carbon reporting, calculations, dashboards, exports, and product insights are unchanged from v1.3.1.

= 1.3.1 =
* Improved: The Pro tier feature list in the upgrade card now includes "Data Quality Center — find missing product data" to reflect the new Pro feature shipped in VerdantCart AI Pro 1.6.0.
* No changes to carbon reporting, calculations, dashboards, exports, or product insights — all free features continue to work exactly as in v1.3.0.

= 1.3.0 =
* New: One-time admin announcement for the VerdantCart AI Pro+ tier — describes the Sustainability Performance Score feature available in Pro+ (0–100 score, downloadable certificate PDF, monthly snapshots, 12-month trend, industry benchmark). Auto-expires 2026-08-15, dismissible per user, hidden automatically if Pro is already active.
* New: Text-based feature description card on the main carbon reports overview — lists what the Pro+ tier adds on top of the free reporting features. No fake data, no simulated UI — a plain feature description with a link to learn more.
* Improved: Pro upsell card redesigned as a side-by-side Pro vs Pro+ tier comparison. Pro ($19/mo) is labeled "Most popular" for store owners; Pro+ ($29/mo) is labeled "New" for brands selling sustainability. Both tiers include a 14-day free trial with no credit card required.
* Improved: Pro upsell CTAs now use separate UTM campaign labels so conversion data can be split between tiers and analyzed independently.
* No changes to carbon reporting, calculations, dashboards, exports, or product insights — all free features continue to work exactly as in v1.2.5.

= 1.2.5 =
* Hotfix: Resolved a fatal error ("Undefined constant META_MANAGED_PAGE") that could occur on the WordPress admin Pages list when running PHP 8.0+. The legacy backwards-compatibility constant is now correctly declared in the plugin-pages helper class. Affected users: anyone running PHP 8.0 or higher who opened wp-admin → Pages with plugin-managed pages present. The bug existed in prior versions but was silent on older PHP — PHP 8.x promotes the warning to a fatal.
* Hotfix: Prevented duplicate insertion of the [vcarb_dashboard] shortcode on the auto-provisioned dashboard page during plugin reactivation, network re-activation, or WordPress update cycles. The duplicate-detection logic now uses literal string detection instead of has_shortcode(), which was unreliable during activation because the shortcode handler is not yet registered at that point in the bootstrap.
* No changes to reporting, calculations, dashboards, exports, or the v1.4.1 launch notice. Existing pages with duplicate shortcodes need to be cleaned up manually (remove the extra [vcarb_dashboard] line in the page editor) — the hotfix prevents future duplication.

= 1.2.4 =
* New: In-plugin launch announcement for VerdantCart AI Pro v1.4.1 — appears at the top of wp-admin until 2026-07-04, dismissible per user, with one-click access to VerdantCart AI Pro information and the latest Scope 3 emissions guide.
* Hidden automatically for users who already have Pro active (via Freemius detection), so existing Pro customers see no noise.
* No changes to core reporting features — all dashboards, exports, snapshots, and insights continue to work exactly as in v1.2.3.

= 1.2.3 =
* Corrected Pro pricing display in admin upsell cards: now correctly shows $19/month and $179/year (Save 21% — over 2 months free).
* Added support contact: support@verdantcart.ai is now displayed in all upgrade prompts so users can reach the team directly.
* Improved Pro upgrade CTAs across admin: emphasized the 14-day free trial (no credit card required) instead of generic "Upgrade to Pro" buttons.
* Added subtle Pro discovery hint at the top of free plugin admin pages — informational, brand-colored, with one-click dismiss (auto-reappears after 30 days).
* Side-by-side layout on the Advanced sub-menu: "Included in Pro" and "License Management" cards now sit next to each other on wide screens, reducing scroll.
* Refined upsell messaging to clarify trial benefits and reduce friction at the moment of decision.
* No functional changes to core carbon reporting features — all reports, exports, and analytics work as before.

= 1.2.2 =
* Enhanced VerdantCart AI Pro experience and upgrade workflow.
* Added detailed feature descriptions and a Free vs Pro comparison table.
* Improved license management interface and validation experience.
* Added direct links to product, pricing, documentation, and upgrade resources.
* Updated plugin branding, author information, and website references to VerdantCart AI.
* Improved branding consistency throughout plugin metadata and admin screens.
* Removed legacy references and completed internal codebase cleanup.
* Improved Advanced Reporting admin page layout and usability.
* Enhanced overall admin UI consistency and maintainability.
* General stability, performance, and code quality improvements.

= 1.2.1 =
* Fixed: undefined method `VCARB_Export_Audit::table()` causing a fatal error on CSV and PDF export requests.
* Fixed: trend label "X%lower" / "X%higher" missing a space between the value and the word in CSV exports and PDF exports. Now correctly renders "X% lower" / "X% higher".
* Added: 11 new `apply_filters()` extension points so an external plugin can inject business name, logo URL, brand color, footer note, disclaimer, and per-format export metadata into the Sustainability Summary, PDF report view, and CSV exports without forking.
* Added: optional Pro-rendered brand block (logo + business name + brand color divider) above the Sustainability Summary header when a consumer of `verdantcart_report_logo_url` / `verdantcart_report_business_name` is registered. The page renders unchanged when no consumer is present.
* Improved: removed all remaining legacy `AmatorCarbon`/`amatorcarbon_*` references (admin slugs, helper aliases, filter dispatches, AJAX actions, meta key fallbacks, option fallbacks, uninstall cleanup). Internal prefix is now uniformly `vcarb`/`VCARB_`.
* Improved: simplified `verify_insights_nonce` to a single-action check now that the legacy nonce alias is no longer needed.

= 1.2.0 =
* Improved Plugin Check compatibility.
* Updated stable tag alignment with the main plugin version.
* Removed discouraged manual translation loading for WordPress.org compatibility.
* Improved VerdantCart namespace consistency.
* Improved dashboard, admin report, export, and backfill compatibility.
* Improved snapshot-based reporting reliability.
* Improved migration-safe compatibility with earlier internal builds.

= 1.1.0 =
* Updated internal naming to the VerdantCart Carbon Reports namespace.
* Improved dashboard, AJAX, export, and backfill naming consistency.
* Improved snapshot-based reporting flow for month, week, and year views.
* Improved admin dashboard period navigation and export handling.
* Preserved existing reporting table names to avoid losing historical data.
* Added safer uninstall cleanup for current and legacy lightweight options.
* Removed local paid feature gating from the WordPress.org build.

= 1.0.2 =
* Improved VerdantCart branding consistency.
* Cleaned language folder contents.
* Improved dashboard and reporting asset organization.
* Minor Plugin Check cleanup.

= 1.0.1 =
* Updated branding to VerdantCart Carbon Reports.
* Cleaned up readme content and plugin metadata.
* Improved submission readiness and compatibility updates.

= 1.0.0 =
* Initial public release.
* Estimated carbon emission tracking for WooCommerce orders.
* Month, week, and year reporting views.
* Sustainability insights.
* Product hotspot reporting.
* CSV and PDF export support.
* Backfill support for historical orders.

== Upgrade Notice ==

= 1.4.0 =
Adds a WooCommerce dependency check with a friendly one-click Install/Activate notice, so merchants without WooCommerce clearly see what VerdantCart needs instead of empty dashboards. Also uses the WordPress 6.5+ `Requires Plugins` header for native dependency handling. Fixes internal version constant that was stuck at 1.3.1. Recommended for all users.

= 1.3.3 =
Adds two optional dismissible admin prompts to bridge the gap between the free plugin and VerdantCart AI Pro. After 7 days, an "email the maintainer with feedback" prompt appears; after 14 days, a "meet VerdantCart Pro" prompt describes the paid tier (hidden when Pro is already installed). Both are per-user dismissible with a "Remind me later" snooze. No changes to carbon reporting features.

= 1.3.2 =
Compatibility patch — plugin is now marked "Tested up to WordPress 7.1" ahead of the August 19, 2026 release. No functional changes to carbon reporting.

= 1.3.1 =
Copy update — the Pro upgrade card now mentions the new "Data Quality Center" feature available in VerdantCart AI Pro 1.6.0. No changes to free features.

= 1.3.0 =
Adds a description of the new VerdantCart AI Pro+ tier and its Sustainability Performance Score feature. Includes a one-time announcement (auto-expires 2026-08-15) and a text-based feature card on the overview page describing what Pro+ adds. Free reporting features are unchanged.

= 1.2.5 =
Hotfix: resolves a fatal error on the wp-admin Pages list under PHP 8.0+, and prevents duplicate [vcarb_dashboard] shortcode insertion on reactivation. Recommended for all users.

= 1.2.4 =
Adds a one-time launch announcement (auto-expires 2026-07-04) for VerdantCart AI Pro v1.4.1 — try Pro free for 14 days, no card required. Hidden automatically if you already have Pro. No changes to core reporting.

= 1.2.3 =
Corrected Pro pricing display ($19/mo, $179/yr — save 21%), added support@verdantcart.ai contact, and improved Pro upgrade CTAs to emphasize the 14-day free trial.

= 1.2.2 =
Improves VerdantCart branding, Pro experience, licensing interface, upgrade flow, and overall admin usability.

= 1.2.0 =
Improves Plugin Check compatibility, version alignment, VerdantCart naming consistency, snapshot reporting reliability, and migration-safe compatibility.

= 1.1.0 =
Improves VerdantCart naming consistency, snapshot-based reporting, dashboard/export handling, and migration compatibility from earlier internal builds.

= 1.0.2 =
Branding consistency, language folder cleanup, and minor asset/reporting polish.

= 1.0.1 =
Branding, metadata, and readme update.

= 1.0.0 =
Initial public release of VerdantCart Carbon Reports.