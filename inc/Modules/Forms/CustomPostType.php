<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Modules\Forms;

use Develtio\WP\Forms\Core\Base\BaseController;
use Develtio\WP\Forms\Core\Api\MetaBoxApi;

/**
 * Class CustomPostType
 * @package Develtio\WP\Forms\Modules\Forms
 */
class CustomPostType extends BaseController
{
    /**
     * @var string
     */
    public $post_type_name;

    /**
     * @var MetaBoxApi
     */
    public $meta_box;

    /**
     * @var array
     */
    public $custom_post_types = [];

    /**
     * @var array
     */
    public $form_components;

    /**
     * @var CreateForm $form_instance
     */
    public $form_instance;

    /**
     * @var array
     */
    protected $displayed_types = ['text', 'email'];

    /**
     * Register CPT
     * @return $this
     */
    public function registerCustomPostTypes()
    {

        foreach ( $this->custom_post_types as $post_type ) {
            register_post_type( $post_type['name'], $post_type['args'] );
        }

        return $this;
    }

    /**
     * Store data for custom post types, metabox, and columns
     * @param CreateForm $instance
     * @return $this
     */
    public function storeCustomPostTypes( CreateForm $instance  )
    {
        $this->form_instance = $instance;
        $this->post_type_name = $instance->post_type_prefix . $instance->form_slug;
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

        return $this;
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
        unset( $columns['title'] );

        foreach ( $this->form_components as $component ) {
            $label = $this->form_instance->getFormLabel($component);

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
