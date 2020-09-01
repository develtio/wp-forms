<?php
/**
 * @package  DeveltioForms
 */
namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;

/**
 *
 */
class Enqueue extends BaseController
{
    public function init() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    function enqueue() {
        wp_enqueue_script( 'develtio-form', $this->plugin_url . 'dist/bundle.js' );
    }
}