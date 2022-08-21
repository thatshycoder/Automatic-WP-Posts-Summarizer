<?php

namespace Awps\Templates;

defined('ABSPATH') || exit;

class Main implements ITemplates
{
    /**
     * Custom class for styling content summary div
     */
    private const SUMMARY_CLASS = 'awps_summary';

    public static function render($args): string
    {
        $output     = '';
        $content    = isset($args['content']) ? $args['content'] : '';

        if (isset($args['title'])) {
            $output    .= '<h3>' . $args['title'] . '</h3>';
        }

        if (isset($args['summary'])) {
            $output    .= '<p>' . $args['summary'] . '</p>';
        }

        if (isset($args['position'])) {

            if ($args['position'] === 'after') {

                $output = $content . '<div class="' . self::SUMMARY_CLASS . '">' . $output . '</div>';
            } else {

                $output = '<div class="' . self::SUMMARY_CLASS . '">' . $output . '</div>' . $content;
            }
        } else {

            $output = $content . '<div class="' . self::SUMMARY_CLASS . '">' . $output . '</div>';
        }

        return $output;
    }
}
