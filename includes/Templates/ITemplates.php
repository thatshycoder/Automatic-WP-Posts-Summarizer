<?php

namespace Awps\Templates;

defined('ABSPATH') || exit;

interface ITemplates
{
    /**
     * Customizes how the summary is displayed in post content
     * 
     * @param array $args
     * @return string
     */
    public static function render($args): string;
}
