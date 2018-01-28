<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeliveryPromiseControllerTest extends WebTestCase
{
    public function testGetalldeliverypromise()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'delivery-promise');
    }

}
