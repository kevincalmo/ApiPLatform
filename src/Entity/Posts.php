<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Filter\SearchFilter;
use App\Repository\PostsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
#[
    ApiResource(
        paginationItemsPerPage: 2,
        paginationMaximumItemsPerPage:2,
        paginationClientItemsPerPage: true,
        collectionOperations: [
            'get',
            'post' => [
                'validation_groups' => [Posts::class, 'validationGroups']
            ]
        ],
        itemOperations: [
            'put' => [
                'denormalization_context' => ['groups' => ['put:post:item']],
                'validation_groups' => [Posts::class, 'validationGroups']
            ],
            'delete',
            'get' => [ //ici posts ne va afficher que l'opération get
                'normalization_context' => ['groups' => ['read:post:collection', 'read:post:item']]
                /* read:item correspond à la vision d'un post précis ( ex :"/api/post/21" ) */
            ]
        ]
    ),
    ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])
] //ApiPlatform peut utiliser cette entité 
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:post:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:post:collection', 'put:post:item']),
        Length(min: 5, groups: ['create:post:collection']),
        Length(min: 10, groups: ['modify:post:collection'])
    ]
    private $title;
    /* length est la contrainte de validation de symfony */

    #[ORM\Column(type: 'text')]
    #[Groups(['read:post:collection', 'put:post:item'])]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:post:item'])]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'posts')]
    #[Groups(['read:post:item', 'put:post:item'])]
    private $categories;

    public static function validationGroups(self $post)
    {
        return [
            'create:post:collection', //contrainte validation pour creer un post
            'modify:post:collection' //contrainte validation pour modifier un post
        ];
    }

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
    }

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
