<?php
    $client = new SoapClient("http://localhost:49319/Service1.asmx?WSDL");
    echo $client->HelloWorld()->HelloWorldResult
?>