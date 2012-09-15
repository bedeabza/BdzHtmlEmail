# HTML Email module for Zend Framework 2

The main purpose of this module is to provide a simple way of sending HTML emails using ZF2 views as templates, while keeping the many advantages of being able to interact directly with the objects in the `zendframework/mail` package.
The module handles the HTML rendering of templates and offers the possibility of retrieving the `\Zend\Mail\Message` and `\Zend\Mail\Transport` instances to manipulate them further.

## Installation

### Composer

Add the following to your `composer.json` file:

```php
"require": {
    "bedeabza/bdz-html-email": "dev-master"
}
```

Then run `php composer.phar install`

### Manually

In order to start using the module clone the repo in your vendor directory or add it as a submodule if you're already using git for your project:

    git clone git@github.com:bedeabza/BdzHtmlEmail.git vendor/BdzHtmlEmail
    or
    git submodule add git@github.com:bedeabza/BdzHtmlEmail.git vendor/BdzHtmlEmail

The module will also be available as a Composer package soon.

The last thing that needs to be done is adding it to your application's list of active modules. For this, add the `BdzHtmlEmail` key in the `modules` array of your `config/application.config.php`

## Configuration

The module configuration is based entirely on the new DIC of ZF2. The default configuration uses Sendmail as default transport, but you can override this in your app's configuration. There is also a file `module.config.php.smtp-sample` which shows how the SMTP configuration should be done, but don't modify configuration in the vendor directory, use your own application or other module's configuration to override the default settings.

Also, the entire configuration is based on aliases, so you can swap any of the classes used by the module with your own if you whish to extend it.

One of the first things to do is adding your own views directory to the configuration, so your `module/Application/config/module.config.php` could contain the following:

```php
return array(
    'di' => array(
        'instance' => array(
            'bdz-email' => array(
                'parameters' => array(
                    'viewsDir'  => array(
                        dirname(__DIR__) . '/view/email'
                    )
                )
            )
        )
    )
);
```

The template lookup is using a stack, so you can use templates from multiple modules, just keep the file names different.

Also, the default template for emails is the example.phtml shipped with the module, you can override this in the parameters using the `template` key or by calling setTemplate() on the email object at runtime.

## Usage

There are 2 ways of obtaining an email object: using the factory, or configuring it by yourself. (the examples happen in the controller)

### Factory

```php
$email = $this->getServiceLocator()->get('bdz-email-factory')->create(array(
    'from' => array('email@domain', 'John Doe'),
    'subject' => 'Email subject',
    'template' => 'default'
));
```

### Manually

```php
$email = $this->getServiceLocator()->get('di')->newInstance('bdz-email');
$email->setFrom('email@domain', 'John Doe');
$email->setSubject('Email subject');
$email->setTemplate('default');
```

All of the above params are also injectable through the DI configuration, so it's up to you how you want to use it.

The email object also provides a way to retrieve or replace the `\Zend\Mail\Message` and the `\Zend\Mail\Transport\TransportInterface` objects:

```php
$message = $email->getMessage();
$transport = $email->getTransport();
```

The Email class implements the `proxy` pattern, so that any method called that doesn't exist in `\BdzHtmlEmail\Email` will be proxied to the underlying message object.

Sending the email can also be done in 2 ways:

```php
// 1
$email->send(array('firstTemplateParam' => 'value'), 'email@domain', 'Receiver Name');

// 2
$email->setTo('email@domain', 'Receiver Name');
$email->send(array('firstTemplateParam' => 'value'));
```

In both of the above use cases the name parameter is not compulsory. Also if your template doesn't require any variables, you can just call `send()`.

## Get in touch

You can contact me at bedeabza at gmail dot com or send pull requests for the repo.