<?php
/**
 * Category special prices tab
 * 
 * @category    Aydus
 * @package     Aydus_Categoryspecialprices
 * @author		Aydus <davidt@aydus.com>
 */

class Aydus_Categoryspecialprices_Block_Adminhtml_Catalog_Category_Tab_Specialprices extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('special_price_apply');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in special_price_apply flag
        if ($column->getId() == 'in_special_price_apply') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
    	$categoryId = $this->getCategory()->getId();
    	
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_special_price_apply'=>1));
        }
        
        $categoryId = (int) $this->getRequest()->getParam('id', 0);
        $storeId = (int)$this->getRequest()->getParam('store');
        
        /*
         * joinField($alias, $table, $field, $bind, $cond=null, $joinType=�inner�)
         */
        
        $specialPriceApply = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'special_price_apply');
        $attributeId = $specialPriceApply->getId();
        
        $collection = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('sku')
        ->addAttributeToSelect('price')
        ->addAttributeToSelect('special_price')
        ->addStoreFilter($storeId)
        ->joinField('position',
        		'catalog/category_product',
        		'position',
        		'product_id=entity_id',
        		'category_id='.$categoryId,
        		'inner');
        $collection->joinField('special_price_apply',
        		'catalog_product_entity_int',
        		'value',
        		'entity_id=entity_id',
        		'attribute_id='.$attributeId,
        		'left');
        
        $this->setCollection($collection);
        
        if ($this->getCategory()->getProductsReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
        }

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!$this->getCategory()->getProductsReadonly()) {
            $this->addColumn('in_special_price_apply', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_special_price_apply',
                'values'    => $this->_getCheckedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id'
            ));         
        }
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
        $this->addColumn('special_price', array(
        		'header'    => Mage::helper('catalog')->__('Special Price'),
        		'type'  => 'currency',
        		'width'     => '1',
        		'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        		'index'     => 'special_price'
        ));        

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/categoryspecialprices/specialpricesgrid', array('_current'=>true));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if (is_null($products)) {
        	
        	$products = $this->getCategory()->getProductsPosition();
            return array_keys($products);
        }
        return $products;
    }
    
    protected function _getCheckedProducts()
    {
    	$storeId = (int)$this->getRequest()->getParam('store');
       	$products = $this->getCategory()->getProductsPosition();
    	    	
    	foreach ($products as $productId => $position){
    		$specialPriceApply = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'special_price_apply', $storeId);
    		
    		if (!$specialPriceApply){
    			unset($products[$productId]);
    		}
    	}
    	
        return array_keys($products);
    }

}

