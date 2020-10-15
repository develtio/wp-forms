<?php
/**
* @package  DeveltioForms
*/
namespace Develtio\WP\Forms\Modules\Forms;

use Develtio\WP\Forms\Core\Base\BaseController;
use Develtio\WP\Forms\Core\Base\View;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\UploadControl;
use ParagonIE\Sodium\Core\Curve25519\Ge\P1p1;
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

    private $mail_template_title;

    private $mail_template_footer;

    /**
     * @var mixed
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
     * @var string
     */
    private $confirm_template_title;

    /**
     * @var string
     */
    private $confirm_template_content;

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
        $this->confirm_template = $this->plugin_path . 'inc/Modules/Forms/templates/confirmEmail.php';
        $this->confirm_mail_title = __('Thank you for contacting us');
        $this->confirm_template_title = __('Thank you for contacting us!');
        $this->confirm_template_content = __('We have received your enquiry and will respond to you within 24 hours.');

        $this->mail_template_title = __('Contact Form Data');
        $this->mail_template_footer = '<a href="' . get_bloginfo( 'url' ) .' ">'.get_bloginfo( 'url' ) . '</a>';

        $this->setMailer();
    }

    protected function setMailer() {
        if(SMTP_HOST && SMTP_PORT && SMTP_USERNAME && SMTP_PASSWORD) {
            $this->mailer = new Swift_Mailer(( new Swift_SmtpTransport( SMTP_HOST, SMTP_PORT, SMTP_ENCRYPTION ) )
                ->setUsername( SMTP_USERNAME )
                ->setPassword( SMTP_PASSWORD ));
        }
    }

    /**
     * Send mails
     *
     * @return $this
     */
    public function proceed()
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
        $maliContent = $view->render($this->mail_template, ['content' => $content, 'title' => $this->mail_template_title, 'footer' => $this->mail_template_footer]);

        $message = ( new Swift_Message( $this->title ) )
            ->setFrom( $this->from )
            ->setTo( $this->to )
            ->setBody( $maliContent, 'text/html' );

        if($attachment) {
            $message->attach( $attachment );
        }

        $this->form->template = $this->form->success_template;
        $this->mailer->send( $message );
        $this->send();

        return $this;
    }

    /**
     * Send an e-mail to the applicant
     * @return $this
     */
    private function sendConfirmMail() {
        $view = new View();

        $maliContent = $view->render($this->confirm_template, ['content' => $this->confirm_template_content,  'title' => $this->confirm_template_title]);

        $message = ( new Swift_Message( $this->confirm_mail_title ) )
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

    /**
     * @param string $confirm_template
     */
    public function setConfirmTemplate( string $confirm_template ): void
    {
        $this->confirm_template = $confirm_template;
    }

    /**
     * @param string $confirm_mail_title
     */
    public function setConfirmMailTitle( string $confirm_mail_title ): void
    {
        $this->confirm_mail_title = $confirm_mail_title;
    }

    /**
     * @param string $confirm_template_title
     */
    public function setConfirmTemplateTitle( string $confirm_template_title ): void
    {
        $this->confirm_template_title = $confirm_template_title;
    }

    /**
     * @param string $confirm_template_content
     */
    public function setConfirmTemplateContent( string $confirm_template_content ): void
    {
        $this->confirm_template_content = $confirm_template_content;
    }

    /**
     * @param string|void $mail_template_title
     */
    public function setMailTemplateTitle( $mail_template_title ): void
    {
        $this->mail_template_title = $mail_template_title;
    }

    /**
     * @param string $mail_template_footer
     */
    public function setMailTemplateFooter( string $mail_template_footer ): void
    {
        $this->mail_template_footer = $mail_template_footer;
    }

}