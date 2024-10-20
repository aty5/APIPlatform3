<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
// dans l'attribut ci-dessous, ne pas hesiter a creer des fonctions pour garder au propre l en tete de classe
#[
    ApiResource(
    collectionOperations: [
        'get',
        'post',
        'count' => [
            'method' => 'GET',
            'path' => '/posts/count',
            'controller' => PostCountController::class,
            'pagination_enabled' => false,
            'filters' => [],
            'openapi_context' => [
                'summary' => 'Récupere le nombre total d\'article',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'online',
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0
                        ],
                        'description' => 'Filtre les articles en ligne'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ]//,
            //'write' => false options de personnalisations, permet de controler le fonctionnement
        ]
    ],
    itemOperations: [
        'put',
        'delete',
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection', 'read:item', 'read:Post'],
                'openapi_definition_name' => "Detail" //convention
            ]
        ],
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PostPublishController::class,
            //'write' => false options de personnalisations, permet de controler le fonctionnement
            //'read' => false options de personnalisations, permet de controler le fonctionnement
            'openapi_context' => [ //specifications openAPI pour bien documenter
                'summary' => 'Permet de publier un article',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => ['']
                        ]
                     ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:Post']],
    normalizationContext: [
        'groups' => ['read:collection'],
        'openapi_definition_name' => "Collection" //convention
    ],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 5,
    paginationMaximumItemsPerPage: 5
),
    ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])
]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 5, groups: ['create:Post'])]
    #[Groups(['read:collection', 'write:Post'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection', 'write:Post'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:item', 'write:Post'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:item'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'posts')]
    #[Groups(['read:item', 'write:Post'])]
    #[Valid()]
    private ?Category $category = null;

    #[ORM\Column(options:['default' => 0])]
    #[
        Groups(['read:collection']),
        ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'En ligne ou pas ?'])
    ]
    private ?bool $online = false;

    public static function validationGroups(self $post): array
    {
        return ['create:Post'];
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): static
    {
        $this->online = $online;

        return $this;
    }
}
