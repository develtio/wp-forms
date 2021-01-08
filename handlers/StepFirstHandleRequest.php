<?php

class StepFirstHandleRequest
{
    private $request;
    private $form;

    function __construct($request, $form = false)
    {
        $this->request = $request;
        $this->form = $form;
    }

    public static function handle()
    {
        /**
         * Logic goes here
         */

        return [
            'success' => 'true',
            'token' => 'generated_token'
        ];
    }


}