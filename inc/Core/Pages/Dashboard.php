<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Core\Pages;

use Develtio\WP\Forms\Core\Api\SettingsApi;
use Develtio\WP\Forms\Core\Base\BaseController;
use Develtio\WP\Forms\Core\Api\Callbacks\AdminCallbacks;

class Dashboard extends BaseController
{
    public $settings;

    public $callbacks;

    public $pages = [];

    public function __construct()
    {
        parent::__construct();

        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->setSettings();

        $this->setSections();

        $this->setFields();

        $this->pages = [
            [
                'page_title' => 'Develtio plugin',
                'menu_title' => 'Develtio',
                'capability' => 'manage_options',
                'menu_slug' => 'develtio_plugin',
                'callback' => [ $this->callbacks, 'adminDashboard' ],
                'icon_url' => $this->plugin_url . 'assets/images/menu-icon.png',
                'position' => 110
            ]
        ];
    }

    public function init()
    {
        $this->settings->addPages( $this->pages )->withSubPage( __('Settings', 'develtio-forms') )->init();
    }

    public function setSettings()
    {

        $args = [
            [
                'option_group' => 'develtio_plugin_settings',
                'option_name' => 'develtio_plugin',
                'callback' => [ $this->callbacks, 'checkboxSanitize' ]
            ],
        ];

        $this->settings->setSettings( $args );
    }

    public function setSections()
    {
        $args = [
            [
                'id' => 'develtio_dashboard_index',
                'title' => __('Settings', 'develtio-forms'),
                'callback' => [ $this->callbacks, 'develtioAdminSection' ],
                'page' => 'develtio_plugin'
            ]
        ];

        $this->settings->setSections( $args );
    }

    public function setFields()
    {
        $args = [];
        foreach ( $this->modules as $value ) {
            $args[] = [
                'id' => $value['options_name'],
                'title' => $value['name'],
                'callback' => [ $this->callbacks, 'checkboxField' ],
                'page' => 'develtio_plugin',
                'section' => 'develtio_dashboard_index',
                'args' => [
                    'option_name' => 'develtio_plugin',
                    'label_for' => $value['options_name'],
                    'class' => 'ui-toggle'
                ]
            ];
        }

        $this->settings->setFields( $args );
    }
}
