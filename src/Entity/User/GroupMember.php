<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\User\GroupMemberRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=GroupMemberRepository::class)
 */
class GroupMember
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="groupMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="groupMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $joinDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getGroupId(): ?Group
    {
        return $this->groupId;
    }

    public function setGroupId(?Group $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getJoinDate(): ?DateTimeImmutable
    {
        return $this->joinDate;
    }

    public function setJoinDate(DateTimeImmutable $joinDate): self
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
