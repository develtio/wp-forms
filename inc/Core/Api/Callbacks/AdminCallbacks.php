<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Core\Api\Callbacks;

use Develtio\WP\Forms\Core\Base\BaseController;

class AdminCallbacks extends BaseController
{
    public function adminDashboard()
    {
        return require_once( "$this->plugin_path/inc/Core/templates/admin.php" );
    }

    public function develtioOptionsGroup( $input )
    {
        $output = [];

        foreach ( $this->modules as $value ) {
            $output[ $value['options_name']] = isset( $input[ $value['options_name']] ) ? $input[ $value['options_name']] : 'test';
        }
        return $output;
    }

    public function develtioAdminSection()
    {
        echo __('Activate features', 'develtio-forms');
    }

    public function develtioTextExample( $args )
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $value = get_option( $option_name );
        $display = isset( $value[$name] ) ? $value[$name] : '';

        echo '<input type="text" class="regular-text" name="' . $option_name . '[' . $name . ']" value="' . $display . '">';
    }

    public function adminCpt()
    {
        echo 'CPT';
    }

    public function checkboxSanitize( $input )
    {
        $output = [];
        foreach ($this->modules as $value) {
            $output[ $value['options_name'] ] = isset($input[ $value['options_name']]);
        }
        return $output;
    }

    public function checkboxField( $args )
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $checkbox = get_option( $option_name );
        $class = $args['class'];
        $checked = (isset($checkbox[$name]) && $checkbox[$name])? 'checked' : '';

        echo '<input type="checkbox" ' . $checked . ' name="' . $option_name . '[' . $name . ']" class="' . $class . '" />';
    }
}