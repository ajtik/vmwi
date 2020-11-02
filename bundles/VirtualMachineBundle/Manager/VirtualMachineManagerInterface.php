<?php

namespace VirtualMachineBundle\Manager;

use VirtualMachineBundle\Entity\VirtualMachine;

interface VirtualMachineManagerInterface
{
    public function create(string $name, string $sizeSlug, string $imageSlug, int $sshKeyId): VirtualMachine;

    public function checkStatus(VirtualMachine $virtualMachine): VirtualMachine;

    public function start(VirtualMachine $virtualMachine): void;

    public function stop(VirtualMachine $virtualMachine): void;

    public function restart(VirtualMachine $virtualMachine): void;

    public function delete(VirtualMachine $virtualMachine): void;
}