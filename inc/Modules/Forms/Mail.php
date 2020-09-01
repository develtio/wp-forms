<?php
/**
* @package  DeveltioForms
*/
namespace Develtio\Modules\Forms;

use Develtio\Core\Base\BaseController;
use Develtio\Core\Base\View;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\UploadControl;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Mail extends BaseController {

    /**
     * @var array
     */
    private $from;

    /**
     * @var array
     */
    private $to;

    /**
     * @var string
     */
    private $title;

    /**
     * @var CreateForm
     */
    private $form;

    /**
     * @var string
     */
    private $mail_template;

    /**
     * @var string
     */
    private $confirm_template;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $confirm_mail_field;

    /**
     * @var string
     */
    private $confirm_mail_title;

    /**
     * Mail constructor.
     * @param CreateForm $form
     */
    public function __construct(CreateForm $form)
    {
        parent::__construct();

        $this->form = $form;
        $this->title = $this->form->form_name . ' ' . date( "Y-m-d h:i:sa", time() );
        $this->mail_template = $this->plugin_path . 'inc/Modules/Forms/templates/email.php';
        $this->confirm_template = $this->plugin_path . 'inc/Modules/Forms/templates/email.php';
        $this->confirm_mail_title = __('Thank you for contacting us');

        $this->mailer = new Swift_Mailer(( new Swift_SmtpTransport( SMTP_HOST, SMTP_PORT, SMTP_ENCRYPTION ) )
            ->setUsername( SMTP_USERNAME )
            ->setPassword( SMTP_PASSWORD ));
    }

    /**
     * Send mails
     *
     * @return $this
     */
    public function send()
    {
        $this->sendDataMail();
        if($this->form->options['send_confirm_mail']) $this->sendConfirmMail();

        return $this;
    }

    /**
     * Send mail for site owner
     * @return $this
     */
    private function sendDataMail() {
        $content = '';
        $attachment = false;

        foreach ($this->form->form->getComponents() as $component) {

            if( $component instanceof SubmitButton) {
                continue;
            }

            if ( $component instanceof UploadControl ) {
                $file = $component->value;

                if($file->hasFile()) {
                    $attachment = Swift_Attachment::fromPath( $file->getTemporaryFile() )->setFilename( $file->getName() );
                }
                continue;
            }

            $content .= $this->form->getFormLabel($component) . ': ' . $component->value . '<br />';
        }

        $view = new View();
        $maliContent = $view->render($this->mail_template, ['content' => $content]);

        $message = ( new Swift_Message( $this->title ) )
            ->setFrom( $this->from )
            ->setTo( $this->to )
            ->setBody( $maliContent, 'text/html' );

        if($attachment) {
            $message->attach( $attachment );
        }

        $this->form->template = $this->form->success_template;
        $this->mailer->send( $message );

        return $this;
    }

    /**
     * Send an e-mail to the applicant
     * @return $this
     */
    private function sendConfirmMail() {
        $content = __('Thank you for contacting us. We have received your enquiry and will respond to you within 24 hours.');

        $view = new View();
        $maliContent = $view->render($this->mail_template, ['content' => $content]);

        $message = ( new Swift_Message( $this->title ) )
            ->setFrom( $this->from )
            ->setTo( $this->form->form->getValues()[$this->confirm_mail_field])
            ->setBody( $maliContent, 'text/html' );

        $this->mailer->send( $message );

        return $this;
    }

    /**
     * @param array $from
     */
    public function setFrom( $from ): void
    {
        $this->from = $from;
    }

    /**
     * @param array $to
     */
    public function setTo( $to ): void
    {
        $this->to = $to;
    }

    /**
     * @param mixed $title
     */
    public function setTitle( $title ): void
    {
        $this->title = $title;
    }

    /**
     * @param mixed $mail_template
     */
    public function setMailTemplate( $mail_template ): void
    {
        $this->mail_template = $mail_template;
    }

    /**
     * @param mixed $confirm_mail_field
     */
    public function setConfirmMailField( $confirm_mail_field ): void
    {
        $this->confirm_mail_field = $confirm_mail_field;
    }

}