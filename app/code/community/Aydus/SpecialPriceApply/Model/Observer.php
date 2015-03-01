<?php

/**
 * Category special prices
 * 
 * @category    Aydus
 * @package     Aydus_SpecialPriceApply
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_SpecialPriceApply_Model_Observer extends Mage_Core_Model_Abstract
{
	protected $_read;
	protected $_write;
	protected $_table;
	
	protected function _construct()
	{
		$resource = Mage::getSingleton('core/resource');
		$this->_write = $resource->getConnection('core_write');
		$this->_read = $resource->getConnection('core_read');
		$prefix = Mage::getConfig()->getTablePrefix();
		$this->_table = $prefix.'Aydus_specialpriceapply';
	}
	
	/**
	 * Observer on adminhtml_catalog_category_tabs
	 * Add 'Category Special Prices' tab while the tabs are being generated
	 * 
     * @param Varien_Event_Observer $observer
	 * @return Aydus_SpecialPriceApply_Model_Observer
	 */
	public function addCategorySpecialPricesTab($observer)
	{
		$tabs = $observer->getTabs();
		
		$block = Mage::app()->getLayout()->createBlock('specialpriceapply/adminhtml_catalog_category_tab_specialprices')->setName('category.specialprice.grid');
        $content = $block->toHtml();
        $content .= Mage::helper('specialpriceapply')->getApplyJavascript();
        
		$tabs->addTab('specialprice', array(
				'label'     => Mage::helper('specialpriceapply')->__('Category Special Prices'),
				'content'   => $content,
				'active'    => false
		));
		
		return $this;
	}
	
	/**
	 * Apply special price
	 * 
     * @param Varien_Event_Observer $observer
	 * @return Aydus_SpecialPriceApply_Model_Observer
	 */
	public function applySpecialPrices($observer)
	{
		
		$data = $observer->getRequest()->getPost();
		
		$specialPriceApply = $data['special_price_apply'];
		
		$specialPriceApplies = explode('&',$specialPriceApply);
		
		if (is_array($specialPriceApplies) && count($specialPriceApplies)>0){
			foreach ($specialPriceApplies as $specialPriceApply){
				
				$productApply = explode('=',$specialPriceApply);
				
				if (is_array($productApply) && count($productApply)==2){
					
					$productId = $productApply[0];
					$apply = $productApply[1];
					
					try {
						
						$product = Mage::getModel('catalog/product');
						$product->load($productId);
						$currentSpecialPriceApply = $product->getSpecialPriceApply();
						
						if ($currentSpecialPriceApply != $apply){
							
							$product->setSpecialPriceApply($apply)->save();
						} 
						
					} catch (Exception $e){
						
						Mage::log($e->getMessage(),null, 'specialpriceapply.log');
					}
					
				}
				
			}
			
		}
		
		return $this;
	}
	
	/**
	 * Reserve the special price date and set a date in the past 
	 * So special price does not display
	 * 
     * @param Varien_Event_Observer $observer
	 * @return Aydus_SpecialPriceApply_Model_Observer
	 */
	public function productSpecialPriceApply($observer)
	{
		$product = $observer->getProduct();
		$productId = $product->getId();
		$specialFromDate = $product->getSpecialFromDate();
		$specialToDate = $product->getSpecialToDate();
		
		$savedDates = $this->_read->fetchAll("SELECT special_from_date, special_to_date FROM {$this->_table} WHERE product_id = '$productId'");
		
		if (!$product->getSpecialPriceApply()){
			
			//save the current date
			$specialFromDate = ($specialFromDate && strtotime($specialFromDate)) ? date('Y-m-d H:i:s', strtotime($specialFromDate)) : null;
			$specialToDate = ($specialToDate && strtotime($specialToDate)) ? date('Y-m-d H:i:s', strtotime($specialToDate)) : null;
				
			if ($specialFromDate){
				$query = "REPLACE INTO {$this->_table} (product_id, special_from_date, special_to_date) VALUES('$productId',".(($specialFromDate)?"'$specialFromDate'":"NULL").",".(($specialToDate)?"'$specialToDate'":"NULL").")";
				$this->_write->query($query);
			}
			
			//set a date in the past
			$product->setSpecialFromDate('1/1/2014')->setSpecialToDate('1/1/2014');
			
		//put back reserved to/from dates
		} else if ($specialFromDate == '1/1/2014' && is_array($savedDates) && count($savedDates)>0)   {
			
			$specialFromDate = ($savedDates[0]['special_from_date']) ? date('n/j/Y',strtotime($savedDates[0]['special_from_date'])) : null;
			$specialToDate = ($savedDates[0]['special_to_date']) ? date('n/j/Y',strtotime($savedDates[0]['special_to_date'])) : null;
			
			$product->setSpecialFromDate($specialFromDate)->setSpecialToDate($specialToDate);
				
		}
		
		return $this;
	}
	
	/**
	 * Display the reserved date in admin product edit
	 * 
	 * @see catalog_product_edit_action
     * @param Varien_Event_Observer $observer
	 * @return Aydus_SpecialPriceApply_Model_Observer
	 */
	public function displaySpecialPriceDate($observer)
	{
		$product = $observer->getProduct();
		$productId = $product->getId();
		
		$specialFromDate = ($product->getSpecialFromDate()) ? date('n/j/Y', strtotime($product->getSpecialFromDate())) : null;
		$specialToDate = ($product->getSpecialToDate()) ? date('n/j/Y', strtotime($product->getSpecialToDate())) : null;
		
		$savedDates = $this->_read->fetchAll("SELECT special_from_date, special_to_date FROM {$this->_table} WHERE product_id = '$productId'");
		
		if ($specialFromDate == '1/1/2014' && is_array($savedDates) && count($savedDates)>0)   {
				
			$specialFromDate = ($savedDates[0]['special_from_date']) ? date('n/j/Y',strtotime($savedDates[0]['special_from_date'])) : null;
			$specialToDate = ($savedDates[0]['special_to_date']) ? date('n/j/Y',strtotime($savedDates[0]['special_to_date'])) : null;
				
			$product->setSpecialFromDate($specialFromDate)->setSpecialToDate($specialToDate);
 		}
				
		return $this;
	}
	
}