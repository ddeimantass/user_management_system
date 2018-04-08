<?php
namespace App\Controller;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\Club;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class UserController extends Controller {


    /**
     * @Route("/", name="user")
     */
    public function user() {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(is_object($user) && in_array("ROLE_ADMIN",$user->getRoles())){
            return $this->redirectToRoute('user_list');
        }
        else if(is_object($user) && in_array("ROLE_USER",$user->getRoles())){
            return $this->redirectToRoute('user_profile');
        }
        else{
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("user/profile", name="user_profile")
     * @Security("has_role('ROLE_USER')")
     */
    public function profile() {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('user/show.html.twig', array('user' => $user));
    }

    /**
     * @Route("/admin/users", name="user_list")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function user_list(Request $request) {

        //$users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT u FROM App:User u";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );

        return $this->render('user/index.html.twig', array('users' => $pagination));
    }

    /**
     * @Route("/admin/user/new", name="new_user")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newUser(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $roles = $this->getDoctrine()->getRepository(Role::class)->findAll();
        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', PasswordType::class, array('attr' => array('class' => 'form-control')))
            ->add('role', ChoiceType::class, array('choices' => $roles,
                'choice_label' => function($role, $key, $index) {
                    return $role->getTitle();
                },
                'preferred_choices' => function($role, $key, $index) {
                    return $role->getTitle() == 'User';
                },
                'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->render('common/new.html.twig', array(
            'form' => $form->createView(),
            'title' => "user",
        ));
    }

    /**
     * @Route("/admin/user/delete/{id}")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteUser($id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if($user){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/admin/user/edit/{id}", name="user_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $encoder, $id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $roles = $this->getDoctrine()->getRepository(Role::class)->findAll();
        $clubs = $this->getDoctrine()->getRepository(Club::class)->findAll();

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', PasswordType::class, array('attr' => array('class' => 'form-control', 'value' => '') , 'required' => false))
            ->add('role', ChoiceType::class, array('choices' => $roles,
                'choice_label' => function($role, $key, $index) {
                    return $role->getTitle();
                },
                'preferred_choices' => function($role, $key, $index) {
                    return $role->getTitle() == 'User';
                },
                'attr' => array('class' => 'form-control')))
            ->add('clubs', EntityType::class, array(
                'class' => Club::class,
                'choice_label' => function ($club) {
                    return $club->getTitle();
                },
                'attr' => array('class' => 'form-control'),
                'data' => $user->getClubs(),
                "label" => "Group",
                'required' => false,
                'choices' => $clubs,
                'multiple' => true,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            if($userData->getPassword() !== ""){
                $encoded = $encoder->encodePassword($userData, $userData->getPassword());
                $userData->setPassword($encoded);
            }
            else{
                $userData->setPassword($user->getPassword());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userData);
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->render('common/edit.html.twig', array(
            'form' => $form->createView(),
            'title' => "user",
        ));
    }

    /**
     * @Route("admin/user/{id}", name="user_show")
     */
    public function showUser($id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        return $this->render('user/show.html.twig', array('user' => $user));
    }

}