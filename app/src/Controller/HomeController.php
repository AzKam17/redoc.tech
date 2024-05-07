<?php

namespace App\Controller;

use App\Entity\SensorValues;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return new Response("Ok");
    }

    #[Route('/data', name: 'app_data')]
    public function pidata(Request $request, EntityManagerInterface $manager)
    {
	$data = $request->getContent();
	$json = json_decode($data, true);
	$e = new SensorValues();
	$e->setData($data);
	$e->setPiquet($json["node"]);
	$manager->persist($e);
	$manager->flush();
	return new Response('Ok');
    }
}
