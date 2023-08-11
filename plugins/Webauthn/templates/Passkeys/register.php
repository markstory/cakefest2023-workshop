<?php
/**
 * @var \App\Webauthn\Model\RegistrationData|null $registerData;
 * @property \Cake\View\Helper\FormHelper $Form
 */

use function Cake\Routing\url;

?>
<div id="register-flash" class="message" style="display:none;">
</div>
<h1>Register a new Passkey</h1>
<?php
echo $this->Form->create(null, [
    'method' => 'post',
    'url' => url(['_path' => 'App/Webauthn.Passkeys::completeRegister', '_method' => 'post']),
    'onsubmit' => 'doComplete(event)'
]);
echo $this->Form->control('display_name');
echo $this->Form->control('for_login', [
    'type' => 'checkbox',
    'hiddenField' => false,
    'label' => 'Use key for login instead of password'
]);
echo $this->Form->submit('Add U2F device');
echo $this->Form->end();
?>
<?php if (isset($registerData)): ?>
<?= $this->element('webauthn-utils'); ?>
<script type="text/javascript">
async function completeRegistration(registerData, displayName, forLogin, csrfToken) {
    recursiveBase64ToArrayBuffer(registerData);

    const cred = await navigator.credentials.create(registerData);
    const attestationResponse = {
        clientData: arrayBufferToBase64(cred.response.clientDataJSON),
        attestation: arrayBufferToBase64(cred.response.attestationObject),
        display_name: displayName,
        for_login: forLogin,
    };

    const response = await sendRequest({
        url: '/webauthn/passkeys/add/complete',
        method: 'POST',
        data: attestationResponse,
        csrfToken: csrfToken,
    });
    const responseData = await response.json();
    if (responseData.redirect) {
        window.location = responseData.redirect;
    }
    if (!responseData.success && responseData.message) {
        var flash = document.getElementById('register-flash');
        flash.innerText = responseData.message;
    }
}

function doComplete(event) {
    event.preventDefault();
    event.stopPropagation();
    var form = event.target;
    var displayName = form.elements.display_name.value;
    var forLogin = form.elements.for_login.checked;

    completeRegistration(
        <?= json_encode($registerData->registration); ?>,
        displayName,
        forLogin,
        '<?= $this->request->getAttribute('csrfToken') ?>',
    );
}
</script>
<?php endif; ?>
