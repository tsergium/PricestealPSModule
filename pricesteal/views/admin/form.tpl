<div class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="pricesteal">
	<h4>{l s='Pricesteal' mod='pricesteal'}</h4>
	<div class="separation"></div>
	{if isset($input_elements)}{$input_elements}{/if}
	<div class="panel-footer">
		<button class="btn btn-default pull-right" name="submitAddproductAndStay" type="submit"><i class="process-icon-save"></i> {l s='Save and stay' mod='pricesteal'}</button>
		<button class="btn btn-default pull-right" name="submitAddproduct" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='pricesteal'}</button>
	</div>
</div>