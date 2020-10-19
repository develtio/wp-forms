<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Modules\Forms;

use Develtio\WP\Forms\Core\Base\BaseController;
use Nette\ComponentModel\Component;
use Nette\Forms\Form;
use Nette\Http\FileUpload;

/**
 * Class CreateForm provide for creating and manage forms
 *
 * @since 1.0.0
 * @package Develtio\WP\Forms\Modules\Forms
 */
class CreateForm extends BaseController
{
    /**
     * Nette form instance
     * @var Form
     */
    public $form;

    /**
     * Form name
     * @var string
     */
    public $form_name;

    /**
     * Form slug generate by sanitization
     * @var string
     */
    public $form_slug;

    /**
     * Submitted form values
     * @var array
     */
    public $form_values;

    /**
     * HTML for Form template
     * @var string
     */
    public $template;

    /**
     * HTML for successful form submitting
     * @var string
     */
    public $success_template;

    /**
     * Instance for CPT
     * @var CustomPostType
     */
    public $custom_post_types;

    /**
     * Prefix for created post type
     * @var string
     */
    public $post_type_prefix = 'df-';

    /**
     * Mail instance
     * @var Mail
     */
    public $mail;

    /**
     * Options form customizing features and properties
     * @var array
     */
    public $options = [
        'send_mail' => true,
        'send_confirm_mail' => false
    ];

    /**
     * CreateForm constructor.
     * @param string $form_name
     * @param array $options
     */
    public function __construct( $form_name, $options = [] )
    {
        parent::__construct();

        $this->success_template = '<p class="form__success">' . __('Thank you for contacting us. We have received your enquiry and will respond to you within 24 hours.') . ' </p>';
        $this->options = array_merge( $this->options, $options );
        $this->form = new Form;
        $this->mail = new Mail($this);
        $this->form_name = $form_name;
        $this->form_slug = sanitize_title( $form_name );
        $this->custom_post_types = new CustomPostType();

    }

    /**
     * This method is called after the form is created
     */
    public function save()
    {
        $this->setShortcode();
        $this->custom_post_types->storeCustomPostTypes( $this );

        if ( isset( $_POST ) && !is_admin() && $this->form->isSubmitted() && $this->form->isSuccess() ) {
            $this->form_values = $this->form->getValues();

            if ( $this->options['send_mail'] ) $this->mail->proceed();

            add_action( 'init', [ $this, 'saveFormData' ] );

        }
    }

    /**
     * Create short code based on the form slug
     */
    public function setShortcode()
    {
        add_shortcode( $this->form_slug, function () {
            ob_start();

            echo ( $this->template ) ? $this->getTemplate($this->form) : $this->form;

            return ob_get_clean();
        } );
    }

    /**
     * Get template with data
     *
     * @param Form $form Form instance
     * @return string Template string
     */
    public function getTemplate( $form )
    {
        return $this->buildTemplate( $this->template, $form );
    }

    /**
     * Pass data form form instance to form template
     *
     * @param string $template
     * @param null $form Form instance or null
     * @return string
     */
    public function buildTemplate( $template, $form = null )
    {
        $form = $form ? $form : $this->form;
        $vars = [];

        foreach ($this->form->getComponents() as $key => $value) {
            $vars['{' . $key . '_field}'] = $form[$key]->control;
            $vars['{' . $key . '_error}'] = $form[$key]->error ? '<p class="error">' . $form[$key]->error . '</p>' : '';
        }

        return strtr( $template, $vars );
    }

    /**
     * Set template
     * @param $template
     */
    public function setTemplate( $template )
    {
        $this->template = $template;
    }

    /**
     * Set success template
     * @param $template
     */
    public function setSuccessTemplate($template) {
        $this->success_template = $template;
    }

    /**
     * Create post and save form data to wp meta fields
     */
    public function saveFormData()
    {

        $post_title = $this->form_name . ' ' . date( "Y-m-d h:i:sa", time() );

        $post_arr = array(
            'post_title' => $post_title,
            'post_type' => $this->post_type_prefix . $this->form_slug,
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post( $post_arr );

        foreach ( $this->form_values as $key => $value ) {

            if ( is_array( $value ) ) {
                update_post_meta( $post_id, $key, $value );
                continue;
            }

            if ( $value instanceof FileUpload ) {

                if ( !empty( $_FILES[$key]['name'] ) ) {
                    $upload = wp_upload_bits( $_FILES[$key]['name'], null, file_get_contents( $_FILES[$key]['tmp_name'] ) );

                    if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
                        wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
                    } else {
                        update_post_meta( $post_id, $key, $upload );
                    }
                }

                continue;
            }

            update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
        }
    }

    /**
     * Return form field label
     *
     * @param Component $field Nette form component
     * @return mixed
     */
    public function getFormLabel($field) {

        if ( $field->getLabelPart() && strlen( $field->getLabelPart()->getChildren()[0] ) > 0 ) {
            return $field->getLabelPart()->getChildren()[0];
        } else {
            return $field->getControl()->placeholder;
        }

    }
}