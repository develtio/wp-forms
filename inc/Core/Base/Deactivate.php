<?php
/**
 * @package DForms
 */
namespace Develtio\WP\Forms\Core\Base;

class Deactivate {
    public static  function deactivate() {
        flush_rewrite_rules();
    }
}