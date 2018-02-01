<?php
// src/Repository/RecipeRepository.php
namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('r')
            ->where('r.something = :value')->setParameter('value', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
    * @param $name
    * @return Recipe[]
    */
    public function findRecipeByName($name)
    {
        // automatically knows to select Recipes
        // the "r" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->orderBy('r.name', 'ASC')
            ->getQuery();

        return $qb->execute();

        // to get just one result:
        // return = $qb->setMaxResults(1)->getOneOrNullResult();
    }

    /**
    * @param $id
    * @return Recipe[]
    */
    public function findRecipeById($id)
    {
        // automatically knows to select Recipes
        // the "r" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        // to get just one result:
        return $qb->setMaxResults(1)->getOneOrNullResult();
    }
}
