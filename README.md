<p align="center">
<img width="250px" align="center"  src="https://greenparrot.pl/software-house/app/themes/develtio/dist/images/logo_0336ff1d.svg">
</p>

# Develtio Forms #
Plugin pozwalający na tworzenie i zarządzanie formularzami. Każdy stworzony formularz generuje nowy post type
oraz zapisuje wszystkie wysłane dane jako oddzielne pola do wglądu w administracji WP.

## Użycie ##
Do budowania formularzy jest użyty Nette Forms https://doc.nette.org/en/3.0/forms

Przykład
```php
if ( class_exists( '\Develtio\Modules\Forms\CreateForm' ) ) {
    $options = [ 
        'send_mail' => true, // włącza/wyłącza wysłanie maila, domyślnie włączone
        'send_confirm_mail' => true // włącza/wyłącza wysłanie potwierdzenia, domyślnie wyłączone
    ];

    $instance = new \Develtio\Modules\Forms\CreateForm('Sample Form', $options);
    
    $mail = $instance->mail; // pobranie instancji wysyłki maila
    $mail->setFrom(['noreply@example.com' => 'Sample form']);
    $mail->setTo(['info@example.com']);
    $mail->setTitle('Mail title');
    $mail->setConfirmMailField('contact_email'); // opcjonalne, jeśli wysyłamy mail z potwierdzeniem należy podać nazwe pola, z którego ma być pobrany adres e-mail


    $form = $instance->form; // pobranie instancji NetteForms
    $form->addText('contact_name')->setHtmlAttribute('placeholder', __('Name', 'develtio'));
    $form->addEmail( 'contact_email' )->setHtmlAttribute( 'placeholder', __( 'E-mail' ) )->setRequired( true );    

    $instance->save(); // Generuje formularz oraz post type na jego bazie
}
```

## Szablony ##

##### Form template #####
Domyślnie zostaną wyświetlone pola pod sobą, Nette Forms udostępnia modyfikacje tego wyświetlania zaprezentowaną tutaj https://doc.nette.org/en/3.0/form-rendering
Alternatywnie, można użyć manualnego ustawienia wyglądy formularza za pomocą `$instance->setTemplate($template);` i używania specjalnie nazwanych stringów `field-name_field` oraz `field-name_error` 
w przypoadku jeśli chce się używać formularzy jako shortcode:

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

Jesli shortcode nie będzie używany, stworzyć szablon używając, odwołań `$form['field-name']->control` oraz `$form['field-name']->error` zamiast wyżej podanych stringów

#### Success template ####
Po wysłaniu formularza wyświetla się domyślnie informacja z podziękowaniem. Można ją zmienić za pomocą metody:
```php
    $instance->setSuccessTemplate('<p>Thank you for contacting us</p>');
```

## Wyświetlanie ##
Formularz można wyświetlić na 2 sposoby, albo spreparować własną funkcję bazującą na instancji `$instance->form` i wyświetlić ją w odpowiedni miejcu na stronie,
albo użyć shortcode, który generuje się automatycznie na bazie nazwy formularza. Formularz nazwany `Sample form` stworzy shortcode `[sample-form]`