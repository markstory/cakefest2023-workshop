<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users form content">
    <?= $this->Form->create() ?>
    <fieldset>
        <h2>Hi <?= h($email) ?>,</h2>
        <legend><?= __('Please enter your password') ?></legend>
        <?= $this->Form->hidden('email', ['value' => $email]) ?>
        <?= $this->Form->control('password') ?>
    </fieldset>
    <?= $this->Form->button(__('Login')); ?>
    <?= $this->Form->end() ?>
</div>
