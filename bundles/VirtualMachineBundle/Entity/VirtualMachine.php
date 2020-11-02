<?php

namespace VirtualMachineBundle\Entity;

use App\Entity\User;
use App\Enum\VirtualMachineStatus;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class VirtualMachine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="virtualMachines")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $os;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $vcpus;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     */
    private $ram;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     */
    private $hdd;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $region;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ipV4Address;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ipV6Address;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isDeleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(string $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getVcpus(): ?int
    {
        return $this->vcpus;
    }

    public function setVcpus(int $vcpus): self
    {
        $this->vcpus = $vcpus;

        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(string $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getHdd(): ?string
    {
        return $this->hdd;
    }

    public function setHdd(string $hdd): self
    {
        $this->hdd = $hdd;

        return $this;
    }

    public function setStatus(VirtualMachineStatus $status): self
    {
        $this->status = $status->getValue();

        return $this;
    }

    public function getStatus(): VirtualMachineStatus
    {
        return new VirtualMachineStatus($this->status);
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->isDeleted = true;

        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        
        return $this;
    }

    public function getIpV4Address(): ?string
    {
        return $this->ipV4Address;
    }

    public function setIpV4Address(string $ip): self
    {
        $this->ipV4Address = $ip;
        
        return $this;
    }

    public function getIpV6Address(): ?string
    {
        return $this->ipV6Address;
    }

    public function setIpV6Address(string $ip): self
    {
        $this->ipV6Address = $ip;
        
        return $this;
    }
}
