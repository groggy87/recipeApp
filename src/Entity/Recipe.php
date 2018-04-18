<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="App\Entity\RecipeImage", mappedBy="recipeId", orphanRemoval=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $method = null;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $ingredients = null;

    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\File(mimeTypes={ "application/pdf", "image/*" })
    */
    private $recipeFile = null;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $originalFileName = null;

    protected $recipeImages = null;

    public function __construct()
    {
        $this->recipeImages = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getIngredients()
    {
        return $this->ingredients;
    }

    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;
    }

    public function getRecipeFile()
    {
        return $this->recipeFile;
    }

    public function setRecipeFile($recipeFile)
    {
        $this->recipeFile = $recipeFile;
    }

    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }
    
    public function setOriginalFileName($originalFileName)
    {
        $this->originalFileName = $originalFileName;
    }

    public function getRecipeImages()
    {
        return $this->recipeImages;
    }
    public function setRecipeImages($recipeImages)
    {
        $this->recipeImages = $recipeImages;
    }

}
