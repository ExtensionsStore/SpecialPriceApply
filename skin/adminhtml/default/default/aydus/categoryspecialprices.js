/**
 * Category special prices javascript
 * 
 * @category    Aydus
 * @package     Aydus_Categoryspecialprices
 * @author      Aydus <davidt@aydus.com>
 */

var specialPriceApply = function()
{
	var updateHidden = function()
	{
		var ids = [];
		
		jQuery('#special_price_apply input.checkbox').each(function(){
			
			var jQuerycheckbox = jQuery(this);
			var id = jQuerycheckbox.val();
			id = parseInt(id);
			
			if (!isNaN(id)){
				
				var apply = 0;
					
				if (jQuerycheckbox.is(':checked')){
					apply = 1;
				} 
				
				ids.push(id +'='+ apply);
			}
		});
		
		var inSpecialPriceApply = jQuery('#in_special_price_apply')[0];
		
		if (ids.length > 0){
			
			var idsString = ids.join('&');
			jQuery(inSpecialPriceApply).val(idsString);
			
		} else {
			jQuery(inSpecialPriceApply).val(null);
		}
		
	};
	
	return {
		
		init : function()
		{
			var jQueryhiddenField = jQuery('#in_special_price_apply');
			if (jQueryhiddenField.length == 0){
				var jQueryeditForm = jQuery('#category_edit_form').find('.no-display');
				jQueryeditForm.append('<input type="hidden" name="special_price_apply" id="in_special_price_apply" value="" />');
			}
			
			updateHidden();
			
			jQuery('#special_price_apply input.checkbox').click(function(e){
				updateHidden();
			});
		}
		
	};
	
}();	

if (!window.jQuery){
	document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js">\x3C/script><script>jQuery.noConflict();</script>');	
} 

