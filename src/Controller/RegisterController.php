<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER'])
            ->setCreatedAt(new \DateTime());
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //TO DO vérif véracité des données
            if ($form->get('password')->getData() == $form->get('confirmPassword')->getData()) {
                $pass = $form->get('password')->getData();
                $user->setPassword($encoder->encodePassword($user, $pass));
                $manager->persist($user);
                $manager->flush();
            } else {
                $this->addFlash('danger','Les mot de passe sont différents');
                return $this->redirect($request->headers->get('referer'));
            }

        }


        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'current_menu' => 'register',
            'registerForm' => $form->createView()
        ]);
    }
}
