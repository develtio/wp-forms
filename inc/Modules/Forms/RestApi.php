<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Modules\Forms;

use Develtio\WP\Forms\Core\Base\BaseController;
use Develtio\WP\Hoya\Handlers\Step1FormHandleRequest;

/**
 * Class RestApi
 * @package Develtio\WP\Forms\Modules\Forms
 */
class RestApi extends BaseController
{
    /**
     * @var string
     */
    public $post_type_name;

    /**
     * @var CreateForm $form_instance
     */
    public $form_instance;

    private $custom_handler = null;

    /**
     * @param CreateForm $instance
     * @param null $customHandler
     * @return $this
     */
    public function exposeObject(CreateForm $instance, $customHandler = null)
    {
        $this->form_instance = $instance;
        $this->post_type_name = $instance->post_type_prefix . $instance->form_slug;

        $this->custom_handler = $customHandler;

        add_action('rest_api_init', [$this,'registerAPIRoute']);
        return $this;
    }

    public function registerAPIRoute()
    {
        register_rest_route('develtio/v1', '/form/' . $this->post_type_name,
            array(
                'methods' => 'POST',
                'callback' => [$this, 'handleRoute'],
                'permission_callback' => '__return_true',
            )
        );
    }

    public function handleRoute(\WP_REST_Request $request)
    {
        $this->form_instance->form->validate();
        $errors = $this->form_instance->form->getErrors();

        if(!empty($errors)){
            $response = new \WP_REST_Response([
                'success' => 'false',
                'errors' => $errors
            ]);
            $response->set_headers(array('Cache-Control' => 'no-cache'));
            return $response;
        }

        if ( $this->form_instance->options['send_mail'] ){
            $this->form_instance->mail->proceed();

            $response = new \WP_REST_Response([
                'success' => 'true'
            ]);
            $response->set_headers(array('Cache-Control' => 'no-cache'));
            return $response;
        }

        $customResponse = [];
        if($this->custom_handler){
            require_once $this->custom_handler[1].$this->custom_handler[0].'.php';
            $responseObject = new $this->custom_handler[0]($request, $this->form_instance);
            $customResponse = $responseObject->handle();
        }

        $response = new \WP_REST_Response($customResponse);
        $response->set_headers(array('Cache-Control' => 'no-cache'));
        $response->set_status(200);
        return $response;
    }

}
