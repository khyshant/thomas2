<?php

namespace App\Tests;


use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskTest extends WebTestCase
{
    public function testSuccesscreateTask()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('anth.blanchard@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request(Request::METHOD_GET, "/tasks/create");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=task]')->form([
            "task[title]" => "tache créé par phpunit",
            "task[description]" => "description de tache"
        ]);
        $csrfToken = $form->get("task[_token]")->getValue();
        $client->submit($form);
        //TODO voir avec thomas pourquoi ici cela ne marche pas
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        //TODO voir avec thomas pourquoi ici cela ne marche pas
        $this->assertEquals('task_create',$client->getRequest()->attributes->get('_route'));
    }
    public function testSuccessUpdateTask()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('anth.blanchard@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request(Request::METHOD_GET, "/tasks/update/2");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=task]')->form([
            "task[title]" => "tache mise a jour phpunit",
            "task[description]" => "description de tache mise a jour"
        ]);
        $csrfToken = $form->get("task[_token]")->getValue();
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        //TODO voir avec thomas pourquoi ici cela ne marche pas
        $this->assertEquals('task_update',$client->getRequest()->attributes->get('_route'));
        // vérifie que la taches est bien modifiée
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskExist = $taskRepository->findOneById(2);

        $this->assertTrue($taskExist->getTitle() == "tache mise a jour phpunit");
    }

    public function testSuccessValid()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user

        $testUser = $userRepository->findOneByEmail('anth.blanchard@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request(Request::METHOD_GET, "/tasks/valid/2");
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskExist = $taskRepository->findOneById(2);

        $this->assertTrue($taskExist->getDone() == 1);
        $crawler = $client->request(Request::METHOD_GET, "/tasks/");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessdelete()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('anth.blanchard@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request(Request::METHOD_GET, "/tasks/delete/1");
        //test si l'entité n'exist plus
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskExist = $taskRepository->findOneById(1);
        $this->assertNull($taskExist);
        $crawler = $client->request(Request::METHOD_GET, "/tasks/");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


}
