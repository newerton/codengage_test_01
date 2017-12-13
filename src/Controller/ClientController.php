<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ClientController extends AbstractController
{

    /**
     * @param $page
     * @param ClientRepository $clients
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/client", defaults={"page": "1"}, name="client_index")
     * @Route("/client/page/{page}", defaults={"_format"="html"}, requirements={"page": "[1-9]\d*"}, name="client_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function index($page, ClientRepository $clients, Request $request)
    {
        $query = $request->query->get('client');
        $latestClients = $clients->latestClient($page, $query);

        return $this->render('client/index.html.twig', ['clients' => $latestClients]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/client/create", name="client_create")
     * @Method({"GET", "POST"})
     */
    public function create(Request $request)
    {
        $model = new Client();

        $form = $this->createForm(ClientType::class, $model)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($model);
            $em->flush();

            $this->addFlash('success', 'Criado com sucesso!');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('client_create');
            }
            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/create.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Client $model
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/client/update/{id}", name="client_update")
     * @Method({"GET", "POST"})
     */
    public function update(Request $request, Client $model)
    {
        $form = $this->createForm(ClientType::class, $model)
            ->add('saveAndCreateNew', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();


            $this->addFlash('success', 'Atualizado com sucesso!');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('client_create');
            }

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/update.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Client $model
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/client/delete/{id}", name="client_delete")
     * @Method("GET")
     */
    public function delete(Client $model)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($model);
        $em->flush();
        $this->addFlash('success', 'Removido com sucesso!');
        return $this->redirectToRoute('client_index');
    }
}