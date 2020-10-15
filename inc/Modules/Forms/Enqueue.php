<?php
/**
 * @package  DeveltioForms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;

/**
 * Class Enqueue
 * @package Develtio\Modules\Forms
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

        if ( $option['js_validation'] ) {
            wp_enqueue_script( 'develtio-form', $this->plugin_url . 'dist/bundle.js' );
        }
    }
}