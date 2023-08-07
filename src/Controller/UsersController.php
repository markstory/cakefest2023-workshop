<?php
declare(strict_types=1);

namespace App\Controller;

use App\Webauthn\Model\LoginChallenge;
use Authentication\Authenticator\Result;
use Cake\Routing\Router;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
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

            return $this->redirect($target);
        }
        if ($this->request->is('post')) {
            // Could be UI flow that is username -> get user
            // determine login type, show passwords/u2f instead.
            $useWebauth = false;
            if ($result->getStatus() === Result::FAILURE_CREDENTIALS_MISSING) {
                $loginData = $result->getData();
                if ($loginData instanceof LoginChallenge) {
                    $useWebauth = true;
                    $this->request->getSession()->write('Webauthn.challenge', $loginData->challenge);
                    $this->set('loginData', $loginData);
                    $this->viewBuilder()->setTemplate('login_u2f');
                }
            }
            if (!$useWebauth) {
                $this->Flash->error('Invalid username or password');
            }
        }
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
        if ($id === null) {
            $id = $this->Authentication->getIdentity()->id;
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
