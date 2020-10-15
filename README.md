<p align="center">
<img width="250px" align="center"  src="https://greenparrot.pl/software-house/app/themes/develtio/dist/images/logo_0336ff1d.svg">
</p>

# Develtio Forms #
Plugin that allows you to create and manage forms. Each created form generates a new post type
and saves all sent data as separate fields in WP administration.

## Use ##
We use the Nette From library to building the forms (https://doc.nette.org/en/3.0/forms)

Example
```php
if ( class_exists( '\Develtio\WP\Forms\Modules\Forms\CreateForm' ) ) {
    $options = [ 
        'send_mail' => true, // enables / disables sending an email, enabled by default
        'send_confirm_mail' => true // enables / disables sending of confirmation email, disabled by default
    ];

    $instance = new \Develtio\WP\Forms\Modules\Forms\CreateForm('Sample Form', $options);
    
    $mail = $instance->mail;
    $mail->setFrom(['noreply@example.com' => 'Sample form']);
    $mail->setTo(['info@example.com']);
    $mail->setTitle('Mail title');
    $mail->setConfirmMailField('contact_email'); // optional, if we send a confirmation e-mail, enter the name of the field from which the e-mail address is to be retrieved


    $form = $instance->form; // Nette Forms Instance
    $form->addText('contact_name')->setHtmlAttribute('placeholder', __('Name', 'develtio'));
    $form->addEmail( 'contact_email' )->setHtmlAttribute( 'placeholder', __( 'E-mail' ) )->setRequired( true );    

    $instance->save(); // Generate a form and post type
}
```

## Templates ##

##### Form template #####
By default, the fields will be displayed below each other, Nette Forms provides a modification of this display presented here https://doc.nette.org/en/3.0/form-rendering
Alternatively, you can manually set the display of the form with `$instance->setTemplate($template);` and special fields names `field-name_field` and `field-name_error` 
if you want to use forms as a shortcode.

##### Example #####
```html
<form method="post" action="/" class="form--default" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            {contact_name_field}
            {contact_name_error}
        </div>
        <div class="col-md-12">
            {contact_email_field}
            {contact_email_error}
        </div>
    </div>
    <div class="row">
        <div class="col-md-24">
            <button type="submit">Send</button>
        </div>
    </div>
</div>
```

If the shortcode is not going to be used, you can create a template using, references `$form['field-name']->control` and `$form['field-name']->error` instead `field-name_field` and `field-name_error` 

#### Success template ####
You can Customize thank you message in this way:
```php
    $instance->setSuccessTemplate('<p>Thank you for contacting us</p>');
```

#### Confirm template ####
| Method        | Params           | Description  |
| ------------- |:-------------:| -----:|
| `$mail->setConfirmTemplate( $tempalte )` | html or path to file | full mail confirm template |
| `$mail->setConfirmTemplateContent( $content )`      | html or string      |   mail content |
| `$mail->setConfirmTemplateTitle( $title )` | html or string      |    mail title|

#### Mail template ####
| Method        | Params           | Description  |
| ------------- |:-------------:| -----:|
| `$mail->setMailTemplate( $tempalte )` | html or path to file | full mail template |

In the place where the data should be displayed, place the `{content}` string.

## Display ##
The form can be displayed in two ways. You can create your own function based on the instance `$instance->form` and display it in the appropriate place on the page,
or You can use a shortcode that is generated automatically from the form name e.g. `Sample form` creates `[sample-form]` shortcode.