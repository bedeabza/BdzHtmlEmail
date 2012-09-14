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

use Zend\Di\ServiceLocatorInterface as ServiceLocator,
    Zend\Di\Di,
    BdzHtmlEmail\Exception\BadMethodCallException,
    BdzHtmlEmail\Exception\InvalidMethodException,
    BdzHtmlEmail\Exception\InvalidArgumentException,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class EmailFactory implements ServiceLocatorAwareInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param array $options
     * @return \BdzHtmlEmail\Email
     * @throws Exception\BadMethodCallException
     * @throws Exception\InvalidArgumentException
     */
    public function create(array $options = array())
    {
        $di = $this->serviceLocator->get('di');

        if (!$di instanceof Di) {
            throw new BadMethodCallException("No Di container found in the service locator");
        }

        $email = $di->newInstance('bdz-email');

        foreach ($options as $opt => $val) {
            if (!is_array($val)) {
                $val = array($val);
            }

            try {
                call_user_func_array(array($email, 'set' . ucfirst($opt)), $val);
            } catch (InvalidMethodException $e) {
                throw new InvalidArgumentException("No option {$opt} available for the Email or Message class");
            }
        }

        return $email;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}