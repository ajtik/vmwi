<?php

namespace VirtualMachineBundle\Manager;

interface SizeManagerInterface
{
    public function getAvailableSizes(): array;
}