<?php
/**
 * @package Dforms
 */

namespace Develtio\Core\Api;

class MetaBoxApi
{

    private $meta_box_data;

    private $form_fields;

    private $fields = [];

    public function init( $meta_box_array, $form_fields )
    {
        $this->meta_box_data = $meta_box_array;
        $this->form_fields = $form_fields;


        add_action( 'admin_menu', [ $this, 'addMetaBox' ] );
        add_action( 'post_edit_form_tag', [ $this, 'update_edit_form' ] );
    }

    public function addMetaBox()
    {
        add_meta_box(
            $this->meta_box_data['id'],
            $this->meta_box_data['title'],
            [ $this, 'callback' ],
            $this->meta_box_data['post_type'],
            'normal',
            'default'
        );
    }

    public function callback( $post )
    {
        wp_nonce_field( 'develtio_forms_metaboxes', '_mishanonce' );

        foreach ( $this->form_fields as $field ) {

            $label = '';
            if ( $field->getLabel() && strlen( $field->getLabel()->getChildren()[0] ) > 0 ) {
                $label = $field->getLabel()->getChildren()[0];
            } else {
                $label = $field->getControl()->placeholder;
            }

            if ( $label ) {
                array_push( $this->fields,
                    [
                        'label' => $label,
                        'name' => $field->getControl()->name,
                        'type' => $field->getOption( 'type' )
                    ]
                );
            }
        }

        echo '<table class="form-table">
            <tbody>
            ';
        foreach ( $this->fields as $field ):
            echo '<tr >
                        <th ><label for="seo_title" > ' . $field['label'] . ' </label ></th >
                        <td >' . $this->getInputByType( $field, $post->ID ) . '</td >
                    </tr >';
        endforeach;
        echo '
            </tbody>
        </table>';
    }

    public function getInputByType( $field, $post_id )
    {

        switch ( $field['type'] ) {
            case 'text':
                return '<input type="text" disabled id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . esc_attr( get_post_meta( $post_id, $field['name'], true ) ) . '" class="regular-text" >';

            case 'email':
                return '<input type="email" disabled id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . esc_attr( get_post_meta( $post_id, $field['name'], true ) ) . '" class="regular-text" >';

            case 'file':
                $img = get_post_meta( $post_id, $field['name'], true );

                $image = ( $img['type'] === 'image/png' ) ? '<img src="' . $img['url'] . '" style="max-width: 150px;">' : "";
                $template =
                    '<p>' . wp_basename( $img['file'] ) . '</p>' .
                    $image . ' <a href="' . $img['url'] . '" download>Download file</a>';

                return $template;

            case 'checkbox':
                return '<input type="checkbox" disabled id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . esc_attr( get_post_meta( $post_id, $field['name'], true ) ) . '" >';

            case 'radio':
                return '<input type="radio" disabled id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . esc_attr( get_post_meta( $post_id, $field['name'], true ) ) . '" >';

            case 'textarea':
                return '<textarea disabled id="' . $field['name'] . '" name="' . $field['name'] . '" class="large-text">' . esc_textarea( get_post_meta( $post_id, $field['name'], true ) ) . '</textarea>';

            default:
                return 'Undefined type :(';

        }
    }

    /**
     * Change basic form enctype on edit post page.
     */
    function update_edit_form()
    {
        echo ' enctype="multipart/form-data"';
    }
}