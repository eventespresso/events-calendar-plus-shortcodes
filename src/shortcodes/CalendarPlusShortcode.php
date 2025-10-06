<?php

namespace EventEspresso\CalendarPlusShortcodes\shortcodes;

interface CalendarPlusShortcode
{
    public function getShortcode(): string;


    public function processShortcode(array $attributes): string;
}
