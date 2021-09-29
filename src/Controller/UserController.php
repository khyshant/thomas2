<?php

namespace App\Controller;

use App\Entity\User;
use App\Handler\UserHandler;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="listing_users")
     */
    public function listUsers()
    {
        return $this->render('user/listing.html.twig', ['users' => $this->getDoctrine()->getRepository('App:User')->findAll()]);
    }


    /**
     * @Route("/create", name="user_create")
     * @param Request $request
     * @param UserHandler $handler
     * @return Response
     */
    public function create(Request $request,UserHandler $handler): Response
    {
        $user = new User();
        if($handler->handle($request, $user, ["validation_groups" => ["Default", "add"]])) {
            $this->addFlash('success', "utilisateur créé");
            return $this->redirectToRoute("user_update",array('id' => $user->getId()));
        }
        return $this->render("user/create.html.twig", [
            "form" => $handler->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="user_update")
     * @param Request $request
     * @param User $user
     * @param UserHandler $handler
     * @return Response
     */
    public function update(Request $request, User $user,UserHandler $handler): Response
    {
        //TODO faire une sortie
        //$this->denyAccessUnlessGranted(UserVoter::EDIT, $user);
        if($handler->handle($request, $user)) {
            $this->addFlash('success', "utilisateur mis a jour");
            return $this->redirectToRoute("listing_users");
        }
        return $this->render("user/update.html.twig", [
            "form" => $handler->createView()
        ]);
    }
}