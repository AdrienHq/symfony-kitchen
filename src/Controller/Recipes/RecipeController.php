<?php

namespace App\Controller\Recipes;

use App\Repository\RecipesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    public function __construct(
        private readonly RecipesRepository $recipesRepository
    )
    {
    }

    #[Route('/recipe/{slug}-{id}', name: "recipe.detail", requirements: ['id' => '\d+' , 'slug' => '[a-z0-9-]+'])]
    public function index(int $id, string $slug): Response
    {
        $recipe = $this->recipesRepository->find($id);
        if ($recipe->getSlug() != $slug) {
            return $this->redirectToRoute("recipe.detail", ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render("recipes/show.html.twig", [
            'recipe' => $recipe
        ]);
    }

    #[Route('/recipes', name: "recipes.list")]
    public function listAllRecipes(): Response
    {
        return $this->render("recipes/list.html.twig",[
            'recipes' => $this->recipesRepository->findAll()
        ]);
    }
}