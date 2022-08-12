<?php

namespace Awpps;

defined('ABSPATH') || exit;

use Awpps\Api\MeaningCloudApi;

class Summarizer
{
    public function hooks(): void
    {
    }

    public function save_post_summary(): void
    {
    }

    public function update_post_summary(): void
    {
    }

    public function get_post_summary($post): string
    {
        $summary = MeaningCloudApi::get_text_summary($post, '');
        return $summary;
    }
}
