<?php
// src/Repository/RecipeImageRepository.php
namespace App\Repository;

use App\Entity\RecipeImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecipeImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecipeImage::class);
    }

    /**
    * @param $recipeId
    * @return RecipeImage[]
    */
    public function findRecipeImages($recipeId)
    {
        // automatically knows to select Recipes
        // the "r" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('ri')
            ->andWhere('ri.recipeId = :id')
            ->setParameter('id', $recipeId)
            ->orderBy('ri.id', 'ASC')
            ->getQuery();

        return $qb->execute();

    }
}
