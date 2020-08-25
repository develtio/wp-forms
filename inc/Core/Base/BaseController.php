<?php
/**
 * @package Dforms
 */

namespace Develtio\Core\Base;

class BaseController
{
    public $plugin_name = 'DForms';

    public $plugin_file;

    public $plugin_path;

    public $plugin_url;

    public $plugin_modules_path;

    public $modules = [];

    public $managers = [];

    public function __construct()
    {
        $this->plugin_file = plugin_basename( dirname( __FILE__, 4 ) ) . '/develtio-forms.php';
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 3 ) );
        $this->plugin_url = plugin_dir_url( dirname( __FILE__, 3 ) );
        $this->plugin_modules_path = $this->plugin_path . 'inc/Modules';

        $this->managers = [
            'develtio_forms' => 'Develtio forms',
            'develtio_cleaner' => 'Develtio cleaner',
        ];

        $this->setModules();
    }

    public function setModules() {

        if(is_dir($this->plugin_modules_path)) {
            $directories = glob($this->plugin_modules_path . '/*' , GLOB_ONLYDIR);
            foreach($directories as $directory) {
                $info = get_file_data( $directory . '/init.php',
                    array(
                        'name'     => 'Name',
                        'options_name'     => 'Options name',
                    )
                );

                $this->modules[] = $info;
            }
        }
    }
}