<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Core\Base;

use Develtio\WP\Forms\Core\Base\BaseController;

class Translations extends BaseController
{
    public function init()
    {
        add_action('plugins_loaded', array($this, 'load_my_transl'));
    }

    public function load_my_transl()
    {
        $domain = 'develtio-forms';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        $mofile = $domain . '-' . $locale . '.mo';

        load_textdomain( $domain, $this->plugin_path . 'lang/' . $mofile );
    }
}
