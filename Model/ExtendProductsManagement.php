<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NF\ExtendProducts\Model;

use Magento\Bundle\Model\Product\Type;

class ExtendProductsManagement implements \NF\ExtendProducts\Api\ExtendProductsManagementInterface
{


	protected $_productCollectionFactory;
	protected $scopeConfig;
	protected $response;
        
    public function __construct(      
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Webapi\Rest\Response $response
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->response = $response;

        $this->response->setHeader('Content-Type', 'application/json', true);
    }
    
    

    /**
     * {@inheritdoc}
     */
    public function getExtendProducts($match, $matchValue, $attr, $options, $frontValues)
    {
        
        $vettedAttributes = $this->getAllowed(explode(",", $attr));

        if (!$this->validateMatched($match)) {
            return "{}";
        }


    	$response = json_decode("{}");
    	$productList = [];

    	$productCollection = $this->getProductCollection($vettedAttributes, $match, $matchValue);

    	foreach ($productCollection as $prod) {

    		$data = json_decode("{}");
    		foreach($vettedAttributes as $att){

    			if ($frontValues == "1") {

    				$data->$att = $this->getAttributeValue($prod, $att);

    			} else {

    				$data->$att = $prod->getData($att);

    			}
                
    		}




            /*
            *  Options
            */
            
            if ($options == "1") {

                if(count($prod->getProductOptionsCollection()) > 0){
                    $data->hasOptions = true;
                    $data->options = $this->optionsData($prod);
                } else {
                    $data->hasOptions = false;
                }
                
                /* do bundle options */
                if($prod->getTypeId() == "bundle"){

                    $data->isBundle = true;
                    $data->bundleData = $this->getBundleOptionsInfo($prod, $prod->getTypeInstance());

                } else {
                    $data->isBundle = false;
                }

            } 





            $productList[] = json_encode($data);
    	}

        return $productList;
    }



    
    private function getAllowed($requestedAttr)
    {
    	$allowed = explode(",",$this->getScopeConfigValue("extend_products/general/allowed_attributes"));

    	$vetted = [];

    	foreach($requestedAttr as $attr){

    		if(in_array($attr, $allowed)){
    			$vetted[] = $attr;
    		}

    	}

    	return $vetted;
    }

    private function validateMatched($match)
    {
    	$allowed = explode(",",$this->getScopeConfigValue("extend_products/general/attribute_allowed_to_match"));

    	$vetted = [];

    	foreach($allowed as $attr){

    		if(in_array($match, $allowed)){
    			return true;
    		}

    	}

    	return false;
    }

    private function getProductCollection($getAttributes, $matchAttribute, $matchValue)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect($getAttributes);
        $collection->addAttributeToFilter($matchAttribute,['in'=>explode(",",$matchValue)]);



        

        return $collection;
    }



    /*
    * options
    */


    public function getBundleOptionsInfo($product, Type $bundleInstance)
    {
        $selectionIds = [];
        
            $optionCollection = $bundleInstance->getOptionsCollection($product);

            if (!count($optionCollection->getItems())) {
                return "";
            }

            $selectionCollection = $bundleInstance->getSelectionsCollection($optionCollection->getAllIds(), $product);
            //$selectionIds = array_merge($selectionIds, $selectionCollection->getAllIds());
            $bundleOptions = array();


            foreach($optionCollection as $option){
                $bundleOption = json_decode("{}");
                $bundleOption->data = $option->getData();
                $bundleOption->value = array();
                array_push($bundleOptions, $bundleOption);
            }

           
            foreach($selectionCollection as $bundleOption){
                
                $bundleValues = json_decode("{}");
                $bundleValues->sku = $bundleOption->getSku();
                $bundleValues->name = $bundleOption->getName();
                $bundleValues->id = $bundleOption->getId();
                $bundleValues->option_id = $bundleOption->getOptionId();
                $bundleValues->is_default = $bundleOption->getData('is_default');
                $bundleValues->is_salable = $bundleOption->getData('is_salable');
                $bundleValues->position = $bundleOption->getData('position');
                $bundleValues->type_id = $bundleOption->getData('type_id');
                $bundleValues->selection_price_value = $bundleOption->getData('selection_price_value');
                $bundleValues->selection_qty = $bundleOption->getData('selection_qty');
                $bundleValues->selection_id = $bundleOption->getData('selection_id');
                $bundleValues->has_options = $bundleOption->getData('has_options');
                $bundleValues->has_options = $bundleOption->getData('attribute_set_id');
                
                $iter = 0;
                $count = count($bundleOptions);
                for($x = 0; $x < $count; $x++){
                    if($bundleOptions[$x]->data['option_id'] == $bundleValues->option_id){
                        $iter = $x;
                    }
                }
                array_push($bundleOptions[$iter]->value, $bundleValues);
                
            }

        return $bundleOptions;
    }

    public function optionsData($product)
    {
      $optionsList = [];
      $i = 0;
      $options = $product->getProductOptionsCollection();
      foreach ($options as $option) {
       // array_push($optionsList, );
            $optionObj = json_decode("{}");
            $optionObj->data = json_decode("{}");
            $optionObj->data->title = $option->getData('title');
            $optionObj->data->option_id = $option->getData('option_id');
            $optionObj->data->type = $option->getData('type');
            $optionObj->data->product_id = $option->getData('product_id');
            $optionObj->data->is_require = $option->getData('is_require');
            $optionObj->value = [];
            
            foreach ($option->getValues() as $value) {
                    //$optionObj->value = [];
                    $optionSelectioObject = json_decode("{}");
                    $optionSelectioObject->data = $value->getData();
                    $optionSelectioObject->title = $value->getData()['default_title'];
                    $optionSelectioObject->price = $value->getData()['default_price'];
                    $optionSelectioObject->option_id = $value->getData()['option_id'];
                    $optionSelectioObject->option_type_id = $value->getData()['option_type_id'];
                    array_push($optionObj->value, $optionSelectioObject);
                    

            }

            array_push($optionsList, $optionObj);

        }
        return $optionsList;
    }




    public function getAttributeValue($_product, $attCode){
        
        $val = $_product->getData($attCode);

        if($val){

            $_attribute = $_product->getResource()->getAttribute($attCode);
            $attText = $_product->getAttributeText($attCode);

            if ($_attribute->getIsVisibleOnFront()) {
                $attVal = $_attribute->getFrontend()->getValue($_product);
                return $attVal;    
            } elseif ($attText){    
                return  $attText;    
            } else {
                return  $val;   
            }
        }
        return "";
    }


    private function getScopeConfigValue($path)
    {
    	return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}

