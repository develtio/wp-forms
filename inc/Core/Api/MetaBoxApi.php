<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Core\Api;

class MetaBoxApi
{

    private $meta_box_data;

    private $form_components;

    private $parsed_fields = [];

    /**
     * Initialize metabox
     *
     * @param $meta_box_array
     * @param $form_components
     */
    public function init( $meta_box_array, $form_components )
    {
        $this->meta_box_data = $meta_box_array;
        $this->form_components = $form_components;

        add_action( 'admin_menu', [ $this, 'addMetaBox' ] );
        add_action( 'post_edit_form_tag', [ $this, 'update_edit_form' ] );
    }

    /**
     * Add metabox
     */
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

    /**
     * Callback for metabox. This function display form data inside metabox
     *
     * @param $post
     */
    public function callback( $post )
    {
        wp_nonce_field( 'develtio_forms_metaboxes', '_mishanonce' );

        foreach ( $this->form_components as $key => $field ) {

            $label = '';

            if ( $field->getLabelPart() && strlen( $field->getLabelPart()->getChildren()[0] ) > 0 ) {
                $label = $field->getLabelPart()->getChildren()[0];
            } else {
                $label = $field->getControl()->placeholder;
            }

            if ( $field->getOption( 'type' ) !== 'button' ) {
                array_push( $this->parsed_fields,
                    [
                        'label' => $label,
                        'name' => $key,
                        'type' => $field->getOption( 'type' )
                    ]
                );
            }
        }

        echo '<table class="form-table">
            <tbody>
            ';
        foreach ( $this->parsed_fields as $field ):
            echo '<tr >' . $this->getRowByType( $field, $post->ID ) . '</tr>';
        endforeach;
        echo '
            </tbody>
        </table>';
    }

    /**
     * Displaying form data row on admin post page
     *
     * @param array $field Form field
     * @param int $post_id Post id
     * @return string Row data
     */
    protected function getRowByType( $field, $post_id )
    {
        $row = '<td style="min-width: 170px;">_title</td><td>_value</td>';

        $title = $field['label'];

        switch ( $field['type'] ) {
            case 'hidden':
            case 'text':
            case 'email':
            case 'textarea':
                $value = '<p>'. esc_html( get_post_meta( $post_id, $field['name'], true ) ) . '</p>';
                break;
            case 'file':
                $img = get_post_meta( $post_id, $field['name'], true );
                if ( $img ) {
                    $image = ( $img['type'] === 'image/png' ) ? '<img src="' . $img['url'] . '" style="max-width: 150px;">' : "";
                    $template =
                        '<p>' . wp_basename( $img['file'] ) . '</p>' .
                        $image . ' <a href="' . $img['url'] . '" download>Download file</a>';

                    $value = $template;
                } else {
                    $value = __( 'No file attached', 'develtio_forms' );
                }
                break;
            case 'radio':
                $value = get_post_meta( $post_id, $field['name'], true );
                break;

            case 'checkbox':

                $values = get_post_meta( $post_id, $field['name'], true );
                $template = '';
                if ( is_array( $values ) ) {
                    $template .= '<ul>';
                    foreach ( $values as $value ) {
                        $template .= '<li>' . $value . '</li>';
                    }
                    $template .= '</ul>';
                } else {
                    $template = $values ?  '<span style="color:green;">' . $title .'</span>' : '<span style="color:red;">' . $title .'</span>';
                    $title = '';
                }

                $value = $template;
                break;
            default:
                $value = 'Undefined type :(';
                break;

        }

        return strtr( $row, [ '_title' => $title, '_value' => $value ] );
    }

    /**
     * Change basic form enctype on edit post page.
     */
    function update_edit_form()
    {
        echo ' enctype="multipart/form-data"';
    }
}