<?php

namespace EventEspresso\CalendarPlusShortcodes\shortcodes;

/**
 * CalendarPlus
 *
 * @package     Event Espresso
 * @subpackage  EventEspresso\CalendarPlusShortcodes\shortcodes
 * @author      Mohsin Sabir
 * @since       1.0.0
 */
class CalendarPlus implements CalendarPlusShortcode
{
    public const SHORTCODE = 'EVENTS_CALENDAR_PLUS';


    private static array $allowed_views = [
        'agenda',
        'day',
        'month',
        'week',
    ];

    private static array $allowed_filters = [
        'category',
        'location',
        'search',
        'tag',
        'venue',
    ];

    public function getShortcode(): string
    {
        return CalendarPlus::SHORTCODE;
    }


    public function processShortcode(array $attributes): string
    {
        $attributes = shortcode_atts(
            [
                'cat'             => '',
                'tag'             => '',
                'expired'         => '',
                'filters'         => '',
                'venue'           => '',
                'view'            => '',
                /// managing individual filters
                'filter-location' => '',
                'filter-search'   => '',
                'filter-venue'    => '',
                'filter-category' => '',
                'filter-tag'      => '',
            ],
            $attributes,
            CalendarPlus::SHORTCODE
        );

        $calendar_props = [
            'cat' => sanitize_text_field($attributes['cat']),
            'tag' => sanitize_text_field($attributes['tag']),
        ];

        // Handling the "filters" attribute (global filter toggle)
        if (in_array($attributes['filters'], ['true', 'false'], true)) {
            $calendar_props['filters'] = filter_var($attributes['filters'], FILTER_VALIDATE_BOOLEAN);
        }

        // Handling the "expired" attribute (global filter toggle)
        if (in_array($attributes['expired'], ['true', 'false'], true)) {
            $calendar_props['expired'] = filter_var($attributes['expired'], FILTER_VALIDATE_BOOLEAN);
        }

        // Handling the "view" attribute
        if (in_array($attributes['view'], CalendarPlus::$allowed_views, true)) {
            $calendar_props['view'] = sanitize_text_field($attributes['view']);
        }

        // Check the venue attribute and handle it
        if ($attributes['venue'] !== "") {
            $calendar_props['venue'] = sanitize_text_field($attributes['venue']);
        }

        // Loop through all the attributes and dynamically handle Individual filter
        foreach ($attributes as $key => $value) {
            // Check if the attribute key starts with 'filter-'
            if (strpos($key, 'filter-') === 0) {
                $filter_name = substr($key, 7);
                if ($value !== '') {
                    $calendar_props[$filter_name . "Filter"] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        if ($attributes['filters'] !== "" &&  ! in_array($attributes['filters'], ['true', 'false'], true)) {
            $filters = explode(',', $attributes['filters']);
            $filters = array_map('trim', $filters);
            foreach (CalendarPlus::$allowed_filters as $filter) {
                $calendar_props[$filter . 'Filter'] = in_array($filter, $filters, true);
            }
        }

        // Encode the props into a JSON object
        $calendar_props_json = json_encode($calendar_props);

        return "<div id='calendar-plus' class='calendar-plus' data-props='$calendar_props_json'></div>";
    }
}
