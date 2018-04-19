<?php
// src/EventListener/RecipeUploadListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\Recipe;
use App\Service\FileUploader;

class RecipeUploadListener
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

        if (!$entity instanceof Recipe) {
            return;
        }

        if ($fileName = $entity->getRecipeFile()) {
            $entity->setRecipeFile(new File($this->uploader->getTargetDir().'/'.$fileName));
        }
    }

    private function uploadFile($entity)
    {
        // upload only works for Recipe entities
        if (!$entity instanceof Recipe) {
            return;
        }

        $file = $entity->getRecipeFile();

        // only upload new files
        if ($file instanceof UploadedFile) {
            //check for null files in controller so previous file isn't nulled
            $fileName = $this->uploader->upload($file);
            $originalfileName = $this->uploader->getOriginalFilename();
            $entity->setRecipeFile($fileName);
            $entity->setOriginalFilename($originalfileName);
        }
    }
}