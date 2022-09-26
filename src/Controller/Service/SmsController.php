<?php

declare(strict_types=1);

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
