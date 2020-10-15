<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Core\Base;

use Develtio\WP\Forms\Core\Base\BaseController;

class SettingsLinks extends BaseController
{
    public function init()
    {
        add_filter("plugin_action_links_" . $this->plugin_file, array($this, 'settings_link'));
    }

    public function settings_link( $links )
    {
        $settings_link = '<a href="admin.php?page_develtio_plugin">'. __('Settings', 'develtio-forms').'</a>';
        array_push( $links, $settings_link );

        return $links;
    }
}