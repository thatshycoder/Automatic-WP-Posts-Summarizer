<?php

namespace Awps;

defined('ABSPATH') || exit;

use Exception;

class Api
{

    private const ENDPOINT = 'https://api.meaningcloud.com/summarization-1.0';
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Get text summary via MeaningCloud text summarization API
     * 
     * @param string $text
     * @param int $length
     * @return string
     */
    public function get_text_summary($text, $length): string
    {
        $summary = '';
        $params = [
            'key'           => $this->key,
            'sentences'     => $length,
            'txt'           => $text
        ];

        try {

            $url = self::ENDPOINT . '?' . http_build_query($params, '', '&');
            $request = wp_remote_request($url);

            if (!is_wp_error($request)) {
                $response = json_decode($request['body'], true);

                if (array_key_exists('summary', $response)) {
                    return $response['summary'];
                } else {

                    throw new Exception("Error fetching summary" . json_encode($response));
                }
            } else {
                throw new Exception("Error fetching summary" . json_encode($request));
            }
        } catch (Exception $e) {
            // log error
            error_log($e);
        }

        return $summary;
    }
}
