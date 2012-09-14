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

class EmailFactoryTest extends Framework\TestCase
{
    /**
     * @var \BdzHtmlEmail\EmailFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = $this->getLocator()->get('bdz-email-factory');
    }

    public function tearDown()
    {
        $this->factory = null;
    }

    public function testDiConfigurationWasInterpretedAsExpected()
    {
        $this->assertEquals('BdzHtmlEmail\EmailFactory', get_class($this->factory));
    }

    public function testFactoryReturnsEmailWhenCalledWithNoParams()
    {
        $this->assertEquals('BdzHtmlEmail\Email', get_class($this->factory->create()));
    }

    public function testAllTheOptionsArePassedCorrectly()
    {
        $email = $this->factory->create(array(
            'from'      => array('email@example.com', 'Your Name'),
            'subject'   => 'Subject of email',
            'template'  => 'the-template'
        ));

        $address = $email->getFrom()->current();

        $this->assertEquals('email@example.com', $address->getEmail());
        $this->assertEquals('Your Name', $address->getName());
        $this->assertEquals('Subject of email', $email->getSubject());
        $this->assertEquals('the-template', $email->getTemplate());
    }

    /**
     * @expectedException \BdzHtmlEmail\Exception\InvalidArgumentException
     */
    public function testPassingInvalidOptionThrowsException()
    {
        $this->factory->create(array(
            'inexisting' => 'value'
        ));
    }
}
