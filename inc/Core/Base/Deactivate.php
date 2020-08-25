<?php
/**
 * @package DForms
 */
namespace Develtio\Core\Base;

class Deactivate {
    public static  function deactivate() {
        flush_rewrite_rules();
    }
}