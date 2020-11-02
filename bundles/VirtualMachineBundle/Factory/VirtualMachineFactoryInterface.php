<?php

namespace VirtualMachineBundle\Factory;

use VirtualMachineBundle\Entity\VirtualMachine;

interface VirtualMachineFactoryInterface
{
    public function create(): VirtualMachine;
}