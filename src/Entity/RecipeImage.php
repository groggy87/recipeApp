<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeImageRepository")
 */
class RecipeImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $recipeId;

    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\File(mimeTypes={ "image/*" })
    */
    private $recipeImageFile = null;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $originalImageFileName = null;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Recipe", inversedBy="recipeImages", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRecipeId()
    {
        return $this->recipeId;
    }

    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
    }

    public function getRecipeImageFile()
    {
        return $this->recipeImageFile;
    }

    public function setRecipeImageFile($recipeImageFile)
    {
        $this->recipeImageFile = $recipeImageFile;
    }

    public function getOriginalImageFileName()
    {
        return $this->originalImageFileName;
    }
    
    public function setOriginalImageFileName($originalImageFileName)
    {
        $this->originalImageFileName = $originalImageFileName;
    }
}
