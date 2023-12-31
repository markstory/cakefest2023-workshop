<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\UsersController;
use App\Model\Entity\User;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\UsersController Test Case
 *
 * @uses \App\Controller\UsersController
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Articles',
    ];

    protected function login(User $user)
    {
        $this->session([
            'Auth' => $user,
        ]);
    }

    protected function createUser(string $name, string $email) {
        $users = $this->fetchTable('Users');
        $user = $users->newEntity([
            'name' => $name,
            'email' => $email,
        ]);
        $user->password = 'cakefest2023';
        $users->saveOrFail($user);

        return $user;
    }

    public function testEditGet(): void
    {
        $user = $this->createUser('Mark', 'mark@example.com');
        $this->login($user);
        $this->enableCsrfToken();
        $this->get("/users/edit/{$user->id}");

        $this->assertResponseOk();
        $this->assertResponseContains($user->name);
        $this->assertResponseContains($user->email);
    }

    public function testEditPostRequiresSudo(): void
    {
        $user = $this->createUser('Mark', 'mark@example.com');
        $this->login($user);
        $this->enableCsrfToken();
        $this->post("/users/edit/{$user->id}", [
            'name' => 'Markus',
        ]);

        $this->assertResponseCode(403);
        $this->assertResponseContains('sudo-required');
    }

    public function testEditPostWithSudoFailure(): void
    {
        $user = $this->createUser('Mark', 'mark@example.com');
        $this->login($user);
        $this->enableCsrfToken();
        $this->post("/users/edit/{$user->id}", [
            'op' => 'sudo_activate',
            'password' => 'wrong',
            'name' => 'Markus',
        ]);

        $this->assertResponseCode(403);
    }

    public function testEditPostWithSudoSuccess(): void
    {
        $user = $this->createUser('Mark', 'mark@example.com');
        $this->login($user);
        $this->enableCsrfToken();
        $this->post("/users/edit/{$user->id}", [
            'op' => 'sudo_activate',
            'password' => 'cakefest2023',
            'name' => 'Markus',
        ]);

        $this->assertRedirect("/users/edit/{$user->id}");
    }
}
