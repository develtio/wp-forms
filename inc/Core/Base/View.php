<?php
/**
 * @package Dforms
 */

namespace Develtio\Core\Base;

use Develtio\Core\Base\BaseController;

/**
 * Class View
 * @package Develtio\Core\Base
 */
class View extends BaseController {

    /**
     * Array of variables passed to view/template
     * @var array
     */
    private $vars = array();

    /**
     * Setter for template variables
     *
     * @param string $name  Name of the variable.
     * @param mixed  $value Value of the variable.
     *
     * @return void
     */
    public function __set( $name, $value ) {
        $this->vars[ $name ] = $value;
    }

    /**
     * Getter for template variables
     *
     * @param String $name Name of the variable.
     *
     * @return mixed
     */
    public function __get( $name ) {
        if ( isset( $this->vars[ $name ] ) ) {
            return $this->vars[ $name ];
        }

        return null;
    }

    /**
     * Render template
     *
     * Render template using ob_start/ob_get_clean/ob_end_clean functions
     *
     * @param String $template Template path.
     * @param array  $params   Available params.
     *
     * @return string
     *
     * @throws Exception If there is unexpected error.
     */
    public function render( $template, $params = array() ) {

        if ( is_array( $params ) && ! empty( $params ) ) {

            foreach ( $params as $key => $value ) {
                $this->vars[ $key ] = $value;
            }
        }

        try {

            ob_start();

            include $template;

            $output = ob_get_clean();

        } catch (\Exception $e) {

            ob_end_clean();
            throw $e;
        }

        return $output;
    }
}
