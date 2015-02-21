<?php

/**
 * Catalog category controller
 *
 * @category    Aydus
 * @package     Aydus_Categoryspecialprices
 * @author		Aydus <davidt@aydus.com>
 */

require('Mage/Adminhtml/controllers/Catalog/CategoryController.php');

class Aydus_Categoryspecialprices_Adminhtml_CategoryspecialpricesController extends Mage_Adminhtml_Catalog_CategoryController
{
    /**
     * Grid Action
     * Display list of category products and whether special price boolean is applied
     *
     * @return void
     */
    public function specialpricesgridAction()
    {
        if (!$category = $this->_initCategory(true)) {
            return;
        }
        
        $html = $this->getLayout()->createBlock('categoryspecialprices/adminhtml_catalog_category_tab_specialprices', 'category.specialprice.grid')->toHtml();
        $html .= Mage::helper('categoryspecialprices')->getApplyJavascript();
        
        $this->getResponse()->setBody($html);
    }

}
