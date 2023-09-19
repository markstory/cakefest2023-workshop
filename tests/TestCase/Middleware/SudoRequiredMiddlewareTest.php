<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use App\Middleware\SudoRequiredMiddleware;
use App\Model\Entity\User;
use App\Policy\SudoRequiredException;
use Authentication\Identity as AuthenticationIdentity;
use Authorization\AuthorizationService;
use Authorization\Identity as AuthorizationIdentity;
use Authorization\Policy\OrmResolver;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * App\Middleware\SudoRequiredMiddleware Test Case
 */
class SudoRequiredMiddlewareTest extends TestCase
{
    use IntegrationTestTrait;

    public array $fixtures = ['app.Users'];

    protected function createUser(): User
    {
        $users = $this->fetchTable('Users');
        $user = $users->newEntity([
            'email' => 'frog@example.com',
            'name' => 'Frog',
        ]);
        $user->password = 'cakefest2023';
        $user->uuid = Text::uuid();
        $user = $users->saveOrFail($user);

        return $user;
    }

    protected function createIdentity(): AuthorizationIdentity
    {
        $authorizationService = new AuthorizationService(new OrmResolver());

        return new AuthorizationIdentity(
            $authorizationService,
            new AuthenticationIdentity($this->createUser())
        );
    }

    public function testProcessGetNoUser(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $handler = new TestRequestHandler(function () {
            return new Response(['body' => 'ok']);
        });

        $middleware = new SudoRequiredMiddleware();
        $response = $middleware->process($request, $handler);
        $this->assertEquals('ok', (string)$response->getBody());
    }

    public function testProcessPostNoUser(): void
    {
        // No user, no challenge.
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
            'data' => ['op' => 'sudo_activate'],
        ]);
        $handler = new TestRequestHandler(function () {
            return new Response(['body' => 'ok']);
        });

        $middleware = new SudoRequiredMiddleware();
        $response = $middleware->process($request, $handler);
        $this->assertEquals('ok', (string)$response->getBody());
    }

    public function testProcessPostFailure(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
            'post' => [
                'op' => 'sudo_activate',
                'password' => 'wrong',
            ],
        ]);
        $request = $request->withAttribute('identity', $this->createIdentity());

        $handler = new TestRequestHandler(function () {
            return new Response(['body' => 'ok']);
        });

        $middleware = new SudoRequiredMiddleware();
        $this->expectException(SudoRequiredException::class);
        $middleware->process($request, $handler);
    }

    public function testProcessPostSuccess(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
            'post' => [
                'op' => 'sudo_activate',
                'password' => 'cakefest2023',
            ],
        ]);
        $request = $request->withAttribute('identity', $this->createIdentity());

        $handler = new TestRequestHandler(function () {
            return new Response(['body' => 'ok']);
        });

        $middleware = new SudoRequiredMiddleware();
        $response = $middleware->process($request, $handler);
        $this->assertEquals('ok', (string)$response->getBody());
    }
}
