<?php

namespace App\Controller;

use App\Entity\Task;
use App\Handler\TaskHandler;
use App\Security\voter\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class TaskController
 * @package App\Controller
 * @Route("/tasks")
 */
class TaskController extends AbstractController
{


    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var SessionInterface */
    protected $session;

    public function __construct(UrlGeneratorInterface $urlGenerator, SessionInterface $session ) {
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    /**
     * @Route("/", name="listing_tasks")
     * @param Request $request
     */
    public function listTask(Request $request)
    {
        $user = $this->getUser();
        if($user->getRoles()[0] == "ROLE_ADMIN"){
            return $this->render('task/listing.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findAll()]);
        }
        return $this->render('task/listing.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findBY(['user'=>$user->getId()])]);
    }

    /**
     * @Route("/create", name="task_create")
     * @param Request $request
     * @param TaskHandler $handler
     * @return Response
     */
    public function create(Request $request,TaskHandler $handler): Response
    {
        $user = $this->getUser();
        $task = new Task();
        $task->setUser($user);
        if($handler->handle($request, $task, ["validation_groups" => ["Default", "add"]        ]
        )) {
            $this->addFlash('success', "tache ajoutée");
            $this->session->getFlashBag()->add('success', "tache ajoutée");
            return $this->redirectToRoute("task_update",array('id' => $task->getId()));
        }
        return $this->render("task/create.html.twig", [
            "form" => $handler->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="task_update", requirements={"id":"\d+"})
     * @param Request $request
     * @param Task $task
     * @param TaskHandler $handler
     * @return Response
     */
    public function update(Request $request, Task $task,TaskHandler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);
        if($handler->handle($request, $task)) {
            $this->session->getFlashBag()->add('success', "tâche mise à jour");
            $this->addFlash('success', "tâche mise à jour");
            return $this->redirectToRoute("task_update",array('id' => $task->getId()));
        }
        return $this->render("task/update.html.twig", [
            "form" => $handler->createView()
        ]);
    }


    /**
     * @Route("/valid/{id}", name="task_valid" , requirements={"id":"\d+"})
     * @param Task $task
     * @param $entityManager
     * @return RedirectResponse
     */
    public function valid(Task $task,EntityManagerInterface $entityManager): RedirectResponse
    {
        $task->setDone(true);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "tâche validée");
        return $this->redirectToRoute("listing_tasks");

    }

    /**
     * @Route("/delete/{id}", name="task_delete" , requirements={"id":"\d+"})
     * @param Task $task
     * @return RedirectResponse
     */
    public function delete(Task $task): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);
        $this->addFlash('success', "tâche supprimée");
        $this->getDoctrine()->getManager()->remove($task);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("listing_tasks");
    }
}