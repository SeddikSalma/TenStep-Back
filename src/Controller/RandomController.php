<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RandomController extends AbstractController
{
    /**
     * @Route("/random", name="random")
     */
    public function index(): Response
    {
        $json = json_encode("salma");

        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;
    }
}
