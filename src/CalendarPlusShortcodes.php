<?php

namespace EventEspresso\CalendarPlusShortcodes;

use EventEspresso\CalendarPlusShortcodes\shortcodes\CalendarPlusShortcode;

/**
 * @package    CalendarPlus
 * @subpackage CalendarPlusShortcodes
 * @author     Event Espresso <support@eventespresso.com>
 * @since      1.0.0
 */
class CalendarPlusShortcodes
{

    private string $plugin_slug;

    private string $version;


    public function __construct(string $plugin_slug, string $version)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version     = $version;
        add_filter(
            'FHEE__EventEspresso_CalendarPlus_frontend_Frontend__registerShortcodes__shortcodes',
            [$this, 'registerShortcodes']
        );
    }


    public function registerShortcodes(array $all_shortcodes): array
    {
        $shortcode_paths = apply_filters(
            'FHEE__EventEspresso_CalendarPlusShortcodes_CalendarPlusShortcodes__registerShortcodes__shortcodes',
            glob(CALENDAR_PLUS_SHORTCODES_BASE_PATH . 'src/shortcodes/*.php')
        );
        foreach ($shortcode_paths as $shortcode_path) {
            if (strpos($shortcode_path, 'CalendarPlusShortcode.php') !== false) {
                continue;
            }
            // if shortcode is a local file, then convert to CalendarPlus FQCN
            $shortcode_class = strpos($shortcode_path, CALENDAR_PLUS_SHORTCODES_BASE_PATH) === 0
                ? 'EventEspresso\\CalendarPlusShortcodes\\shortcodes\\' . basename($shortcode_path, '.php')
                : $shortcode_path;
            if (class_exists($shortcode_class)) {
                $shortcode = new $shortcode_class();
                if ($shortcode instanceof CalendarPlusShortcode) {
                    $all_shortcodes[ $shortcode->getShortcode() ] = [$shortcode, 'processShortcode'];
                }
            }
        }
        return $all_shortcodes;
    }


    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function pluginSlug(): string
    {
        return $this->plugin_slug;
    }


    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function version(): string
    {
        // appended time() to version number for local, dev, or staging environments so that assets are not cached
        return wp_get_environment_type() !== 'production'
            ? $this->version . '.' . time()
            : $this->version;
    }
}
