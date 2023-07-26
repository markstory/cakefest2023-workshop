<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Entity\User;
use App\Policy\SudoRequiredException;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\View\JsonView;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if ($this->request->getData('op') === 'sudo_activate') {
            $this->activateSudo();
        }
    }

    protected function activateSudo()
    {
        $password = $this->request->getData('password');
        $user = $this->Authentication->getIdentity()->getOriginalData();
        assert($user instanceof User, 'requires a user instance');
        if ($user->activateSudo($password)) {
            $users = $this->fetchTable('Users');
            $users->saveOrFail($user);

            $data = $this->request->getData();
            unset($data['op'], $data['password']);
            $this->request = $this->request->withParsedBody($data);

            Log::info('user.activate_sudo', ['id' => $user->id, 'scope' => 'audit']);
            return true;
        }

        $this->Flash->error('Sudo failed, password was incorrect');
        throw new SudoRequiredException();
    }

    public function viewClasses(): array
    {
        return [JsonView::class];
    }
}
