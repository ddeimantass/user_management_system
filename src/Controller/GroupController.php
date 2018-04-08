<?php
namespace App\Controller;

use App\Entity\Club;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class GroupController extends Controller {

    /**
     * @Route("/admin/groups", name="group_list")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function group_list(Request $request) {
        //$groups = $this->getDoctrine()->getRepository(Club::class)->findAll();

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM App:Club c";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );

        return $this->render('group/index.html.twig', array('groups' => $pagination));
    }

    /**
     * @Route("/admin/group/new", name="new_group")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newGroup(Request $request) {
        $group = new Club;
        $form = $this->createFormBuilder($group)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirectToRoute('group_list');
        }
        return $this->render('common/new.html.twig', array(
            'form' => $form->createView(),
            'title' => "group",
        ));
    }

    /**
     * @Route("/admin/group/delete/{id}")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteGroup($id) {
        $group = $this->getDoctrine()->getRepository(Club::class)->find($id);

        if($group && count($group->getUsers()) === 0){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }
        return $this->redirectToRoute('group_list');

    }

    /**
     * @Route("/admin/group/edit/{id}", name="group_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editGroup(Request $request, $id) {
        $group = $this->getDoctrine()->getRepository(Club::class)->find($id);

        $form = $this->createFormBuilder($group)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirectToRoute('group_list');
        }
        return $this->render('common/edit.html.twig', array(
            'form' => $form->createView(),
            'title' => "group",
        ));
    }


}