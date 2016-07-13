<?php

namespace OroCRM\Bundle\SalesBundle\Tests\Unit\Entity;

use OroCRM\Bundle\SalesBundle\Entity\Lead;
use OroCRM\Bundle\SalesBundle\Entity\LeadEmail;

class LeadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Lead();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $now          = new \DateTime('now');
        $user         = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $address      = $this->getMock('Oro\Bundle\AddressBundle\Entity\Address');
        $customer     = $this->getMock('OroCRM\Bundle\SalesBundle\Entity\B2bCustomer');
        $channel      = $this->getMock('OroCRM\Bundle\ChannelBundle\Entity\Channel');
        $organization = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');

        return [
            'namePrefix'        => ['namePrefix', 'test', 'test'],
            'firstName'         => ['firstName', 'test', 'test'],
            'middleName'        => ['middleName', 'test', 'test'],
            'lastName'          => ['lastName', 'test', 'test'],
            'nameSuffix'        => ['nameSuffix', 'test', 'test'],
            'numberOfEmployees' => ['numberOfEmployees', 10, 10],
            'website'           => ['website', 'test', 'test'],
            'companyName'       => ['companyName', 'test', 'test'],
            'phoneNumber'       => ['phoneNumber', 'test', 'test'],
            'jobTitle'          => ['jobTitle', 'test', 'test'],
            'industry'          => ['nameSuffix', 'test', 'test'],
            'address'           => ['owner', $address, $address],
            'owner'             => ['owner', $user, $user],
            'createdAt'         => ['createdAt', $now, $now],
            'updatedAt'         => ['updatedAt', $now, $now],
            'notes'             => ['notes', 'test', 'test'],
            'customer'          => ['customer', $customer, $customer],
            'dataChannel'       => ['dataChannel', $channel, $channel],
            'organization'      => array('organization', $organization, $organization)
        ];
    }

    public function testEmails()
    {
        $emailOne = new LeadEmail('email-one@example.com');
        $emailTwo = new LeadEmail('email-two@example.com');
        $emailThree = new LeadEmail('email-three@example.com');
        $emails = array($emailOne, $emailTwo);

        $lead = new Lead();
        $this->assertSame($lead, $lead->resetEmails($emails));
        $actual = $lead->getEmails();
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $actual);
        $this->assertEquals($emails, $actual->toArray());

        $this->assertSame($lead, $lead->addEmail($emailTwo));
        $actual = $lead->getEmails();
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $actual);
        $this->assertEquals($emails, $actual->toArray());

        $this->assertSame($lead, $lead->addEmail($emailThree));
        $actual = $lead->getEmails();
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $actual);
        $this->assertEquals(array($emailOne, $emailTwo, $emailThree), $actual->toArray());

        $this->assertSame($lead, $lead->removeEmail($emailOne));
        $actual = $lead->getEmails();
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $actual);
        $this->assertEquals(array(1 => $emailTwo, 2 => $emailThree), $actual->toArray());

        $this->assertSame($lead, $lead->removeEmail($emailOne));
        $actual = $lead->getEmails();
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $actual);
        $this->assertEquals(array(1 => $emailTwo, 2 => $emailThree), $actual->toArray());
    }

    public function testGetPrimaryEmail()
    {
        $lead = new Lead();
        $this->assertNull($lead->getPrimaryEmail());

        $email = new LeadEmail('email@example.com');
        $email->setPrimary(true);
        $lead->addEmail($email);
        $this->assertSame($email, $lead->getPrimaryEmail());
    }
}
