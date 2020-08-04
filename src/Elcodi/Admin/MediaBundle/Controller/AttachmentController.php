<?php

namespace Elcodi\Admin\MediaBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Core\Entity\Interfaces\EnabledInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Get;
use Mmoreram\ControllerExtraBundle\Annotation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller for Media
 *
 * @Route(
 *      path = "/media/attachment"
 * )
 */
class AttachmentController extends AbstractAdminController {
	/**
	 * List elements of certain entity type.
	 *
	 * This action is just a wrapper, so should never get any data,
	 * as this is component responsibility
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "s/",
	 *      name = "admin_attachment_list",
	 *      methods = {"GET"}
	 * )
	 * @Template
	 */
	public function listAction() {
		if (!$this->canRead()) {
			$this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
			return $this->redirect($this->generateUrl('admin_homepage'));
		}

		return [];
	}

	/**
	 * Enable entity
	 *
	 * @param Request          $request Request
	 * @param EnabledInterface $entity  Entity to enable
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/{id}/enable",
	 *      name = "admin_attachment_enable"
	 * )
	 * @Method({"GET", "POST"})
	 *
	 * @EntityAnnotation(
	 *      class = "elcodi.entity.attachment.class",
	 *      mapping = {
	 *          "id" = "~id~"
	 *      }
	 * )
	 */
	public function enableAction(
		Request $request,
		EnabledInterface $entity
	) {
		if (!$this->canUpdate()) {
			$this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
			return $this->redirect($this->generateUrl('admin_homepage'));
		}

		return parent::enableAction(
			$request,
			$entity
		);
	}

	/**
	 * Disable entity
	 *
	 * @param Request          $request Request
	 * @param EnabledInterface $entity  Entity to disable
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/{id}/disable",
	 *      name = "admin_attachment_disable"
	 * )
	 * @Method({"GET", "POST"})
	 *
	 * @EntityAnnotation(
	 *      class = "elcodi.entity.attachment.class",
	 *      mapping = {
	 *          "id" = "~id~"
	 *      }
	 * )
	 */
	public function disableAction(
		Request $request,
		EnabledInterface $entity
	) {
		if (!$this->canUpdate()) {
			$this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
			return $this->redirect($this->generateUrl('admin_homepage'));
		}

		return parent::disableAction(
			$request,
			$entity
		);
	}

	/**
	 * Delete entity
	 *
	 * @param Request $request      Request
	 * @param mixed   $entity       Entity to delete
	 * @param string  $redirectPath Redirect path
	 *
	 * @return RedirectResponse Redirect response
	 *
	 * @Route(
	 *      path = "/{id}/delete",
	 *      name = "admin_attachment_delete"
	 * )
	 * @Method({"GET", "POST"})
	 *
	 * @EntityAnnotation(
	 *      class = "elcodi.entity.attachment.class",
	 *      mapping = {
	 *          "id" = "~id~"
	 *      }
	 * )
	 */
	public function deleteAction(
		Request $request,
		$entity,
		$redirectPath = null
	) {
		if (!$this->canDelete()) {
			$this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
			return $this->redirect($this->generateUrl('admin_homepage'));
		}

		$redirectPathParam = $request
			->query
			->get('redirect-path');

		$redirectPath = is_null($redirectPathParam)
		? $this->generateUrl('admin_attachment_list')
		: $redirectPath;

		return parent::deleteAction(
			$request,
			$entity,
			$redirectPath
		);
	}

	/**
	 * Nav for entity
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/upload",
	 *      name = "admin_attachment_upload"
	 * )
	 *
	 * @JsonResponse()
	 */
	public function uploadAction() {

		$jsonResponse = $this
			->forward('elcodi.controller.attachment_upload:uploadAction')
			->getContent();

		$response = json_decode($jsonResponse, true);

		if ('ok' === $response['status']) {

			$purchasableId = $this->getRequest()->get('purchasableId');
			if ($purchasableId) {
				$purchasable = $this->get('elcodi.repository.purchasable')->findOneById($purchasableId);

				$attachmentId = $response['response']['id'];
				$attachment = $this->get('elcodi.repository.attachment')->findOneById($attachmentId);

				$attachment->setName(pathinfo($response['response']['filename'], PATHINFO_FILENAME));
				$this->get('elcodi.object_manager.attachment')->persist($attachment);
				$this->get('elcodi.object_manager.attachment')->flush();

				$purchasable->addAttachment($attachment);

				$this->get('elcodi.object_manager.purchasable')->persist($purchasable);
				$this->get('elcodi.object_manager.purchasable')->flush();
			}

			$routes = $this
				->get('router')
				->getRouteCollection();

			$response['response']['routes']['delete'] = $routes
				->get('admin_attachment_delete')
				->getPath();
		}

		return $response;
	}

	/**
	 * Nav for entity
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/upload_category",
	 *      name = "admin_attachment_category_upload"
	 * )
	 *
	 * @JsonResponse()
	 */
	public function uploadCategoryAction() {

		$jsonResponse = $this
			->forward('elcodi.controller.attachment_upload:uploadAction')
			->getContent();

		$response = json_decode($jsonResponse, true);
		// \Doctrine\Common\Util\Debug::dump($response);die();

		if ('ok' === $response['status']) {

			$categoryId = $this->getRequest()->get('categoryId');
			if ($categoryId) {
				$category = $this->get('elcodi.repository.category')->findOneById($categoryId);

				$attachmentId = $response['response']['id'];
				$attachment = $this->get('elcodi.repository.attachment')->findOneById($attachmentId);

				$attachment->setName(pathinfo($response['response']['filename'], PATHINFO_FILENAME));
				$this->get('elcodi.object_manager.attachment')->persist($attachment);
				$this->get('elcodi.object_manager.attachment')->flush();

				$category->addAttachment($attachment);

				$this->get('elcodi.object_manager.category')->persist($category);
				$this->get('elcodi.object_manager.category')->flush();
			}

			$routes = $this
				->get('router')
				->getRouteCollection();

			$response['response']['routes']['delete'] = $routes
				->get('admin_attachment_delete')
				->getPath();
		}

		return $response;
	}
}
