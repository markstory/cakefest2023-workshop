<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users form content">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Please enter your email') ?></legend>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->password('password', ['style' => 'visibility: hidden;']) ?>
    </fieldset>
    <?= $this->Form->button(__('Continue')); ?>
    <?= $this->Form->end() ?>
</div>
