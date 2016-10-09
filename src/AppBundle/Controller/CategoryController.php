<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\VO\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Tomáš Linhart <lin.tomeus@gmail.com>
 */
class CategoryController extends Controller
{

	/**
	 * @Route("/category/{slug}", name="category_detail")
	 * @param Request $request
	 * @return Response
	 */
	public function categoryDetailAction(Request $request)
	{
		/** @var Category $category */
		$category = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
			"slug" => $request->attributes->get("slug"),
		]);
		if (!$category) {
			throw new NotFoundHttpException("Kategorie neexistuje");
		}

		$openCategories = [];
		$breadcrumbs = [];
		$categoryParent = $category;
		$openCategories[] = $categoryParent->getId();
		while ($categoryParent = $categoryParent->getParent()) {
			$openCategories[] = $categoryParent->getId();
			array_unshift($breadcrumbs, new Breadcrumb($categoryParent->getTitle(), $categoryParent->getSlug()));
		}

		return $this->render("category/detail.html.twig", [
			"category" => $category,
			"products" => $category->getProducts(),
			"breadcrumbs" => $breadcrumbs,
			"openCategories" => $openCategories,
			"activeCategory" => $category->getId(),
			"categories" => $this->getDoctrine()->getRepository(Category::class)->findBy(
				[
					"parentId" => null
				],
				[
					"rank" => "desc",
				]
			),
		]);

	}

}