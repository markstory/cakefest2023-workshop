<?php
declare(strict_types=1);

namespace App\Controller;

use App\Webauthn\Model\LoginChallenge;
use Authentication\Authenticator\Result;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\View\JsonView;

use function Cake\Collection\collection;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['view', 'login']);
    }

    public function login()
    {
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        // Login was successful, or we are already logged in.
        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? Router::url(['action' => 'view']);

            if ($this->request->is('json')) {
                $this->set('redirect', $target);
                $this->viewBuilder()->setOption('serialize', ['redirect']);

                return;
            }

            return $this->redirect($target);
        }

        $template = 'login_start';
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $this->set('email', $email);

            $user = null;
            if ($email) {
                $user = $this->Users->find('login')
                    ->where(['Users.email' => $email])
                    ->first();
            }

            // We should use passkey for login if the user has one configured.
            $useWebauth = false;
            if ($user) {
                $useWebauth = collection($user->passkeys)->some(fn ($item) => $item->for_login);
            }

            if ($result->getStatus() === Result::FAILURE_CREDENTIALS_MISSING) {
                $loginData = $result->getData();
                if ($loginData instanceof LoginChallenge) {
                    $this->request->getSession()->write('Webauthn.challenge', $loginData->challenge);
                    $this->set('loginData', $loginData);
                }
            }
            // TODO handle invalid U2F validation.

            if ($useWebauth) {
                $template = 'login_u2f';
            } else {
                $template = 'login_password';
            }

            if (!$useWebauth && $this->request->getData('password')) {
                $this->Flash->error('Invalid email or password');
            }
        }

        $builder = $this->viewBuilder();
        if ($this->request->is('json')) {
            $builder->setClassName(JsonView::class);
        }

        $builder
            ->setTemplate($template)
            ->setOption('serialize', ['success', 'redirect', 'message']);
    }

    public function logout()
    {
        $this->Authorization->skipAuthorization();
        $this->Authentication->logout();

        return $this->redirect(['action' => 'login']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Authorization->skipAuthorization();
        $identity = $this->Authentication->getIdentity();
        if ($id === null && $identity) {
            $id = $identity->getIdentifier();
        }
        if ($id === null) {
            throw new NotFoundException();
        }

        $user = $this->Users->get($id, [
            'contain' => ['Articles'],
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->Authorization->skipAuthorization();

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id);
        $action = $this->request->is('get') ? 'edit' : 'editWrite';
        $this->Authorization->authorize($user, $action);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'edit', $user->id]);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }
}
