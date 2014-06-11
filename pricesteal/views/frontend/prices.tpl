<li class="priceStealBlock">
	<a class="jsPriceSteal" href="javascript:;">{l s='Show competitor prices' mod='pricesteal'}</a>
	<div class="jsPriceStealPrices"></div>
</li>
<script>
$(document).ready(function(){
	$('.jsPriceSteal').click(function(){
		$('.jsPriceStealPrices').html('');
		var query = $.ajax({
			type: 'POST',
			url: baseDir + 'modules/pricesteal/ajax.php',
			data: 'idProduct={$idProduct}',
			dataType: 'json',
			success: function(data) {
				if(data)
				{
					$.each(data, function(i, item) {	
						$('.jsPriceStealPrices').append('<div class="priceStealCompetitors"><span class="competitor">'+item.competitor+'</span><span class="price">'+item.price+'</span></div>');
					});
				}
			}
		});
	});
});
</script>