<li class="priceStealBlock">
	<!--<a class="jsPriceSteal" href="javascript:;">{l s='We have the best price on the market. The prices of our competitors are:' mod='pricesteal'}</a>-->
	{l s='We have the best price on the market. The prices of our competitors are:' mod='pricesteal'}
	<div class="jsPriceStealPrices">
		{if isset($competitors)}
			{foreach from=$competitors item='competitor' name='competitor'}
				<div>
					<a class="priceStealCompetitor" target="_blank" rel="nofollow" href="{$competitor.product_url}">{$competitor.name}</a>
					<span class="jsPriceStealReplace"><a class="jsPriceSteal" href="javascript:;" data-settings="{$competitor.settingsId}">{l s='See the competitor price' mod='pricesteal'}</a></span>
				</div>
			{/foreach}
		{/if}
	</div>
</li>
<script>
$(document).ready(function(){
	$('.jsPriceSteal').click(function(){
		var idSettings = $(this).data('settings');
		var currentObject = $(this);
		var currency = '{$currency}';
		var query = $.ajax({
			type: 'POST',
			url: baseDir + 'modules/pricesteal/ajax.php',
			data: 'idProduct={$idProduct}&idSettings='+idSettings,
			dataType: 'json',
			success: function(data) {
				if(data)
				{
					currentObject.parent().html(data.price+' '+currency);
				}
			}
		});
	});
});
</script>