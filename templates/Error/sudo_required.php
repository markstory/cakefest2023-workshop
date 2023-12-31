<?php
declare(strict_types=1);

use Cake\Utility\Hash;
?>
<div class="sudo-required form content">
    <?= $this->Form->create() ?>
    <?= $this->Form->hidden('op', ['value' => 'sudo_activate']) ?>
    <?php foreach (Hash::flatten($this->request->getData()) as $key => $value): ?>
        <?php
        if ($key === 'op' || $key === 'password') {
            continue;
        }
        ?>
        <?= $this->Form->hidden($key, ['value' => $value]) ?>
    <?php endforeach; ?>
    <fieldset>
        <legend><?= __('Please enter your password to continue') ?></legend>
        <?= $this->Form->control('password') ?>
    </fieldset>
    <?= $this->Form->button(__('Proceed')); ?>
    <?= $this->Form->end() ?>
</div>
