<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use VirtualMachineBundle\Entity\SSHKey;

class SSHKeyVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, ['KEY_ADD', 'KEY_EDIT', 'KEY_DELETE'])) {
            return false;
        }

        if (!$subject instanceof SSHKey) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case 'KEY_EDIT':
                return $this->canEdit($subject, $user);
            break;
            case 'KEY_DELETE':
                return $this->canDelete($subject, $user);
            break;
        }

        return false;
    }

    private function canEdit(SSHKey $sshKey, User $user)
    {
        return in_array($sshKey, $user->getSSHKeys()) || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(SSHKey $sshKey, User $user)
    {
        return in_array($sshKey, $user->getSSHKeys()) || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
