<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/hello")
     */
    public function hello()
    {
        return $this->render('index/hello.html.twig');
    }

    /**
     * Partie variable de la route (entre accolades)
     * La route matche /salut/Julien ou /salut/Ben
     * Le $qui en paramètre de la méthode (même nom que dans la route)
     * contient la valeur de cette partie variable
     *
     * @Route("/salut/{qui}")
     */
    public function salut($qui)
    {
        return $this->render(
            'index/salut.html.twig',
            [
                'qui' => $qui
            ]
        );
    }

    /**
     * Partie variable optionnelle de la route :
     * La route matche /bonjour/Julien
     * et matche aussi /bonjour ou /bonjour/
     * Si non présente dans la route, $nom vaut "tout le monde"
     * dans la méthode
     *
     * @Route("/bonjour/{nom}", defaults={"nom": "tout le monde"})
     */
    public function bonjour($nom)
    {
        return $this->render(
            'index/bonjour.html.twig',
            [
                'nom' => $nom
            ]
        );
    }

    /**
     * @Route("/coucou/{prenom}-{nom}", defaults={"nom": ""})
     */
    public function coucou($prenom, $nom)
    {
        $nomComplet = rtrim($prenom . ' ' . $nom);

        return $this->render(
            'index/coucou.html.twig',
            [
                'qui' => $nomComplet
            ]
        );
    }

    /**
     * id doit être un nombre (\d+ en expression régulière)
     *
     * @Route("/categorie/{id}", requirements={"id": "\d+"})
     */
    public function category($id)
    {
        return $this->render(
            'index/category.html.twig',
            [
                'id' => $id
            ]
        );
    }
}
