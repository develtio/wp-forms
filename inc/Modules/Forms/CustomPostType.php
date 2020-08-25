<?php
/**
 * @package Dforms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Develtio\Core\Api\SettingsApi;
use Develtio\Core\Api\Callbacks\AdminCallbacks;

class CustomPostType extends BaseController
{

    public $settings;

    public $subpages = [];

    public $callbacks;

    public $custom_post_types = [];

    public function init()
    {
//        $this->settings = new SettingsApi();

//        $this->callbacks = new AdminCallbacks();

//        $this->setSubpages();

//        $this->settings->addSubPages( $this->subpages )->init();

        $this->storeCustomPostTypes();

        if ( !empty( $this->custom_post_types ) ) {
            add_action( 'init', [ $this, 'registerCustomPostTypes' ] );
        }
    }

    public function registerCustomPostTypes()
    {

        foreach ( $this->custom_post_types as $post_type ) {
            register_post_type( $post_type['name'], $post_type['args'] );
        }

    }

    public function storeCustomPostTypes()
    {
        $this->custom_post_types = [
            [
                'name' => 'develtio_forms',
                'args' => [
                    'labels' => [
                        'name' => 'Develtio Forms',
                        'singular_name' => 'Defeltio Form'
                    ],
                    'public' => true,
                    'has_archive' => false
                ]
            ]
        ];
    }

    public function setSubpages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'develtio_plugin',
                'page_title' => 'Custom post type',
                'menu_title' => 'CPT',
                'capability' => 'manage_options',
                'menu_slug' => 'develtio_cpt',
                'callback' => [ $this->callbacks, 'adminCpt' ],
            ]
        ];
    }
}