<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\Cake\Datasource\EntityInterface> $passkeys
 */
?>
<div class="passkeys index content">
    <div style="display: flex; gap: 8px; flex-direction: row-reverse;">
        <?= $this->Html->link(__('New Passkey'), ['action' => 'startRegister'], ['class' => 'button float-right']) ?>
        <?= $this->Html->link(__('View Profile'), ['_path' => 'Users::view'], ['class' => 'button float-right']) ?>
    </div>
    <h3><?= __('Passkeys') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('display_name') ?></th>
                    <th><?= $this->Paginator->sort('for_login') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passkeys as $passkey): ?>
                <tr>
                    <td><?= h($passkey->display_name) ?></td>
                    <td><?= h($passkey->for_login) ?></td>
                    <td class="actions">
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', '_method' => 'POST', $passkey->id], ['action' => 'delete', 'confirm' => __('Are you sure you want to delete # {0}?', $passkey->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
