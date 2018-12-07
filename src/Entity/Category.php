<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(name="category",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="places_name_unique",columns={"name"})}
 * )

 */
class Category
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CriticalArticle", mappedBy="category")
     */
    private $CriticalArticles;

    public function __construct()
    {
        $this->CriticalArticles = new ArrayCollection();
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

    /**
     * @return Collection|CriticalArticle[]
     */
    public function getCriticalArticles(): Collection
    {
        return $this->CriticalArticles;
    }

    public function addCriticalArticle(CriticalArticle $criticalArticle): self
    {
        if (!$this->CriticalArticles->contains($criticalArticle)) {
            $this->CriticalArticles[] = $criticalArticle;
            $criticalArticle->setCategory($this);
        }

        return $this;
    }

    public function removeCriticalArticle(CriticalArticle $criticalArticle): self
    {
        if ($this->CriticalArticles->contains($criticalArticle)) {
            $this->CriticalArticles->removeElement($criticalArticle);
            // set the owning side to null (unless already changed)
            if ($criticalArticle->getCategory() === $this) {
                $criticalArticle->setCategory(null);
            }
        }

        return $this;
    }
}
