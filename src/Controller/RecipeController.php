<?php
// src/Controller/RecipeController.php
namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\GetDataFromUrl;
use App\Entity\Recipe;
use App\Entity\RecipeImage;
use App\Service\FileUploader;
use App\Form\RecipeType;
use App\Form\RecipeImageType;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;

class RecipeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->redirectToRoute('recipe_list');
    }
    /**
     * @Route("/recipes", name="recipe_list")
     */
    public function list()
    {
        //TODO: handle pagination for large result sets 
        $recipes = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->findAll();

        return $this->render('recipes/list-recipes.html.twig', array(
            'recipes' => $recipes,
        ));
    }

    /**
     * @Route("/recipes/{id}", name="recipe_show", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        //get recipe where $id = $id 
        $recipe = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        else
        {
            //put itinto array of details to be passed into twig template
            $recipe_arr = array(
                            'id' => $id
                            ,'title' => $recipe->getName()
                            ,'method' => $recipe->getMethod()
                            , 'ingredients' => $recipe->getIngredients()
                            ); 

            $recipeImages = $this->getDoctrine()
            ->getRepository(RecipeImage::class)
            ->findRecipeImages($id);

            $img_arr = array();
            foreach ($recipeImages as $recipeImage) {
                $img_arr[] = basename($recipeImage->getRecipeImageFile());
            }
            $recipe_arr['images'] = $img_arr;

            //if has file load file viwer
            if($recipe->getRecipeFile() != null){

                $recipe_arr['filename'] = basename($recipe->getRecipeFile());
                if(pathinfo($recipe->getRecipeFile(), PATHINFO_EXTENSION) == "pdf"){
                    return $this->render('recipes/show-recipe-file.html.twig', array(
                        'recipe' => $recipe_arr,
                    )); 
                }
                else{
                    return $this->render('recipes/show-recipe-image.html.twig', array(
                        'recipe' => $recipe_arr,
                    )); 
                }
            }
            else
            {
                return $this->render('recipes/show-recipe.html.twig', array(
                    'recipe' => $recipe_arr,
                ));                
            } 

        }

    }

    /**
     * @Route("/recipes/newrecipe", name="new_recipe")
     */
    public function newRecipe(Request $request, FileUploader $fileUploader)
    {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // file upload should be handled by doctrine in src/EventListener/RecipeUploadListener.php
            // persist recipe (insert it)
            $em = $this->getDoctrine()->getManager();
            //get original upload name

            $em->persist($recipe);    
            //  //we need to flush first in order to be able to retrieve the id of the saved recipe (old db config wasn't right)
            // $em->flush();

            foreach ($recipe->getRecipeImages() as $image) {
                $image->setRecipe($recipe);
                $em->persist($image);
            }
            $em->flush();
            

            return $this->show($recipe->getId());
        }
        //pass in edit or new mode and replace title on template 
        return $this->render('recipes/add-edit-recipe.html.twig', array(
                'form' => $form->createView(),
                'mode' => 'New',
        ));  

    }
    /**
     * @Route("/recipes/edit/{id}", name="recipe_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, FileUploader $fileUploader, $id)
    {
        //get images for this id + show in list with delete option 
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository(Recipe::class)->findRecipeById($id);

        if (!$recipe) {
            throw $this->createNotFoundException(
                'No recipe found for id '.$id
            );
        }

        $existingFile = $recipe->getRecipeFile();
        $existingFileName = $recipe->getOriginalFileName();

        $img_arr = [];
        if($recipe->getRecipeImages()){
            foreach ($recipe->getRecipeImages() as $image) {
                $img_arr[$image->getId()] = basename($image->getOriginalImageFileName());
            }            
        }

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //TODO: add method to actively remove existing images
            //get original upload name
            foreach ($recipe->getRecipeImages() as $image) {
                $image->setRecipe($recipe);
                $em->persist($image);
            }
            //so existing files are not overwritten
            //TODO: add method to actively remove existing file (via JS I reckon or checkbox beside existing filename)
            $uploadedFile = $form["recipeFile"]->getData();
            if($uploadedFile == null && $existingFile != null){
                $recipe->setRecipeFile(basename($existingFile));
            }
            $em->persist($recipe);    
            $em->flush();
        
            return $this->show($id);
        }

        return $this->render('recipes/add-edit-recipe.html.twig', array(
            'form' => $form->createView(),
            'mode' => 'Edit',
            'existingFileName' => $existingFileName,
            'recipeId' => $recipe->getId(),
            'existingImages' => $img_arr
        ));                


    }

    /**
     * @Route("/recipes/delete/{id}", name="recipe_delete", requirements={"id"="\d+"})
     */
    public function delete($id)
    {

        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException(
                'No recipe found for id '.$id
            );
        }

        $em->remove($recipe);
        $em->flush();

        return $this->redirectToRoute('recipe_list');
    }

    /**
     * @Route("/recipes/search", name="recipe_search")
     */
    public function search(Request $request)
    {
        // $_POST parameters
        $searchTerm = $request->request->get('searchTerm');
        if($searchTerm == null){
            return $this->render('recipes/no-results-found.html.twig');   
        }

        // $_GET parameters
        //$searchTerm = $request->query->get('searchTerm');

        $search_results = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->findRecipeByName($searchTerm);// TODO: change this to search by all fields in recipe table

        if($search_results){
             return $this->render('recipes/search-results.html.twig', array(
                'search_results' => $search_results,
            ));           
        }

    }

    /**
     * @Route("/recipes/getRecipeFromUrl", name="get_recipe_from_url")
     */
    function getRecipeFromUrl(Request $request, GetDataFromUrl $getUrlDataObj) {
        //bbcgoodfood sites not working - could be loaded via ajax 
        //$url = 'http://susanjanewhite.com/5-spice-apple-and-ginger-lentil-soup-served-with-garlic-yoghurt/';
        $url = $request->request->get('url');
        //html 
        $html = $getUrlDataObj->curlGetData($url);
        //array of keywords
        //$keywords = $getUrlDataObj->phpDomGetData($url);
        $data = $getUrlDataObj->filterHTML($html);

        if($data != false)
        {
             return $this->render('recipes/url-results.html.twig', array(
                'data' => $data,
            ));   
        }
        else
        {
             return $this->render('recipes/no-results-found.html.twig');           
        }  
    }

    /**
     * @Route("/recipes/downloadRecipe/{id}", name="download_recipe", requirements={"id"="\d+"})
     */
    public function downloadRecipe($id) {
        //https://mobikul.com/controller-upload-download-files-symfony/
        try {
            $recipe = $this->getDoctrine()
                ->getRepository(Recipe::class)
                ->findRecipeById($id);// TODO: change this to search by all fields in recipe table

            $file = $recipe->getRecipeFile();

            if (! $file) {
                $array = array (
                    'status' => 0,
                    'message' => 'File does not exist' 
                );
                $response = new JsonResponse ( $array, 200 );
                return $response;
            }
            
            $fileName = $file->getFileName ();
            $displayName = $recipe->getOriginalFilename();// get original filename
            $file_with_path = $this->get('kernel')->getProjectDir()."/public/uploads/recipes/" . $fileName;
            $response = new BinaryFileResponse ( $file_with_path );
            $response->headers->set ( 'Content-Type', 'text/plain' );
            $response->setContentDisposition ( ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName );
            return $response;
        } catch ( Exception $e ) {
            $array = array (
                'status' => 0,
                'message' => 'Download error' 
            );
            $response = new JsonResponse ( $array, 400 );
            return $response;
        }
    }
    /**
     * @Route("/recipes/deleteFile/{id}", name="delete_file", requirements={"id"="\d+"})
     */
    public function deleteFile($id){

        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException(
                'No recipe found for id '.$id
            );
        }

        $recipe->setRecipeFile(null);
        $recipe->setOriginalFileName(null);
        $em->flush();

        return new Response(
            'deleted'
        );

    }

    /**
     * @Route("/recipes/deleteImage/{id}", name="delete_image", requirements={"id"="\d+"})
     */
    public function deleteImage($id){

        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository(RecipeImage::class)->find($id);

        if (!$image) {
            throw $this->createNotFoundException(
                'No image found for id '.$id
            );
        }

        $em->remove($image);
        $em->flush();

        return new Response(
            'deleted'
        );

    }
}