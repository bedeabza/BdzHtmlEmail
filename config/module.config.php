<?php
return array(
    'di' => array(
        'instance' => array(
            'BdzHtmlEmail\Email' => array(
                'parameters' => array(
                    'viewsDir'  => array(
                        dirname(__DIR__) . '/view/bdz-html-email/example'
                    ),
                    'transport'         => 'bdz-email-transport',
                    'message'           => 'bdz-email-message',
                    'templateRenderer'  => 'bdz-email-renderer',
                    'template'          => 'example'
                )
            ),
            'aliases' => array(
                'bdz-email'                 => 'BdzHtmlEmail\Email',
                'bdz-email-factory'         => 'BdzHtmlEmail\EmailFactory',
                'bdz-email-transport'       => 'Zend\Mail\Transport\Sendmail',
                'bdz-email-message'         => 'Zend\Mail\Message',
                'bdz-email-renderer'        => 'Zend\View\Renderer\PhpRenderer',
            )
        ),
    )
);
