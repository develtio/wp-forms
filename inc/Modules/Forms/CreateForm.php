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

    public $form_content;

    public $form_name;

    public $form_values;

    public $template;

    public $custom_post_types;

    public $form_components;

    public function __construct()
    {
        parent::__construct();

        $this->form = new Form;

        $this->custom_post_types = new CustomPostType();
    }

    public function save( $form_name, Form $form, $template = null )
    {
        $this->form_content = $form;
        $this->template = $template;
        $this->form_name = $form_name;
        $this->setShortcode();
        $this->form_components = $form->getComponents();
        $this->registerPostTypes();

        if ( isset($_POST) && !is_admin() && $form->isSubmitted() && $form->isSuccess() ) {
            $this->form_values = $form->getValues();
//            $this->sendMailForm();
            add_action( 'init', [ $this, 'saveFormData' ] );

        }
    }

    protected function registerPostTypes() {
        $this->custom_post_types->storeCustomPostTypes($this->getFormName($this->form_name), $this->form_components);
    }

    private function getFormName($form_name) {
        if(is_array($form_name)) return $form_name->label;

        return $form_name;
    }

    public function setShortcode()
    {
        add_shortcode( sanitize_title( $this->getFormName($this->form_name) ), function ( $atts ) {
            ob_start();

            echo ($this->template) ? $this->template : $this->form_content;

            return ob_get_clean();
        } );
    }

    public function saveFormData()
    {
        $content = '';
        foreach ( $this->form_values as $key => $value ) {
            if(!is_object($value)) {
                $content .= ucfirst( $key ) . ': ' . $value . '<br />';
            }
        }

        $post_title = $this->getFormName($this->form_name) . ' ' . date( "Y-m-d h:i:sa", time() );
        $post_type_name = str_replace( '-', '_', sanitize_title_with_dashes( $this->getFormName($this->form_name) ) );


        $post_arr = array(
            'post_title' => $post_title,
            'post_type' => 'df_' . $post_type_name,
            'post_content' => $content,
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post( $post_arr );

        foreach ( $this->form_values as $key => $value ) {

            if(!is_object($value)) {
                update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
            }

            if($value instanceof FileUpload) {
                if(!empty($_FILES[$key]['name'])) {
                    $upload = wp_upload_bits($_FILES[$key]['name'], null, file_get_contents($_FILES[$key]['tmp_name']));

                    if(isset($upload['error']) && $upload['error'] != 0) {
                        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                    } else {
                        update_post_meta($post_id, $key, $upload);
                    }
                }
            }
        }

    }

    private function sendMailForm()
    {
        $mail_title = $this->form_name . ' ' . date( "Y-m-d h:i:sa", time() );

        $transport = ( new Swift_SmtpTransport( SMTP_HOST, SMTP_PORT, SMTP_ENCRYPTION ) )
            ->setUsername( SMTP_USERNAME )
            ->setPassword( SMTP_PASSWORD );
        $mailer = new Swift_Mailer($transport);

        $content = '';
        foreach ( $this->form_values as $key => $value ) {

            if(!is_object($value)) {
                $content .= ucfirst( $key ) . ': ' . $value . '<br />';
            }
        }

        $message = (new Swift_Message($mail_title))
            ->setFrom(['greengo88@gmail.com' => 'Formularz develtio'])
            ->setTo(['michal.malinowski@greenparrot.pl'])
            ->setBody($content, 'text/html')
        ;
        $result = $mailer->send($message);

        return $result;
    }
}