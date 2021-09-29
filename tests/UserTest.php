<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseRedirects('/login','302');
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1','Please sign in');
        $form = $crawler->filter('form[name=login_form]')->form([
            "email" => "anth.blanchard@gmail.com",
            "password" => "test",
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertEquals('homepage',$client->getRequest()->attributes->get('_route'));
    }

    public function testBadLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1','Please sign in');
        $form = $crawler->filter('form[name=login_form]')->form([
            "email" => "error@gmail.com",
            "password" => "test",
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();
        $this->assertEquals('app_login',$client->getRequest()->attributes->get('_route'));
        $this->assertStringContainsString('Invalid credentials.',$crawler->filter('div.alert-danger')->text());
    }

    public function testBadpassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1','Please sign in');
        $form = $crawler->filter('form[name=login_form]')->form([
            "email" => "anth.blanchard@gmail.com",
            "password" => "error",
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();
        $this->assertEquals('app_login',$client->getRequest()->attributes->get('_route'));
        $this->assertStringContainsString('Invalid credentials.',$crawler->filter('div.alert-danger')->text());
    }
    public function testSuccesscreatUser()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('anth.blanchard@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request(Request::METHOD_GET, "/users/create");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=user]')->form([
            "user[email]" => "a.a@gmail.com",
            "user[username]" => "created",
            "user[password][first]" => "test",
            "user[password][second]" => "test",
            "user[roles][0]" => "ROLE_USER"
        ]);
        $csrfToken = $form->get("user[_token]")->getValue();
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        //TODO voir avec thomas pourquoi ici cela ne marche pas
        $this->assertEquals('user_create',$client->getRequest()->attributes->get('_route'));

        // vérifie que la taches est bien modifiée
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userExist = $userRepository->findOneById(16);

        $this->assertNotNull($userExist);
    }

    public function testUpdateUser()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('anth.blanchard@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request(Request::METHOD_GET, "users/update/2");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=user]')->form([
            "user[email]" => "2mail@test.fr",
            "user[username]" => "user2-bis",
            "user[password][first]" => "test",
            "user[password][second]" => "test",
            "user[roles][0]" => "ROLE_USER"
        ]);
        $csrfToken = $form->get("user[_token]")->getValue();
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        // TODO that
        $this->assertEquals('user_update',$client->getRequest()->attributes->get('_route'));

        // vérifie que la taches est bien modifiée
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userExist = $userRepository->findOneById(2);

        $this->assertTrue($userExist->getUsername() == "user2-bis");
    }
}
