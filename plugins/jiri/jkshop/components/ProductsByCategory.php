<?php namespace Jiri\JKShop\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;


class ProductsByCategory extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Products By Category',
            'description' => 'Display list of products by category'
        ];
    }

    public function defineProperties()
    {
        return [
            'category' => [
                'title'       => 'URL Slug Category',
                'description' => 'URL Slug Category',
                'default'     => '{{ :category }}',
                'type'        => 'string'
            ],            
            'pageNumber' => [
                'title'       => 'URL Page',
                'description' => 'URL Page',
                'default'     => '{{ :page }}',
                'type'        => 'string'
            ],          
            'perPage' => [
                'title'       => 'Products per page',
                'description' => 'Count of products per page',
                'default'     => 20,
                'type'        => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The Max Items property can contain only numeric symbols'                
            ],
            'orderBy' => [
                'title'       => 'Order By',
                'description' => 'Order By',
                'default'     => 'title',
                'type'        => 'dropdown',
                'options'     => [
                    'title' => 'Title', 
                    'retail_price_with_tax' => 'Price with Tax', 
                    'on_sale' => 'On Sale',
                    'id' => 'ID',
                    'ean_13' => 'EAN 13',
                    'barcode' => 'Barcode',
                    'quantity' => 'Quantity',
                    'slug' => 'Slug',
                    ]
            ],   
            'sort' => [
                'title'       => 'Sort',
                'description' => 'Sort ASC or DESC',
                'default'     => 'asc',
                'type'        => 'dropdown',
                'options'     => ['asc'=>'Ascending', 'desc'=>'Descending']
            ],             
            'productPage' => [
                'title'       => 'Product page',
                'description' => 'Product detail page',
                'type'        => 'dropdown',
                'default'     => 'product',
                'group'       => 'Links',
            ],
        ];
    }
    
    public function getProductPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }    
    
    
    public function onRun()
    {
        $slug = $this->property('category');
        $category = \Jiri\JKShop\Models\Category::where('slug', $slug)->first();
        if ($category == null) {
            return \Response::make($this->controller->run('404'), 404);
        }
        
        // get products
        $products = $this->loadProducts();

        // pagination
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $perPage = $this->property('perPage');
        $page = $this->property('pageNumber');
        $productsPagination = new \Illuminate\Pagination\LengthAwarePaginator($products, count($products), $perPage, $page);
        $productsPagination->setPath(\Request::url()."/N");
        $this->productsPagination = $this->page['productsPagination'] = $productsPagination;
        
        // jkshopsetting
        $this->page['jkshopSetting'] = \Jiri\JKShop\Models\Settings::instance();
        
        // set category into page
        $this->page['category'] = $category;
        
        // meta info
        $mainCategory = $category;
        if (isset($mainCategory)) {
            $this->page->meta_title = $mainCategory->meta_title;
            $this->page->meta_description = $mainCategory->meta_description;
            $this->page->meta_keywords = $mainCategory->meta_keywords;        
        }
    }

    protected function loadProducts()
    {
        $slug = $this->property('category');
        //$categories = \Jiri\JKShop\Models\Category::where('slug', $slug)->first()->getAllChildrenAndSelf()->lists("id", "id");
        $category = \Jiri\JKShop\Models\Category::where('slug', $slug)->first();
        
        
        $products = $category->products()
                             ->where("active", 1)
                             ->where("visibility", 1)
                             ->orderBy($this->property('orderBy'), $this->property('sort'))
                             ->get();        
        
        /*
        $products = \Jiri\JKShop\Models\Product::
                where("active",1)->
                join("jiri_jkshop_products_categories","id","=","jiri_jkshop_products_categories.product_id")->
                whereIn("category_id",$categories)->
                where("visibility", 1)->
                groupBy("jiri_jkshop_products.id")->
                orderBy($this->property('orderBy'), $this->property('sort'))->
                get();
         */

        
        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $products->each(function($product) {
            $product->setUrl($this->property('productPage'), $this->controller);
        });        

        return $products;
    }    

}