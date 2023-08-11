<?php
declare(strict_types=1);

namespace App\Webauthn\Controller;

use App\Controller\AppController;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Utility\Text;
use Cake\View\JsonView;
use lbuchs\WebAuthn\WebAuthnException;

use function Cake\Routing\url;

/**
 * Passkeys Controller
 *
 * @property \App\Model\Table\PasskeysTable $Passkeys
 */
class PasskeysController extends AppController
{
    protected function getPasskey($id)
    {
        $passkey = $this->Passkeys->get($id);
        $this->Authorization->authorize($passkey);

        return $passkey;
    }

    public function index()
    {
        $query = $this->Authorization->applyScope($this->Passkeys->find());
        $passkeys = $this->paginate($query);
        $this->set('passkeys', $passkeys);
    }

    /**
     * Delete method
     *
     * @param string|null $id Passkey id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $passkey = $this->getPasskey($id);
        if ($this->Passkeys->delete($passkey)) {
            $this->Flash->success(__('The passkey has been deleted.'));
        } else {
            $this->Flash->error(__('The passkey could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Passkeys', 'action' => 'index']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Passkey id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $passkey = $this->getPasskey($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $passkey = $this->Passkeys->patchEntity($passkey, $this->request->getData());
            if ($this->Passkeys->save($passkey)) {
                $this->Flash->success(__('The passkey has been saved.'));

                return $this->redirect(['controller' => 'Passkeys', 'action' => 'index']);
            }
            $this->Flash->error(__('The passkey could not be saved. Please, try again.'));
        }
        $this->set(compact('passkey'));
    }

    public function startRegister()
    {
        $this->Authorization->skipAuthorization();

        /** @var \Authentication\AuthenticationService $authService */
        $authService = $this->Authentication->getAuthenticationService();
        $webauth = $authService->authenticators()->get('Webauthn');
        $user = $authService->getIdentity()->getOriginalData();

        $registerData = $webauth->getRegistrationData(
            $user->uuid,
            $user->email,
            $user->name
        );
        // Store registration data in the session so we can use
        // it once the user has completed their u2f prompt.
        $this->request->getSession()->write('Registration', [
            'user' => $user,
            'challenge' => $registerData->challenge,
        ]);
        $this->set('registerData', $registerData);
        $this->set('user', $user);
        $this->render('register');
    }

    public function completeRegister(): void
    {

        $request = $this->request;
        $request->allowMethod('POST');

        $session = $request->getSession();

        /** @var \Authentication\AuthenticationService $authService */
        $authService = $this->Authentication->getAuthenticationService();
        $webauth = $authService->authenticators()->get('Webauthn');

        $this->viewBuilder()
            ->setClassName(JsonView::class)
            ->setOption('serialize', ['success', 'message', 'redirect']);

        try {
            $challenge = $session->read('Registration.challenge');
            $processData = $webauth->validateRegistration(
                $request,
                $challenge,
            );
        } catch (WebAuthnException $error) {
            $this->set('success', false);
            $this->set('message', $error->getMessage());

            return;
        }

        $user = $authService->getIdentity()->getOriginalData();
        try {
            $this->Passkeys->getConnection()->transactional(function () use ($user, $processData): void {
                $alias = $this->request->getData('display_name');
                $passkey = $this->Passkeys->createFromData($processData, $alias);

                $this->Authorization->authorize($passkey, 'add');

                $passkey->user_id = $user->id;
                $passkey->for_login = (bool) $this->request->getData('for_login');
                $this->Passkeys->saveOrFail($passkey);
            });

            $this->Flash->success('Passkey added');
            $this->set('success', true);
            $this->set('redirect', url(['_path' => 'App/Webauthn.Passkeys::index']));
        } catch (PersistenceFailedException $error) {
            $this->set('success', false);
            $this->set('message', $error->getMessage());
        }
    }
}
