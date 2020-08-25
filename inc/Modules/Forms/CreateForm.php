<?php
/**
 * @package Dforms
 */

namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Nette\Forms\Form;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use function App\template;

class CreateForm extends BaseController
{

    public $form;

    public $form_content;

    public $form_name;

    public $form_values;

    public $template;

    public function __construct()
    {
        parent::__construct();

        $this->form = new Form;
    }

    public function save( string $form_name, Form $form, $template = null )
    {
        $this->form_content = $form;
        $this->template = $template;
        $this->form_name = $form_name;
        $this->setShortcode();

        if ( isset($_POST) && !is_admin() && $form->isSubmitted() && $form->isSuccess() ) {
            $this->form_values = $form->getValues();
            $this->sendMailForm();
            add_action( 'init', [ $this, 'saveFormData' ] );
        }
    }

    public function setShortcode()
    {
        add_shortcode( sanitize_title( $this->form_name ), function ( $atts ) {
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

        $post_title = $this->form_name . ' ' . date( "Y-m-d h:i:sa", time() );

        $post_arr = array(
            'post_title' => $post_title,
            'post_type' => 'develtio_forms',
            'post_content' => $content,
            'post_status' => 'publish',
        );

        wp_insert_post( $post_arr );

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