<?php declare(strict_types = 1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use VirtualMachineBundle\Entity\SSHKey;
use VirtualMachineBundle\Entity\VirtualMachine;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"email"}, message="user.unique.email")
 */
class User implements UserInterface
{

    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email = null;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isActive = false;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var ArrayCollection<VirtualMachine>
     * @ORM\OneToMany(targetEntity="VirtualMachineBundle\Entity\VirtualMachine", mappedBy="user")
     */
    private $virtualMachines;

    /**
     * @var ArrayCollection<SSHKey>
     * @ORM\ManyToMany(targetEntity="VirtualMachineBundle\Entity\SSHKey")
     * @ORM\JoinTable(name="users_x_sshkeys",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sshkey_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $sshKeys;

    public function __construct()
    {
        $this->virtualMachines = new ArrayCollection();
        $this->sshKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isActive(): bool
    {
        return (bool) $this->isActive;
    }

    public function setActive(bool $active): self
    {
        $this->isActive = $active;

        return $this;
    }

    public function getVirtualMachines(): ArrayCollection
    {
        return $this->virtualMachines;
    }

    public function addVirtualMachine(VirtualMachine $virtualMachine): self
    {
        $this->virtualMachines->add($virtualMachine);

        return $this;
    }

    public function removeVirtualMachine(int $virtualMachineId): self
    {
        $this->virtualMachines->remove($virtualMachineId);

        return $this;
    }

    /**
     * @return array<SSHKey>
     */
    public function getSSHKeys(): array
    {
        return $this->sshKeys->toArray();
    }

    public function addSSHKey(SSHKey $virtualMachine): self
    {
        $this->sshKeys->add($virtualMachine);

        return $this;
    }

    public function removeSSHKey(SSHKey $sshKey): self
    {
        $this->sshKeys->removeElement($sshKey);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
