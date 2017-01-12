<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Store\ProductBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Component\Product\Entity\Interfaces\CategoryInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;
use Elcodi\Component\Product\Repository\CategoryRepository;
use Elcodi\Component\Product\Repository\PurchasableRepository;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as AnnotationEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Category controller
 *
 * @Route(
 *      path = ""
 * )
 */
class CategoryController extends Controller
{
    use TemplateRenderTrait;

    /**
     * Renders the category nav component
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/categories/nav",
     *      name = "store_categories_nav",
     *      methods = {"GET"}
     * )
     */
    public function navAction()
    {
        $masterRequest = $this
            ->get('request_stack')
            ->getMasterRequest();

        $currentCategory = $this->getCurrentCategoryGivenRequest($masterRequest);

        $categoryTree = $this
            ->get('elcodi_store.store_category_tree')
            ->load();

        return $this->renderTemplate(
            'Subpages:category-nav.html.twig',
            [
                'currentCategory' => $currentCategory,
                'categoryTree' => $categoryTree,
            ]
        );
    }

    /**
     * Render all category purchasables
     *
     * @param CategoryInterface $category Category
     *
     * @return Response Response
     *
     * @Route(
     *      path = "category/{slug}/{id}/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "store_category_purchasables_list",
     *      requirements = {
     *          "slug" = "[a-zA-Z0-9-]+",
     *          "id" = "\d+",
     *          "page" = "\d*",
     *          "limit" = "\d*",
     *      },
     *      defaults = {
     *          "page" = "1",
     *          "limit" = "10",
     *          "orderByField" = "id",
     *          "orderByDirection" = "DESC",
     *      },
     *      methods = {"GET"}
     * )
     *
     * @AnnotationEntity(
     *      class = "elcodi.entity.category.class",
     *      name = "category",
     *      mapping = {
     *          "id" = "~id~",
     *          "enabled" = true,
     *      }
     * )
     */
    public function viewAction(CategoryInterface $category, $slug, $id, $page, $limit, $orderByField, $orderByDirection, Request $request)
    {
        //item_for_page parametro di configurazione, indica il numero di elementi per pagina, se settato imposta la vairabile $limit con un valore diverso dal default = 10
        if ($this->container->hasParameter('item_for_page')) {
            $limit = $this->getParameter('item_for_page');
        }

        /**
         * We must check that the product slug is right. Otherwise we must
         * return a Redirection 301 to the right url
         */
        if ($slug !== $category->getSlug()) {
            return $this->redirectToRoute('store_category_purchasables_list', [
                'id' => $category->getId(),
                'slug' => $category->getSlug(),
            ], 301);
        }

        if ($category->getSubCategories()->count() > 0) {
            return $this->redirectToRoute('store_subcategories_list', array('id' => $category->getId()));
        }

        /**
         * @var CategoryRepository $categoryRepository
         * @var PurchasableRepository $purchasableRepository
         */
        $categoryRepository = $this->get('elcodi.repository.category');
        $purchasableRepository = $this->get('elcodi.repository.purchasable');

        $categories = array_merge(
            [$category],
            $categoryRepository->getChildrenCategories($category)
        );

        $purchasablesQuery = $purchasableRepository->getAllFromCategories($categories);

        $paginator = new Paginator($purchasablesQuery);

        $paginator->getQuery()
            ->setFirstResult($limit * ($request->get('page') - 1)) // Offset
            ->setMaxResults($limit); // Limit

        $maxPages = ceil($paginator->count() / $limit);

        return $this->renderTemplate(
            'Pages:category-view.html.twig',
            [
                'category' => $category,
                'purchasables' => $paginator,
                'currentPage' => $request->get('page'),
                'limit' => $limit,
                'totalPages' => $maxPages,
            ]
        );
    }

    /**
     * Fa il render del template che mostra le sottocategorie di una categoria padre paginandole
     * @Route(
     *      path = "categories/{id}/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "store_subcategories_list",
     *      requirements = {
     *          "page" = "\d*",
     *          "limit" = "\d*",
     *      },
     *      defaults = {
     *          "page" = "1",
     *          "limit" = "10",
     *          "orderByField" = "id",
     *          "orderByDirection" = "DESC",
     *      },
     *      methods = {"GET"}
     * )
     * @AnnotationEntity(
     *      class = "elcodi.entity.category.class",
     *      name = "category",
     *      mapping = {
     *          "id" = "~id~",
     *          "enabled" = true,
     *      }
     * )
     */
    public function subcategoriesView(CategoryInterface $category, $id, $page, $limit, $orderByField, $orderByDirection, Request $request)
    {

        return $this->renderTemplate(
            'Pages:subcategories-view.html.twig',
            [
                'category' => $category,

            ]
        );

    }

    /**
     * Fa il render del template che mostra le sottocategorie di una categoria padre paginandole
     * @Route(
     *      path = "categories/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "store_all_root_categores",
     *      requirements = {
     *          "page" = "\d*",
     *          "limit" = "\d*",
     *      },
     *      defaults = {
     *          "page" = "1",
     *          "limit" = "10",
     *          "orderByField" = "id",
     *          "orderByDirection" = "DESC",
     *      },
     *      methods = {"GET"}
     * )

     */
    public function getAllRootCategoriesAction($page, $limit, $orderByField, $orderByDirection, Request $request)
    {

        $categoryRepository = $this->get('elcodi.repository.category');
        $categories = $categoryRepository->findBy(array('root' => true, 'enabled' => true));
        return $this->renderTemplate(
            'Pages:all-categories-view.html.twig',
            [
                'categories' => $categories,

            ]
        );

    }

    /**
     * Given a request, return the current highlight-able category
     *
     * @param Request $request Request
     *
     * @return CategoryInterface|null
     */
    protected function getCurrentCategoryGivenRequest(Request $request)
    {
        $masterRoute = $request->get('_route');
        $category = null;

        /**
         * @var CategoryInterface $category
         * @var PurchasableInterface $purchasable
         */
        if ($masterRoute === 'store_purchasable_view') {
            $purchasableId = $request->get('id');
            $productRepository = $this->get('elcodi.repository.purchasable');

            $purchasable = $productRepository->find($purchasableId);
            $category = $purchasable->getPrincipalCategory();
        } elseif ($masterRoute === 'store_category_purchasables_list') {
            $categoryId = $request->get('id');
            $categoryRepository = $this->get('elcodi.repository.category');
            $category = $categoryRepository->find($categoryId);
        }

        return $category;
    }
}
