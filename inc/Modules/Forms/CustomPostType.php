<?php
/**
 * @package Dforms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Develtio\Core\Api\MetaBoxApi;

class CustomPostType extends BaseController
{

    public $post_type_name;

    public $meta_box;

    public $custom_post_types = [];

    public $form_components;

    protected $post_type_prefix = 'df_';

    protected $displayed_types = ['text', 'email'];

    public function registerCustomPostTypes()
    {

        foreach ( $this->custom_post_types as $post_type ) {
            register_post_type( $post_type['name'], $post_type['args'] );
        }

    }

    /**
     * @param $instance
     */
    public function storeCustomPostTypes( CreateForm $instance  )
    {
        $this->post_type_name = $this->post_type_prefix . str_replace( '-', '_', $instance->form_slug );
        $this->form_components = $instance->form->getComponents();

        if ( strlen( $this->post_type_name ) > 20 ) {
            var_dump( 'Length of form name must be smaller than 15 chars' );
            return;
        }

        array_push( $this->custom_post_types,
            [
                'name' => $this->post_type_name,
                'args' => [
                    'labels' => [
                        'name' => $instance->form_name . ' form',
                        'singular_name' => $instance->form_name . ' form'
                    ],
                    'public' => true,
                    'has_archive' => false,
                    'supports' => [ 'editor' => false ],
                    'show_in_menu' => 'develtio_plugin',
                    'position' => 10
                ]
            ]
        );

        if ( !empty( $this->custom_post_types ) ) {
            add_action( 'init', [ $this, 'registerCustomPostTypes' ] );

            $this->meta_box = new MetaBoxApi();
            $this->meta_box->init(
                [
                    'id' => 'form_fields',
                    'title' => 'Form fields',
                    'post_type' => $this->post_type_name,
                ],
                $this->form_components
            );

            add_filter( 'manage_' . $this->post_type_name . '_posts_columns', [ $this, 'setTableColumns' ] );
            add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', [ $this, 'setColumnsData' ], 10, 2 );

        }
    }

    /**
     * Set columns on forms table list
     *
     * @param $columns
     * @return mixed
     */
    public function setTableColumns( $columns )
    {
        unset( $columns['date'] );

        foreach ( $this->form_components as $component ) {
            $label = '';

            if ( $component->getLabel() && strlen( $component->getLabel()->getChildren()[0] ) > 0 ) {
                $label = $component->getLabel()->getChildren()[0];
            } else {
                $label = $component->getControl()->placeholder;
            }

            if ( $label && in_array($component->getOption( 'type' ), $this->displayed_types) ) {
                $columns[$component->getControl()->name] = $label;
            }
        }

        $columns['date'] = __( 'Date' );
        return $columns;
    }

    /**
     * Set data for columns on form table list
     *
     * @param $column
     * @param $post_id
     */
    function setColumnsData( $column, $post_id )
    {

        foreach ( $this->form_components as $component ) {
            if ( in_array($component->getOption( 'type' ), $this->displayed_types) ) {
                if ( $column === $component->getControl()->name ) {
                    echo get_post_meta( $post_id, $component->getControl()->name, true );
                }
            }
        }
    }
}
