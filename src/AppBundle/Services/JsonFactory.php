<?php

namespace AppBundle\Services;

class JsonFactory 
{
    
    /**
     * Returns a list of Basket object in a JSON format
     * 
     * @param type $baskets a list of Basket objects
     * @return array an array of Basket in a JSON format
     */
    public function getBaskets($baskets) {
        
        if(is_null($baskets)) {
            return [];
        }
        
        //An array which will store all the baskets
        $data = [];
        
        foreach ($baskets as $basket){
            
            //Returns the list of products
            $product_list = $this->getProducts($basket->getProductList());
            
            //General informations of the basket
            array_push($data, array(
            	"id" => $basket->getId(),
                "name" => $basket->getName(),
                "price" => $basket->getPrice(),
                "category" => $basket->getCategory()->getName(),
                "category_image" => $basket->getCategory()->getImagePath(),
                "products" => $product_list
            ));
      
        }
        
        return $data;
        
    }
    
    /**
     * Returns a list of Product object in a JSON format.
     * 
     * The JSON representation contains :
     *  - the name of the product
     *  - the category of the product
     * 
     * If the Product list is empty or null, an empty array is returned
     * 
     * @param type $products a list of Product objects
     * @return array a JSON representation of the projects
     */
    public function getProducts($products) {
        
        if(is_null($products)) {
            return [];
        }
        
        //An array containing the products
        $data = [];
        
        //List of products in the basket
        foreach ($products as $product){  
           array_push($data, array(
               "name" => $product->getName(),
               "category" => $product->getCategory()->getName()
           ));
        }
        
        return $data;
        
    }
    
    public function getBasketCategories($basketCategories) {
    	
    	if(is_null($basketCategories)) {
    		return [];
    	}
    	
    	//An array which will store all the basket categories
    	$data = [];
    	
    	foreach ($basketCategories as $basketCategory){
    		array_push($data, array(
    				"id" => $basketCategory->getId(),
    				"name" => $basketCategory->getName(),
    				"image" => $basketCategory->getImagePath()
    		));
    	}
    	
    	return $data;
    	
    }
    
}