<?php
/**
 * Html Email
 *
 * Send HTML email easily using template functionality provided by ZF2
 *
 * @category    Library
 * @author         Dragos Badea    <bedeabza@gmail.com>
 */

namespace BdzHtmlEmail;

use BdzHtmlEmail\Exception\InvalidMethodException,
    BdzHtmlEmail\Exception\MailNotReadyException,
    Zend\View\Resolver\TemplatePathStack as TemplateResolver,
    Zend\View\Model\ViewModel,
    Zend\Mime\Part as MimePart,
    Zend\Mime\Message as MimeMessage;

class Email
{
    /**
     * @var \Zend\Mail\Transport\TransportInterface
     */
    protected $transport;

    /**
     * @var \Zend\Mail\Message
     */
    protected $message;

    /**
     * List of directories where to look for email templates
     *
     * @var array
     */
    protected $viewsDir = array();

    /**
     * @var string
     */
    protected $template;

    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $templateRenderer;

    /**
     * @param \Zend\Mail\Transport\TransportInterface $transport
     */
    public function setTransport(\Zend\Mail\Transport\TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param \Zend\Mail\Message $message
     */
    public function setMessage(\Zend\Mail\Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return \Zend\Mail\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $viewsDir
     * @return void
     */
    public function setViewsDir($viewsDir)
    {
        $this->viewsDir = $viewsDir;
    }

    /**
     * @return array
     */
    public function getViewsDir()
    {
        return $this->viewsDir;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param \Zend\View\Renderer\RendererInterface $templateRenderer
     */
    public function setTemplateRenderer(\Zend\View\Renderer\RendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function getTemplateRenderer()
    {
        return $this->templateRenderer;
    }

    /**
     * @param array $params
     * @param null $overrideTo
     * @param null $overrideToName
     * @throws MailNotReadyException
     * @throws \Zend\Mail\Transport\Exception\RuntimeException
     */
    public function send(array $params = array(), $overrideTo = null, $overrideToName = null)
    {
        if (!$this->transport instanceof \Zend\Mail\Transport\TransportInterface) {
            throw new MailNotReadyException("The email transport was not defined");
        }

        if (!$this->message instanceof \Zend\Mail\Message) {
            throw new MailNotReadyException("No email message was defined");
        }

        if (!count($this->viewsDir)) {
            throw new MailNotReadyException("No template stack directories defined in viewsDir");
        }

        if (!$this->template) {
            throw new MailNotReadyException("No template was defined");
        }

        $this->message->setBody($this->createBody($params));

        if (null !== $overrideTo && null !== $overrideToName) {
            $this->message->setTo($overrideTo, $overrideToName);
        } else if (null !== $overrideTo) {
            $this->message->setTo($overrideTo);
        }

        if (!count($this->getMessage()->getTo())) {
            throw new MailNotReadyException("Please specify a to address");
        }

        $this->transport->send($this->message);
    }

    /**
     * @param array $params
     * @return \Zend\Mime\Message
     */
    public function createBody(array $params = array())
    {
        $this->templateRenderer->setResolver(new TemplateResolver(array('script_paths' => $this->viewsDir)));

        $viewModel = new ViewModel($params);
        $viewModel->setTemplate($this->template);

        $html = new MimePart($this->templateRenderer->render($viewModel));
        $html->type = 'text/html';

        $body = new MimeMessage();
        $body->setParts(array($html));

        return $body;
    }

    /**
     * @throws InvalidMethodException
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->message, $name)) {
            return call_user_func_array(array($this->message, $name), $arguments);
        }

        throw new InvalidMethodException("Method {$name}() does not exist");
    }
}
