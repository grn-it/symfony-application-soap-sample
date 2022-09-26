# Symfony Application SOAP with WSDL Sample

The application shows how to use the [SOAP](https://en.wikipedia.org/wiki/SOAP) client and server.  
The description of the web service is made using the [WSDL](https://en.wikipedia.org/wiki/Web_Services_Description_Language).  

## Install
```
docker-compose exec symfony-web-application make install uid=$(id -u)
```

## Controller using SOAP Server
```php
namespace App\Controller\Service;

use App\Service\Sms\SmsSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
    #[Route('/service/sms/send', 'sms_send')]
    public function send(SmsSender $smsSender): Response
    {
        // absolute path: /public/wsdl/sms_sender.wsdl
        $soapServer = new \SoapServer('wsdl/sms_sender.wsdl');
        $soapServer->setObject($smsSender);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
        
        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());
        
        return $response;
    }
}
```
The `service` directory in the controller folder `Controller/Service/SmsController.php` means that the controller belongs to services, not web pages or resources.

## Service
```php
namespace App\Service\Sms;

class SmsSender
{
    public function send(string $phoneNumber, string $text): string
    {
        // SMS sending process...
        
        return sprintf('ok. Message "%s" sent to phone number "%s"', $text, $phoneNumber);
    }
}
```

## WSDL is Web-Service Description Language
```xml
<?xml version="1.0" encoding="ISO-8859-1"?>
<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
             xmlns:tns="urn:smssernderwsdl"
             xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
             xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
             xmlns="http://schemas.xmlsoap.org/wsdl/"
             targetNamespace="urn:smssernderwsdl">
    <types>
        <xsd:schema targetNamespace="urn:sendwsdl">
            <xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/"></xsd:import>
            <xsd:import namespace="http://schemas.xmlsoap.org/wsdl/"></xsd:import>
        </xsd:schema>
    </types>
    
    <message name="sendRequest">
        <part name="phoneNumber" type="xsd:string"/>
        <part name="text" type="xsd:string"/>
    </message>
    
    <message name="sendResponse">
        <part name="return" type="xsd:string"/>
    </message>
    
    <portType name="sendwsdlPortType">
        <operation name="send">
            <documentation>Send SMS</documentation>
            <input message="tns:sendRequest"/>
            <output message="tns:sendResponse"/>
        </operation>
    </portType>
    
    <binding name="sendwsdlBinding" type="tns:sendwsdlPortType">
        <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="send">
            <soap:operation soapAction="urn:arnleadservicewsdl#send" style="rpc"/>
            
            <input>
                <soap:body use="encoded" namespace="urn:sendwsdl" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
            </input>
            
            <output>
                <soap:body use="encoded" namespace="urn:sendwsdl" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
            </output>
        </operation>
    </binding>
    
    <service name="sendwsdl">
        <port name="sendwsdlPort" binding="tns:sendwsdlBinding">
            <soap:address location="http://127.0.0.1:8000/service/sms/send"/>
        </port>
    </service>
</definitions>
```

## Test using SOAP Client
```php
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
```

## Resources
- Symfony Docs: [How to Create a SOAP Web Service in a Symfony Controller](https://symfony.com/doc/current/controller/soap_web_service.html)
