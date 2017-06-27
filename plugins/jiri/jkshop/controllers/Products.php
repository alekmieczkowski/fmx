<?php namespace Jiri\JKShop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];
    
    public $requiredPermissions = ['jiri.jkshop.products'];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Jiri.JKShop', 'jkshop', 'products');
    }
    
    /**
     * Duplicate product
     * 
     */
    public function onDuplicate(){

        
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            /** cycle through each id **/
            foreach ($checkedIds as $objectId) {
                /** Check if there's an object actually related to this id
                    * Make sure you replace MODELNAME with your own model you wish to delete from.
                  **/
                if (!$object = \Jiri\JKShop\Models\Product::find($objectId))
                    continue; /** Screw this, next! **/
                /** Valid item, delete it **/
                $object->duplicate();
            }

        }        
        
        return $this->listRefresh();
     }    
}