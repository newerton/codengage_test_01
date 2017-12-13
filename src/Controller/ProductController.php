<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{

    /**
     * @Route("/product", defaults={"page": "1"}, name="product_index")
     * @Route("/product/page/{page}", requirements={"page": "[1-9]\d*"}, name="product_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     **/
    public function index($page, ProductRepository $products, Request $request)
    {
        $query = $request->query->get('product');
        $latestProducts = $products->latestProduct($page, $query);

        return $this->render('product/index.html.twig', ['products' => $latestProducts]);
    }

    /**
     * @Route("/product/create", name="product_create")
     * @Method({"GET", "POST"})
     */
    public function create(Request $request)
    {
        $model = new Product();

        $form = $this->createForm(ProductType::class, $model)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($model);
            $em->flush();

            $this->addFlash('success', 'Criado com sucesso!');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('product_create');
            }
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/create.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/update/{id}", name="product_update")
     * @Method({"GET", "POST"})
     */
    public function update(Request $request, Product $model)
    {
        $form = $this->createForm(ProductType::class, $model)
            ->add('saveAndCreateNew', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Atualizado com sucesso!');
            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('product_create');
            }
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/update.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Product $model
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/product/delete/{id}", name="product_delete")
     * @Method("GET")
     */
    public function delete(Product $model)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($model);
        $em->flush();
        $this->addFlash('success', 'Removido com sucesso!');
        return $this->redirectToRoute('product_index');
    }
}