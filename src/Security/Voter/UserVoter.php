<?php declare(strict_types = 1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, ['USER_EDIT'])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'USER_EDIT':
                return $this->canEdit($subject, $user);

            break;
        }

        return false;
    }

    private function canEdit(User $userToEdit, User $actualUser)
    {
        return $userToEdit === $actualUser || in_array('ROLE_ADMIN', $actualUser->getRoles());
    }

}
