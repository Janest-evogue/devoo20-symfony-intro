<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TwigController
 * @package App\Controller
 *
 * Préfixe de route :
 * toutes les url des routes définies dans cette classe
 * sont préfixées de /twig
 * @Route("/twig")
 */
class TwigController extends AbstractController
{
    /**
     * Du fait du préfixe de route, l'url de la page est /twig ou /twig/
     * @Route("/")
     */
    public function index()
    {
        return $this->render(
            'twig/index.html.twig',
            [

            ]
        );
    }
}
