<?php

declare(strict_types=1);

namespace App\Tests\Controller\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmsControllerTest extends WebTestCase
{
    public function testSend(): void
    {
        $soapClient = new \SoapClient('http://127.0.0.1:8000/service/sms/send?wsdl', ['trace' => 1]);

        try {
            $result = $soapClient->__soapCall('send', ['phoneNumber' => '89190092905', 'text' => 'Hello']);

            $this->assertSame('ok. Message "Hello" sent to phone number "89190092905"', $result);
        } catch (\SoapFault $exception) {
            dump($soapClient->__getLastRequest());
            dump($soapClient->__getLastResponse());
        }
    }
}
