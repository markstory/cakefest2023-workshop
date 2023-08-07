<?php
declare(strict_types=1);

namespace App\Webauthn\Policy;

use Authorization\IdentityInterface;
use Cake\ORM\Query\SelectQuery;

/**
 * Passkeys policy
 */
class PasskeysTablePolicy
{
    /**
     * @param \Authorization\IdentityInterface $user
     * @param \Cake\ORM\Query $query
     */
    public function scopeIndex(IdentityInterface $user, SelectQuery $query): SelectQuery
    {
        return $query->where(['Passkeys.user_id' => $user->id]);
    }
}
