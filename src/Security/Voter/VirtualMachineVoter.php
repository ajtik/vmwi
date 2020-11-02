<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use VirtualMachineBundle\Entity\VirtualMachine;

class VirtualMachineVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, ['MACHINE_START', 'MACHINE_RESTART', 'MACHINE_STOP', 'MACHINE_DELETE'])) {
            return false;
        }

        if (!$subject instanceof VirtualMachine) {
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
            case 'MACHINE_START':
                return $this->canStart($subject, $user);
            break;
            case 'MACHINE_STOP':
                return $this->canStop($subject, $user);
            break;
            case 'MACHINE_RESTART':
                return $this->canRestart($subject, $user);
            break;
            case 'MACHINE_DELETE':
                return $this->canDelete($subject, $user);
            break;
        }

        return false;
    }

    private function canStart(VirtualMachine $virtualMachine, User $user)
    {
        return $virtualMachine->getUser() === $user;
    }

    private function canStop(VirtualMachine $virtualMachine, User $user)
    {
        return $virtualMachine->getUser() === $user;
    }

    private function canRestart(VirtualMachine $virtualMachine, User $user)
    {
        return $virtualMachine->getUser() === $user;
    }

    private function canDelete(VirtualMachine $virtualMachine, User $user)
    {
        return $virtualMachine->getUser() === $user;
    }
}
