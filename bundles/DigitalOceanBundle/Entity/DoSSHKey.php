<?php declare(strict_types = 1);

namespace DigitalOceanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VirtualMachineBundle\Entity\SSHKey;

/**
 * @ORM\Entity()
 */
class DoSSHKey
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id = null;

    /**
     * @ORM\Column(type="integer")
     */
    private $doId;

    /**
     * @ORM\OneToOne(targetEntity="VirtualMachineBundle\Entity\SSHKey", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="sshkey_id", referencedColumnName="id")
     */
    private $sshKey;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDoId(int $doId): self
    {
        $this->doId = $doId;

        return $this;
    }

    public function getDoId(): int
    {
        return $this->doId;
    }

    public function setSshKey(SSHKey $sshKey): self
    {
        $this->sshKey = $sshKey;

        return $this;
    }

    public function getSshKey(): SSHKey
    {
        return $this->sshKey;
    }
}
