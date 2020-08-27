<?php
/**
 * @package Dforms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
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

    public function save( $template = null )
    {

        $this->template = $template;
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

            echo ( $this->template ) ? $this->template : $this->form;

            return ob_get_clean();
        } );
    }

    /**
     * Save form data to wp meta fields
     */
    public function saveFormData()
    {

        $post_title = $this->form_name . ' ' . date( "Y-m-d h:i:sa", time() );
        $post_type_name = str_replace( '-', '_', $this->form_slug );

        $post_arr = array(
            'post_title' => $post_title,
            'post_type' => 'df_' . $post_type_name,
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
        foreach ( $this->form_values as $key => $value ) {

            if ( !is_object( $value ) ) {
                $content .= ucfirst( $key ) . ': ' . $value . '<br />';
            }
        }

        $message = ( new Swift_Message( $mail_title ) )
            ->setFrom( [ 'greengo88@gmail.com' => 'Formularz develtio' ] )
            ->setTo( [ 'michal.malinowski@greenparrot.pl' ] )
            ->setBody( $content, 'text/html' );
        $result = $mailer->send( $message );

        return $result;
    }
}