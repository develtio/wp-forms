<?php
/**
 * @package Dforms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Forms\Controls\UploadControl;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Develtio\Modules\Forms\CustomPostType;

class CreateForm extends BaseController
{

    public $form;

    public $form_name;

    public $form_slug;

    public $form_values;

    public $template;

    public $custom_post_types;

    public $post_type_prefix = 'df-';

    public $options = [
        'send_mail' => true
    ];

    public function __construct( $form_name, $options = [] )
    {
        parent::__construct();

        $this->options = array_merge( $this->options, $options );
        $this->form = new Form;
        $this->form_name = $form_name;
        $this->form_slug = sanitize_title( $form_name );
        $this->custom_post_types = new CustomPostType();

    }

    public function save()
    {
        $this->setShortcode();
        $this->custom_post_types->storeCustomPostTypes( $this );

        if ( isset( $_POST ) && !is_admin() && $this->form->isSubmitted() && $this->form->isSuccess() ) {
            $this->form_values = $this->form->getValues();

            if ( $this->options['send_mail'] ) $this->sendMailForm();

            add_action( 'init', [ $this, 'saveFormData' ] );

        }
    }

    /**
     * Create short code based on form name
     */
    public function setShortcode()
    {
        add_shortcode( $this->form_slug, function () {
            ob_start();

            echo ( $this->template ) ? $this->getTemplate($this->form) : $this->form;

            return ob_get_clean();
        } );
    }

    public function getTemplate( $form )
    {
        return $this->buildTemplate( $this->template, $form );
    }

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

    public function setTemplate( $template )
    {
        $this->template = $template;
    }

    /**
     * Save form data to wp meta fields
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
     * Send mail
     *
     * @return int
     */
    private function sendMailForm()
    {
        $mail_title = $this->form_name . ' ' . date( "Y-m-d h:i:sa", time() );

        $transport = ( new Swift_SmtpTransport( SMTP_HOST, SMTP_PORT, SMTP_ENCRYPTION ) )
            ->setUsername( SMTP_USERNAME )
            ->setPassword( SMTP_PASSWORD );
        $mailer = new Swift_Mailer( $transport );

        $content = '';
        $attachment = false;

        foreach ($this->form->getComponents() as $component) {

            if( $component instanceof SubmitButton) {
                continue;
            }

            if ( $component instanceof UploadControl ) {
                $file = $component->value;
                $attachment = Swift_Attachment::fromPath($file->getTemporaryFile())->setFilename($file->getName());

                continue;
            }

            $content .= $this->getFormLabel($component) . ': ' . $component->value . '<br />';
        }

        $message = ( new Swift_Message( $mail_title ) )
            ->setFrom( [ 'greengo88@gmail.com' => 'Formularz develtio' ] )
            ->setTo( [ 'michal.malinowski@greenparrot.pl' ] )
            ->setBody( $content, 'text/html' );

        if($attachment) {
            $message->attach( $attachment );
        }

        return $mailer->send( $message );
    }

    /**
     * Return form field label
     *
     * @param $field
     * @return mixed
     */
    public function getFormLabel($field) {
        $label = '';

        if ( $field->getLabelPart() && strlen( $field->getLabelPart()->getChildren()[0] ) > 0 ) {
            $label = $field->getLabelPart()->getChildren()[0];
        } else {
            $label = $field->getControl()->placeholder;
        }

        return $label;
    }
}