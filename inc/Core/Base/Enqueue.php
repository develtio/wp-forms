<?php
/**
 * @package Dforms
 */

namespace Develtio\Core\Base;

use Develtio\Core\Base\BaseController;

class Enqueue extends BaseController
{
    public function init()
    {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    public function enqueue()
    {
//        wp_enqueue_style( 'my_style', $this->plugin_url . 'assets/style.css' );
//        wp_enqueue_scripts( 'my_scripts', $this->plugin_url . 'assets/scripts.js' );
    }
}