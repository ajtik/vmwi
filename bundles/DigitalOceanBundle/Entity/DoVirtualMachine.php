<?php

namespace DigitalOceanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use VirtualMachineBundle\Entity\VirtualMachine;

/**
 * @ORM\Entity()
 */
class DoVirtualMachine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $doId;

    /**
     * @ORM\OneToOne(targetEntity="VirtualMachineBundle\Entity\VirtualMachine", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="virtual_machine_id", referencedColumnName="id")
     */
    private $virtualMachine;

    /**
     * @ORM\ManyToMany(targetEntity="DoSSHKey", cascade={"persist"})
     * @ORM\JoinTable(name="dovirtualmachines_x_dosshkeys",
     *     joinColumns={@ORM\JoinColumn(name="dovirtualmachine_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="dosshkey_id", referencedColumnName="id")}
     * )
     */
    private $sshKeys;

    public function __construct()
    {
        $this->sshKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDoId(string $doId): self
    {
        $this->doId = $doId;

        return $this;
    }

    public function getDoId(): string
    {
        return $this->doId;
    }

    public function setVirtualMachine(VirtualMachine $virtualMachine): self
    {
        $this->virtualMachine = $virtualMachine;

        return $this;
    }

    public function getVirtualMachine(): VirtualMachine
    {
        return $this->virtualMachine;
    }

    public function addSshKey(DoSSHKey $doSshKey): self
    {
        $this->sshKeys->add($doSshKey);

        return $this;
    }

    public function removeSshKey(DoSSHKey $doSshKey): self
    {
        $this->sshKeys->removeElement($doSshKey);

        return $this;
    }
}