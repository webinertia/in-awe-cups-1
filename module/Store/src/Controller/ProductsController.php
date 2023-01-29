<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Filter\TitleToLabel;
use Laminas\Navigation\Navigation;
use Laminas\Filter\FilterPluginManager;
use Laminas\Filter\UpperCaseWords;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\Category;
use Store\Model\Image;
use Store\Model\Product;
use Store\Model\ProductOptions;
use Store\Model\OptionsPerProduct;

final class ProductsController extends AbstractAppController
{
    /** @var Category $category */
    protected $category;
    /** @var UpperCaseWords $ucwFilter */
    protected $ucwFilter;
    /** @var Product $product */
    protected $product;
    /** @var ProductOptions $productOptions*/
    protected $productOptions;
    /** @var OptionsPerProduct $optionLookup */
    protected $optionLookup;

    /** @var TitleToLabel $titleToLabelFilter */
    protected $titleToLabelFilter;
    /** @var $navigation */
    protected $navigation;
    /** @var Image $image */
    protected $image;

    public function __construct(
        Category $category,
        FilterPluginManager $filterManager,
        Product $product,
        ProductOptions $productOptions,
        OptionsPerProduct $optionLookup,
        Image $image,
        array $config
        ) {
        parent::__construct($config);
        $this->category           = $category;
        $this->titleToLabelFilter = $filterManager->get(TitleToLabel::class);
        $this->ucwFilter          = $filterManager->get(UpperCaseWords::class);
        $this->product            = $product;
        $this->productOptions     = $productOptions;
        $this->optionLookup       = $optionLookup;
        $this->image              = $image;
    }

    // Show all products for a category or show a single product
    public function indexAction(): ModelInterface
    {
        $navigation = $this->getService(Navigation::class);
        // this is navigation tab as active
        $page = $navigation->findOneBy('label', 'Shop');
        $page->active = true;
        $params = $this->params()->fromRoute();
        $this->view->setVariable('showHeader', true);
        switch($params['product']) {
            case 'all':
                $queryParams = $this->params()->fromQuery();
                $this->view->setVariables([
                    'headerTitle' => $this->titleToLabelFilter->filter($params['category']),
                ]);
                $sidebar = new ViewModel(
                    [
                        'supported_search_filters' => ['cost', 'onSale'],
                        'productOptions' => $this->optionLookup->fetchSearchableOptions($params['category']),
                        'category'       => $params['category'],
                        'query'          => $queryParams,
                        'maxPrice'       => $this->product->fetchMaxCost($this->titleToLabelFilter->filter($params['category']), true),
                    ]
                );
                $sidebar->setTemplate('/store/products/children/product-search-sidebar');
                $this->view->addChild($sidebar, 'sidebar');
                $this->view->setVariables(
                    [
                        'products' => $this->image->fetchAllProductsByMultiColumns(
                            true,
                            true,
                            ['i.categoryTitle' => $params['category']]
                        ),
                    ]
                );
                break;
            default:
                $this->view->setVariables([
                    'headerTitle' => $this->titleToLabelFilter->filter($params['product']),
                ]);
                // override the template so we only show one product, the product detail page
                $this->view->setTemplate('/store/products/detail');
            break;
        }
        return $this->view;
    }
}
