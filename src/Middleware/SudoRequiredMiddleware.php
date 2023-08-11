<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Model\Entity\User;
use App\Policy\SudoRequiredException;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * SudoRequired middleware
 */
class SudoRequiredMiddleware implements MiddlewareInterface
{
    use LocatorAwareTrait;

    /**
     * Process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        assert($request instanceof ServerRequest, 'Requires cake request');

        $password = $request->getData('password');
        $identity = $request->getAttribute('identity');
        if (!$identity || $request->getData('op') !== 'sudo_activate') {
            return $handler->handle($request);
        }
        $user = $identity->getOriginalData();

        assert($user instanceof User, 'User is required');
        if ($user->activateSudo($password)) {
            $users = $this->fetchTable('Users');
            $users->saveOrFail($user);

            $data = $request->getData();
            unset($data['op'], $data['password']);
            $request = $request->withParsedBody($data);

            Log::info('user.sudo_activate', ['id' => $user->id, 'scope' => 'sudo']);

            return $handler->handle($request);
        }

        $request->getFlash()->error('Sudo failed, password was incorrect.');
        Log::info('user.sudo_failed', ['id' => $user->id, 'scope' => 'sudo']);

        throw new SudoRequiredException();
    }
}
