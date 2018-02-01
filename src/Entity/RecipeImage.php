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
     * @ORM\ManyToOne(targetEntity="App\Entity\Recipe", inversedBy="id", cascade={"persist"}))
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $recipeId;

    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\File(mimeTypes={ "image/png", "image/jpeg" })
    */
    private $recipeImageFile = null;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $originalImageFileName = null;


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
