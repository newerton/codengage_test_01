<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    /**
     * @Route("/order", defaults={"page": "1"}, name="order_index")
     * @Route("/order/page/{page}", requirements={"page": "[1-9]\d*"}, name="order_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     **/
    public function index($page, OrderRepository $orders, Request $request)
    {
        $query = $request->query->get('order');
        $latestOrders = $orders->latestOrder($page, $query);

        return $this->render('order/index.html.twig', ['orders' => $latestOrders]);
    }

    /**
     * @Route("/order/create", name="order_create")
     * @Method({"GET", "POST"})
     */
    public function create(Request $request)
    {
        $model = new Order();
        $form = $this->createForm(OrderType::class, $model)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($model->getItems() as $items) {
                $model->addItems($items);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($model);
            $em->flush();

            $this->addFlash('success', 'Criado com sucesso!');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('order_create');
            }
            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/create.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Order $model
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/order/delete/{id}", name="order_delete")
     * @Method("GET")
     */
    public function delete(Order $model)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($model);
        $em->flush();
        $this->addFlash('success', 'Removido com sucesso!');
        return $this->redirectToRoute('order_index');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/order/product_price", name="order_product_price")
     * @Method("GET")
     */
    public function orderProductPrice(Request $request)
    {
        $id = $request->get('id');
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        return new Response($product->getPrice());
    }
}