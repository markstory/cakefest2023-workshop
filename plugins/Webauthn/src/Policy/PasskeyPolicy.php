<?php
declare(strict_types=1);

namespace App\Webauthn\Policy;

use App\Webauthn\Model\Entity\Passkey;
use Authorization\IdentityInterface;

/**
 * Passkey policy
 */
class PasskeyPolicy
{
    /**
     * Check if $user can add Passkey
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Webauthn\Model\Entity\Passkey $passkey
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Passkey $passkey)
    {
        return true;
    }

    /**
     * Check if $user can edit Passkey
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Webauthn\Model\Entity\Passkey $passkey
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Passkey $passkey)
    {
        return $passkey->user_id == $user->id;
    }

    /**
     * Check if $user can delete Passkey
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Webauthn\Model\Entity\Passkey $passkey
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Passkey $passkey)
    {
        return $passkey->user_id == $user->id;
    }

    /**
     * Check if $user can view Passkey
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Webauthn\Model\Entity\Passkey $passkey
     * @return bool
     */
    public function canView(IdentityInterface $user, Passkey $passkey)
    {
        return $passkey->user_id == $user->id;
    }
}
