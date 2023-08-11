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
async function completeLogin(loginData, email, csrfToken) {
    recursiveBase64ToArrayBuffer(loginData);
    const cred = await navigator.credentials.get(loginData);

    const requestData = {
        id: arrayBufferToBase64(cred.rawId),
        clientData: arrayBufferToBase64(cred.response.clientDataJSON),
        authenticator: arrayBufferToBase64(cred.response.authenticatorData),
        signature: arrayBufferToBase64(cred.response.signature),
        userHandle: arrayBufferToBase64(cred.response.userHandle),
        email: email,
    };
    const response = await sendRequest({
        url: '/users/login',
        method: 'POST',
        data: requestData,
        csrfToken: csrfToken,
    });
    if (response.ok) {
        window.location = '/users/view';
    } else {
        const messageEl = document.getElementById('login-flash');
        messageEl.innerText = 'Your Passkey authentication failed';
        messageEl.style.display = 'block';
    }
}

completeLogin(
    <?= json_encode($loginData->loginData); ?>,
    <?= json_encode($email) ?>,
    '<?= $this->request->getAttribute('csrfToken') ?>'
);
</script>
<?php endif ?>


