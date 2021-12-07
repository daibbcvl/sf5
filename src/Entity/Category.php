<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category implements SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableTrait;
    use TimestampableTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categorySlug;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $level = 0;

//    /**
//     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="category")
//     */
//    private $posts;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        //$this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Category $child
     *
     * @return Category
     */
    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param Category $child
     *
     * @return Category
     */
    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    public function getCategorySlug(): ?string
    {
        return $this->categorySlug;
    }

    public function setCategorySlug(string $slug): self
    {
        $this->categorySlug = $slug;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return Category
     */
    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

//    /**
//     * @return mixed
//     */
//    public function getPosts()
//    {
//        return $this->posts;
//    }
//
//    public function getPostCount()
//    {
//        return $this->posts->filter(function (Post $post){ return $post->getType() =='post'; })->count();
//    }
//
//    /**
//     * @param mixed $posts
//     * @return Category
//     */
//    public function setPosts($posts)
//    {
//        $this->posts = $posts;
//
//        return $this;
//    }
//
//    public function addPost(Post $post): self
//    {
//        if (!$this->posts->contains($post)) {
//            $this->posts[] = $post;
//            $post->setCategory($this);
//        }
//
//        return $this;
//    }
//
//    public function removePost(Post $post): self
//    {
//        if ($this->posts->contains($post)) {
//            $this->posts->removeElement($post);
//            // set the owning side to null (unless already changed)
//            if ($post->getCategory() === $this) {
//                $post->setCategory(null);
//            }
//        }
//
//        return $this;
//    }
}
