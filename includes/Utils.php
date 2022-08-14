<?php

namespace Awps;

defined('ABSPATH') || exit;

class Utils
{
    // TODO: Complete sanitizer
    public static function sanitize_inputs($inputs, $options): array
    {
        $options = get_option('awps_options');
        $sanitized_input = [];

        foreach ($inputs as $input_key => $input_value) {

            if (empty($input_value)) {
                $sanitized_input[$input_key] = $options[$input_key];
            } else {

                $input_value = str_replace(' ', '', $input_value);
                $input_value = trim(strip_tags(stripslashes($input_value)));
                $input_value = sanitize_text_field($input_value);

                //$input_value = $this->encrypt_keys($input_value);

                $sanitized_input[$input_key] = $input_value;
            }
        }

        return $sanitized_input;
    }

    public function getKeys()
    {
        $secrete_key = hash('sha256', 'kvvzukizqtmztvafoozlovydalgbriyi');
        $secrete_iv =  substr(hash('sha256', 'kvvzukizqtmztvafoozlovydalgbriyi'), 0, 16);
    }

    public static function encrypt($key)
    {

        return base64_encode(openssl_encrypt($key, AWPS_ENCRYPTION_METHOD, 'secrete_key', 0, 'secrete_iv'));
    }

    public static function decrypt($key)
    {

        return openssl_decrypt(base64_decode($key), AWPS_ENCRYPTION_METHOD, 'secrete_key', 0, 'secrete_iv');
    }
}
