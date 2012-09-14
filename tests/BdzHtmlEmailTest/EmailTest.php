<?php
/**
 * Html Email
 *
 * Send HTML email easily using template functionality provided by ZF2
 *
 * @category    Library
 * @author 	    Dragos Badea	<bedeabza@gmail.com>
 */

namespace BdzHtmlEmailTest;

use \BdzHtmlEmail\Email;

class EmailTest extends Framework\TestCase
{
    /**
     * @var \BdzHtmlEmail\Email
     */
    protected $email;

    public function setUp()
    {
        $this->email = $this->getLocator()->newInstance('bdz-email');
    }

    public function tearDown()
    {
        $this->email = null;
    }

    public function testDiConfigurationWasInterpretedAsExpected()
    {
        $this->assertEquals('Zend\Mail\Transport\Sendmail', get_class($this->email->getTransport()));
        $this->assertEquals('Zend\Mail\Message', get_class($this->email->getMessage()));
        $this->assertEquals('example', $this->email->getTemplate());
    }

    public function testBodyIsCreated()
    {
        $this->assertEquals('Zend\Mime\Message', get_class($this->email->createBody()));
    }

    public function testBodyContainsTagsFoundInTemplate()
    {
        $this->assertContains('<html>', $this->email->createBody()->getPartContent(0));
    }

    public function testBodyContainsParamsPassedForTheRenderer()
    {
        $content = 'this should be rendered';
        $this->assertContains($content, $this->email->createBody(array('content' => $content))->getPartContent(0));
    }

    /**
     * @expectedException \BdzHtmlEmail\Exception\MailNotReadyException
     * @expectedExceptionMessage The email transport was not defined
     */
    public function testExceptionIsThrownWhenNoTransportIsDefined()
    {
        $email = new Email();
        $email->send();
    }

    /**
     * @expectedException \BdzHtmlEmail\Exception\MailNotReadyException
     * @expectedExceptionMessage No email message was defined
     */
    public function testExceptionIsThrownWhenNoMessageIsCreated()
    {
        $email = new Email();
        $email->setTransport($this->getLocator()->get('bdz-email-transport'));
        $email->send();
    }

    /**
     * @expectedException \BdzHtmlEmail\Exception\MailNotReadyException
     * @expectedExceptionMessage No template stack directories defined in viewsDir
     */
    public function testExceptionIsThrownWhenNoViewsDirAvailable()
    {
        $this->email->setViewsDir(array());
        $this->email->send();
    }

    /**
     * @expectedException \BdzHtmlEmail\Exception\MailNotReadyException
     * @expectedExceptionMessage No template was defined
     */
    public function testExceptionIsThrownWhenNoTemplateIsSet()
    {
        $this->email->setTemplate('');
        $this->email->send();
    }

    /**
     * @expectedException \Zend\View\Exception\RuntimeException
     */
    public function testExceptionIsThrownWhenTheTemplateDoesNotExist()
    {
        $this->email->setTemplate('doesnt-exist');
        $this->email->send();
    }
}
