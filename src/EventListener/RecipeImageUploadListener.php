<?php
// src/EventListener/RecipeImageUploadListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\RecipeImage;
use App\Service\FileUploader;

class RecipeImageUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof RecipeImage) {
            return;
        }

        if ($fileName = $entity->getRecipeImageFile()) {
            $entity->setRecipeImageFile(new File($this->uploader->getTargetDir().'/'.$fileName));
        }

    }

    private function uploadFile($entity)
    {
        // upload only works for Recipe entities
        if (!$entity instanceof RecipeImage) {
            return;
        }

        $file = $entity->getRecipeImageFile();

        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $originalfileName = $this->uploader->getOriginalFilename();
            $entity->setRecipeImageFile($fileName);
            $entity->setOriginalImageFileName($originalfileName);
        }
    }
}