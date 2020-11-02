<?php

namespace VirtualMachineBundle\Manager;

use VirtualMachineBundle\Entity\SSHKey;

interface SSHKeyManagerInterface
{
    public function create(SSHKey $sshKey): SSHKey;

    public function delete(SSHKey $sshKey): void;
}