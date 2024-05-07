<?php

namespace App\Controller;

use App\Entity\SensorValues;
use App\Repository\SensorValuesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
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

    #[Route("/dashboard", name: "dashboard")]
    public function dashboard(SensorValuesRepository $repository){
        $count = $repository->count();

        /** @var SensorValues $last */
        $last = $repository->findOneBy([], ['id' => 'DESC']);
        $last = $last->getCreatedAt()->format('d/m/y H:i:s');

        $els = $repository->findBy([], ['id' => 'DESC'], 5,0);
        $e = [];
        foreach ($els as $s){
            $t = json_decode($s->getData(), true);
            $t['date'] = $s->getCreatedAt()->format('d/m/y H:i:s');
            $e[] = json_encode($t);
        }

        return new JsonResponse([
           'count' => $count,
           'last' => $last,
           'data' => $e
        ]);
    }


    #[Route("/export", name: "export")]
    public function export(SensorValuesRepository $repository){
        $data = $repository->findAll();
        $csv = 'Piquet,Humidite,Temperature,Date' . "\n";

        foreach ($data as $el) {
            $e = json_decode($el->getData(), true);
            $csv .= $e['node'];
            $csv .= ',' . $e['hum_air'];
            $csv .= ',' . $e['temp'];
            $csv .= ',' . $el->getCreatedAt()->format('d/m/y H:i:s');
            $csv .= "\n";
        }

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }
}
