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


        add_action( 'admin_menu', [ $this, 'registerMetaFields' ] );
        add_action( 'save_post', [ $this, 'saveMeta' ], 10, 2 );
        add_filter( 'manage_sunset-contact_posts_columns', 'sunset_set_contact_columns' );
        add_action('post_edit_form_tag', [$this, 'update_edit_form']);
    }

    public function registerMetaFields()
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
        wp_nonce_field( 'somerandomstr', '_mishanonce' );

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
                        'type' => $field->getOption('type')
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

    public function saveMeta( $post_id, $post )
    {

        // nonce check
        if ( !isset( $_POST['_mishanonce'] ) || !wp_verify_nonce( $_POST['_mishanonce'], 'somerandomstr' ) ) {
            return $post_id;
        }

        // check current use permissions
        $post_type = get_post_type_object( $post->post_type );

        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }

        // Do not save the data if autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }


        // define your own post type here
        if ( $post->post_type != 'df_contact_bottom' ) {
            return $post_id;
        }

        foreach ( $this->fields as $field ) {
            update_post_meta( $post_id, $field['name'], sanitize_text_field( $_POST[$field['name']] ) );
        }

        return $post_id;

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

                $image = ($img['type'] === 'image/png') ? '<img src="' . $img['url'] .'" style="max-width: 150px;">' : "";
                $template =
                    '<p>' . wp_basename($img['file']) . '</p>' .
                    $image . ' <a href="' . $img['url'] . '" download>Download file</a>';

                return $template;

            case 'checkbox':
                return '<input type="checkbox" disabled id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . esc_attr( get_post_meta( $post_id, $field['name'], true ) ) . '" >';

            case 'radio':
                return '<input type="radio" disabled id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . esc_attr( get_post_meta( $post_id, $field['name'], true ) ) . '" >';

            case 'textarea':
                return '<textarea disabled id="' . $field['name'] . '" name="' . $field['name'] . '" class="large-text">' . esc_textarea(get_post_meta( $post_id, $field['name'], true )). '</textarea>';

            default:
                return 'Undefined type :(';

        }
    }

    function update_edit_form() {
        echo ' enctype="multipart/form-data"';
    } // end update_edit_form
}