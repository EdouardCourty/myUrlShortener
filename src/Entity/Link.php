<?php

namespace App\Entity;

use App\Entity\Utils\TimestampTrait;
use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ExclusionPolicy(ExclusionPolicy::ALL)]
#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[UniqueEntity(
    fields: [
        'customShortcode'
    ],
    message: 'A link with this shortcode already exists.'
)]
class Link
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[Expose]
    #[Groups([
        'apiGetLink'
    ])]
    #[ORM\Column(type: 'text')]
    private string $url;

    #[Expose]
    #[Groups([
        'apiGetLink'
    ])]
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    private ?string $customShortcode = null;

    #[Expose]
    #[Groups([
        'apiGetLink'
    ])]
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $usageCount = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCustomShortcode(): ?string
    {
        return $this->customShortcode;
    }

    public function setCustomShortcode(?string $customShortcode): self
    {
        $this->customShortcode = $customShortcode;

        return $this;
    }

    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    public function setUsageCount(int $usageCount): self
    {
        $this->usageCount = $usageCount;

        return $this;
    }

    public function incrementUsage(): self
    {
        ++$this->usageCount;

        return $this;
    }
}
