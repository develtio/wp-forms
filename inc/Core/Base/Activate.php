<?php
/**
 * @package DForms
 */

namespace Develtio\Core\Base;

class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();

        if ( get_option( 'develtio_plugin' ) ) {
            return;
        }

        $default = [];

        update_option('develtio_plugin', $default);
    }
}