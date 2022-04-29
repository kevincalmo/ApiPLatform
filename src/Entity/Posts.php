<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
#[ApiResource(
    normalizationContext:['groups'=>['read:post:collection']],
    itemOperations:[
        'put' => [
            'denormalization_context' => ['groups' => ['put:post:item']]
        ],
        'delete',
        'get' => [//ici posts ne va afficher que l'opération get
            'normalization_context' => ['groups' => ['read:post:collection', 'read:post:item']]
            /* read:item correspond à la vision d'un post précis ( ex :"/api/post/21" ) */
        ]
    ]
)]//ApiPlatform peut utiliser cette entité 
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:post:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:post:collection','put:post:item'])]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:post:collection','put:post:item'])]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:post:item'])]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'posts')]
    #[Groups(['read:post:item', 'put:post:item'])]
    private $categories;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}
