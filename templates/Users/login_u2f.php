<?php
declare(strict_types=1);
/**
 * @var \App\Model\LoginChallenge $loginData
 * @var \App\Model\Entity\User|null $user
 */
?>
<div id="login-flash" class="message" style="display:none;">
</div>
<h1>Activate your Passkey</h1>
<p><?= $this->Html->link('Go back', ['plugin' => null, 'controller' => 'Users', 'action' => 'login']) ?></p>
<?php if (isset($loginData)): ?>
<?= $this->element('App/Webauthn.webauthn-utils'); ?>
<script type="text/javascript">
async function completeLogin(loginData, csrfToken) {
    recursiveBase64ToArrayBuffer(loginData);
    const cred = await navigator.credentials.get(loginData);

    const requestData = {
        id: arrayBufferToBase64(cred.rawId),
        clientData: arrayBufferToBase64(cred.response.clientDataJSON),
        authenticator: arrayBufferToBase64(cred.response.authenticatorData),
        signature: arrayBufferToBase64(cred.response.signature),
        userHandle: arrayBufferToBase64(cred.response.userHandle),
        username: document.querySelector('#username').value,
    };
    const response = await sendRequest({
        url: '/users/login',
        method: 'POST',
        data: requestData,
        csrfToken: csrfToken,
    });
    if (response.redirected) {
        window.location = '/users/view';
    } else {
        const messageEl = document.getElementById('login-flash');
        messageEl.innerText = "Login failed",
        messageEl.style.display = 'block';
    }
}

completeLogin(<?= json_encode($loginData->loginData); ?>, '<?= $this->request->getAttribute('csrfToken') ?>');
</script>
<?php endif ?>


