<?php
/**
 * @package Dforms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Develtio\Core\Api\SettingsApi;
use Develtio\Core\Api\Callbacks\AdminCallbacks;
use Develtio\Core\Api\MetaBoxApi;

class CustomPostType extends BaseController
{

    public $settings;

    public $subpages = [];

    public $post_type_name;

    public $meta_box;

    public $callbacks;

    public $custom_post_types = [];

    public $form_values;

    public function init()
    {
        $this->settings = new SettingsApi();

//        $this->callbacks = new AdminCallbacks();

//        $this->setSubpages();

        $this->settings->addSubPages( $this->subpages )->init();

        $this->storeCustomPostTypes();

    }

    public function registerCustomPostTypes()
    {

        foreach ( $this->custom_post_types as $post_type ) {
            register_post_type( $post_type['name'], $post_type['args'] );
        }

    }

    public function storeCustomPostTypes( $post_type_name, $form_values )
    {
        $this->post_type_name = str_replace( '-', '_', sanitize_title_with_dashes( $post_type_name ) );
        $this->form_values = $form_values;

        if ( strlen( $this->post_type_name ) > 20 ) {
            var_dump( 'Length of form name must be smaller than 15 chars' );
            return;
        }

        array_push( $this->custom_post_types,
            [
                'name' => 'df_' . $this->post_type_name,
                'args' => [
                    'labels' => [
                        'name' => $post_type_name . ' form',
                        'singular_name' => $post_type_name . ' form'
                    ],
                    'public' => true,
                    'has_archive' => false,
                    'show_in_menu' => 'develtio_plugin'
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
                    'post_type' => 'df_' . $this->post_type_name,
                ],
                $this->form_values
            );

            add_filter( 'manage_' . 'df_' . $this->post_type_name . '_posts_columns', [ $this, 'setTableColumns' ] );
            add_action( 'manage_' . 'df_' . $this->post_type_name . '_posts_custom_column', [ $this, 'setColumnsData' ], 10, 2 );

        }
    }

    public function setSubpages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'develtio_plugin',
                'page_title' => 'Forms data',
                'menu_title' => 'Forms data',
                'capability' => 'manage_options',
                'menu_slug' => 'develtio_cpt',
                'callback' => [ $this->callbacks, 'adminCpt' ],
            ]
        ];
    }

    public function setTableColumns( $columns )
    {
        unset( $columns['date'] );

        foreach ( $this->form_values as $field ) {
            $label = '';
            if ( $field->getLabel() && strlen( $field->getLabel()->getChildren()[0] ) > 0 ) {
                $label = $field->getLabel()->getChildren()[0];
            } else {
                $label = $field->getControl()->placeholder;
            }

            if ( $label && ( $field->getControl()->type === 'text' || $field->getControl()->type === 'email' ) ) {
                $columns[$field->getControl()->name] = $label;
            }
        }

        $columns['date'] = __( 'Date' );
        return $columns;
    }

    function setColumnsData( $column, $post_id )
    {

        foreach ( $this->form_values as $field ) {
            if ( $field->getControl()->type === 'text' || $field->getControl()->type === 'email' ) {
                if ( $column === $field->getControl()->name ) {
                    echo get_post_meta( $post_id, $field->getControl()->name, true );
                }

            }
        }


//        switch ( $column ) {
//
//
//
//            case 'book_author' :
//                $terms = get_the_term_list( $post_id , 'book_author' , '' , ',' , '' );
//                if ( is_string( $terms ) )
//                    echo $terms;
//                else
//                    _e( 'Unable to get author(s)', 'your_text_domain' );
//                break;
//
//            case 'publisher' :
//                echo get_post_meta( $post_id , 'publisher' , true );
//                break;
//
//        }
    }
}