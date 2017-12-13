<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1"}, name="default_index")
     **/
    public function index()
    {
        return $this->render('default/default.html.twig');
    }
}