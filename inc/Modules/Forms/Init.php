<?php
/*
 * Name: Develtio forms
 * Options name: develtio_forms
 */

namespace Develtio\WP\Forms\Modules\Forms;

use Develtio\WP\Forms\Core\Base\BaseController;

/**
 * Class Init
 * @package Develtio\WP\Forms\Modules\Forms
 */
class Init extends BaseController {

    public function init()
    {
        $this->register_services();
    }

    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return [
            Enqueue::class
        ];
    }

    /**
     * Loop thought the classes, initialize them,
     * and call the init() method if it exists
     * @return void
     */
    public static function register_services()
    {
        foreach ( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'init' ) ) {
                $service->init();
            }
        }
    }

    /**
     * Initialize the class
     * @param class $class      Class from the services array
     * @return class instance   New instance of the class
     */
    private static function instantiate( $class )
    {
        return new $class();
    }
}