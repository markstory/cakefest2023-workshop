<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;
use Cake\I18n\DateTime;

/**
 * User policy
 */
class UserPolicy
{
    /**
     * Requires the user to have an active sudo time window
     */
    protected function requireSudo(IdentityInterface $user): void
    {
        if (!$user->sudo_until || $user->sudo_until < DateTime::now()) {
            throw new SudoRequiredException();
        }
    }

    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canAdd(IdentityInterface $user, User $resource)
    {
        return true;
    }

    /**
     * Check if $user can view the edit User flow
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canEdit(IdentityInterface $user, User $resource)
    {
        // Can view oneself
        return $user->id == $resource->id;
    }

    /**
     * Check if $user can update another user
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canEditWrite(IdentityInterface $user, User $resource)
    {
        $this->requireSudo($user);

        // Can update oneself
        return $user->id == $resource->id;
    }

    /**
     * Check if $user can delete User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canDelete(IdentityInterface $user, User $resource)
    {
        return false;
    }

    /**
     * Check if $user can view User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canView(IdentityInterface $user, User $resource)
    {
        return true;
    }
}
