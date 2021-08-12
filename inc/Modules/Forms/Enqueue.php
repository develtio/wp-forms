<?php
/**
 * @package  DeveltioForms
 */

namespace Develtio\WP\Forms\Modules\Forms;

use Develtio\WP\Forms\Core\Base\BaseController;

/**
 * Class Enqueue
 * @package Develtio\WP\Forms\Modules\Forms
 */
class Enqueue extends BaseController
{

    public function init()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    function enqueue()
    {
        $option = get_option('develtio_plugin');

        if ( array_key_exists('js_validation', $option)  && $option['js_validation']) {
            wp_enqueue_script( 'develtio-form', $this->plugin_url . 'dist/scripts/main.js', false, '1.8.3', true );
        }
    }
}