<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return [
            Core\Base\Translations::class,
            Core\Pages\Dashboard::class,
            Core\Base\Enqueue::class,
            Core\Base\SettingsLinks::class,
            Modules\Forms\Init::class,
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

    /**
     * Load translations
     */
    public function load_my_transl()
    {
        load_plugin_textdomain('develtio-forms', FALSE, dirname(plugin_basename(__FILE__)).'/lang/');
    }
}