<?php

namespace VirtualMachineBundle\Manager;

interface ImageManagerInterface
{
    public function getAvailableImages(): array;
}