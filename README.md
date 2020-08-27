<p align="center">
<img width="250px" align="center"  src="https://greenparrot.pl/software-house/app/themes/develtio/dist/images/logo_0336ff1d.svg">
</p>

# Develtio Forms #

## Basic usage ##
To build form we use Nette Forms https://doc.nette.org/en/3.0/forms
```php
if ( class_exists( '\Develtio\Modules\Forms\CreateForm' ) ) {
    $instance = new \Develtio\Modules\Forms\CreateForm();
    
    $form = $instance->form;
    $form->addText('contact_name')->setHtmlAttribute('placeholder', __('Name', 'develtio'));
    
    $instance->save('Contact bottom', $form);
}
```