<?php

namespace PunchyRascal\DonkeyCms;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Controller;

/**
 * Routes an HTTP request to appropriate controller
 */
class Router {

	private $page, $action, $controller, $app;

	/**
	 * Custom routes added manually
	 * @var array
	 */
	private $additionalRoutes;

	public function __construct(Application $app) {
		$this->app = $app;
		$this->page = Http::getGet('p', Http::getPost('p'));
		$this->action = Http::getGet('action', Http::getPost('action'));
	}

	public function addRoute(Controller\Base $controller, $page, $action = null) {
		$this->additionalRoutes[] = [
			'controller' => $controller,
			'page' => $page,
			'action' => $action
		];
	}

	/**
	 * Find the appropriate route
	 * @return \PunchyRascal\DonkeyCms\Router
	 */
	public function route() {
		$controllerClass = "";
		switch ($this->page) {
			case null:
				$controllerClass = Controller\Homepage::class;
				break;
			case '404':
			default:
				$controllerClass = Controller\Error404::class;
				break;
			case 'cenik-platba-doprava':
				$controllerClass = Controller\PaymentDeliveryPrices::class;
				break;
			case 'resizeProductImage':
				$controllerClass = Controller\Service\ProductImageResizer::class;
				break;
			case 'e-shop':
				$controllerClass = Http::getGet('id') ?
					Controller\Product::class
					: Controller\ProductList::class;
				break;
			case 'ajaxSideCart':
				$controllerClass = Controller\AjaxSideCart::class;
				break;
			case 'page':
				$controllerClass = Controller\Page::class;
				break;
			case 'art':
				$controllerClass = Controller\Article::class;
				break;
			case 'kontakt':
				$controllerClass = Controller\Contact::class;
				break;
			case 'obyvatele':
				$controllerClass = Controller\ArticleList::class;
				break;
			case 'obchodni-podminky':
				$controllerClass = Controller\Terms::class;
				break;
			case 'reklamace':
				$controllerClass = Controller\Returns::class;
				break;
			case 'poll':
				$controllerClass = Controller\Poll::class;
				break;
			case 'kosik':
				$controllerClass = Controller\Cart::class;
				break;
			case 'objednavka':
				$controllerClass = Controller\Order\Form::class;
				break;
			case 'objednavka-rekapitulace':
				$controllerClass = Controller\Order\Review::class;
				break;
			case 'objednavka-odeslana':
				$controllerClass = Controller\Order\Sent::class;
				break;
			case 'rss':
				$controllerClass = Controller\Rss::class;
				break;
			case 'zbozi':
				$controllerClass = Controller\ProductExport::class;
				break;
			case 'categoryTree':
				$controllerClass = Controller\CategoryTree::class;
				break;
			case 'css':
				$controllerClass = Controller\LessCss::class;
				break;
			case 'novinky':
				$controllerClass = Controller\Facebook::class;
				break;
			case 'admin':
				if (!Session::isAdminLogged()) {
					$this->action = null;
				}
				switch ($this->action) {
					case null:
					default:
						$controllerClass = Controller\Admin\Main::class;
						break;
					case 'adminUsers':
						$controllerClass = Controller\Admin\AdminUsers::class;
						break;
					case 'adminUserEdit':
						$controllerClass = Controller\Admin\AdminUserEdit::class;
						break;
					case 'productImports':
						$controllerClass = Controller\Admin\ProductImports::class;
						break;
					case 'paymentDelivery':
						$controllerClass = Controller\Admin\PaymentDelivery\PaymentDeliveryList::class;
						break;
					case 'paymentEdit':
						$controllerClass = Controller\Admin\PaymentDelivery\PaymentEdit::class;
						break;
					case 'deliveryEdit':
						$controllerClass = Controller\Admin\PaymentDelivery\DeliveryEdit::class;
						break;
					case 'eetList':
						$controllerClass = Controller\Admin\Eet\ReceiptList::class;
						break;
					case 'eetSend':
						$controllerClass = Controller\Admin\Eet\ReceiptSend::class;
						break;
					case 'eetReceiptPrint':
						$controllerClass = Controller\Admin\Eet\ReceiptPrint::class;
						break;
					case 'banner':
						$controllerClass = Controller\Admin\Banner::class;
						break;
					case 'order_resume':
						$controllerClass = Controller\Admin\OrderResume::class;
						break;
					case 'e_orders':
						$controllerClass = Controller\Admin\Orders::class;
						break;
					case 'e_items':
						$controllerClass = Controller\Admin\Products::class;
						break;
					case 'e_item_edit':
						$controllerClass = Controller\Admin\ProductEdit::class;
						break;
					case 'e_item_batch_edit':
						$controllerClass = Controller\Admin\ProductBatchEdit::class;
						break;
					case 'showInvoice':
						$controllerClass = Controller\Admin\Invoice::class;
						break;
					case 'importResolve':
						$controllerClass = Controller\Admin\ImportResolve::class;
						break;
					case 'e_cat':
						$controllerClass = Controller\Admin\CategoryList::class;
						break;
					case 'cat_edit':
						$controllerClass = Controller\Admin\CategoryEdit::class;
						break;
					case 'pollEdit':
						$controllerClass = Controller\Admin\PollEdit::class;
						break;
					case 'polls':
						$controllerClass = Controller\Admin\Polls::class;
						break;
					case 'arts':
						$controllerClass = Controller\Admin\Articles::class;
						break;
					case 'artEdit':
						$controllerClass = Controller\Admin\ArticleEdit::class;
						break;
					case 'art_stat':
						$controllerClass = Controller\Admin\ArticleStats::class;
						break;
					case 'images':
						$controllerClass = Controller\Admin\Images::class;
						break;
					case 'pages':
						$controllerClass = Controller\Admin\Pages::class;
						break;
					case 'pageEdit':
						$controllerClass = Controller\Admin\PageEdit::class;
						break;
					case 'productIssues':
						$controllerClass = Controller\Admin\ProductIssues::class;
						break;
					case 'uploader':
						$controllerClass = Controller\Admin\Uploader::class;
						break;
					case 'orderDetail':
						$controllerClass = Controller\Admin\OrderDetail::class;
						break;
				}
		}

		foreach ($this->additionalRoutes AS $route) {
			if ($this->page === $route['page'] AND $this->action === $route['action']) {
				$controllerClass = $route['controller'];
				break;
			}
		}

		$this->controller = new $controllerClass($this->app);

		return $this;
	}

	/**
	 * Returns output of the chosen controller
	 * @return string
	 */
	public function output() {
		$this->controller->execute();
		return $this->controller->output();
	}

}
