<?php
if (!defined('_PS_VERSION_'))
	exit;
require_once(dirname(__FILE__) . '/classes/Pricesteal_Settings.php');
require_once(dirname(__FILE__) . '/classes/Pricesteal_Product.php');

class Pricesteal extends Module
{
	/**
	 * function construct
	 * basic function used for initializing PS module
	 */
	public function __construct()
	{
		$this->name = 'pricesteal';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'tsergium';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
		$this->dependencies = array('blockcart');

		parent::__construct();

		$this->displayName = $this->l('Pricesteal');
		$this->description = $this->l('Pricesteal is able to "steal" product prices from competitor web shops and to show to the users/customers some attractive information related to the pricing.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	}
	
	/**
	 * function install
	 * basic function used for installing PS module and creating required tables
	 * @return boolean false
	 */
	public function install()
	{
        $sql = array();
        $sql[] = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ts_pricesteal_settings` (
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`competitor` VARCHAR( 255 ) NOT NULL,
			`regex` VARCHAR( 255 ) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8
		';
        $sql[] = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ts_pricesteal_products` (
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_settings` int(11) NOT NULL,
			`id_product` int( 11 ) NOT NULL,
			`product_url` VARCHAR( 255 ) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8
		';
		if (!parent::install() OR 
			!$this->registerHook('displayAdminProductsExtra') OR
			!$this->registerHook('actionProductUpdate') OR
			!$this->registerHook('displayFooterProduct') OR
			!$this->registerHook('displayLeftColumnProduct') OR
			!$this->runSql($sql)
		) {
			return FALSE;
		}
	}
	
	/**
	 * function runSql
	 * custom function used for executing mysql queries
	 * one should NOT use this, use class methods instead
	 * @param array $sql
	 * @return boolean
	 */
    public function runSql($sql) {
        foreach ($sql as $s) {
			if (!Db::getInstance()->Execute($s)){
				return false;
			}
        }
        return true;
    }
	
	/**
	 * function uninstall
	 * basic function used for uninstalling the module and deleting it's tables
	 * @return type
	 */
    public function uninstall() {
        $sql = array();
	
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ts_pricesteal_settings`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ts_pricesteal_products`';
        if (!parent::uninstall() OR !$this->runSql($sql))
		{
            return FALSE;
        }

        return TRUE;
    }
	
	public function getContent()
	{
		$output = null;
		$sql = array();
		
		if (Tools::isSubmit('submit'.$this->name))
		{
			// Save existing settings
			$pricestealSettings = Pricesteal_Settings::fetchSettings();
			if($pricestealSettings)
			{
				foreach($pricestealSettings as $value)
				{
					$pricesteal_competitor = strval(Tools::getValue('pricesteal_competitor_'.$value['id']));
					$pricesteal_competitor_regex = strval(Tools::getValue('pricesteal_competitor_regex_'.$value['id']));
					if($pricesteal_competitor && $pricesteal_competitor_regex)
					{
						Pricesteal_Settings::updateSettings($value['id'], $pricesteal_competitor, $pricesteal_competitor_regex);
					}
					else
					{
						Pricesteal_Settings::deleteById($value['id']);
					}
					/**
					 * ToDo: Error reporting
					 */
				}
			}
			// Add new settings
			$pricesteal_competitor = strval(Tools::getValue('pricesteal_competitor'));
			$pricesteal_competitor_regex = strval(Tools::getValue('pricesteal_competitor_regex'));
			if($pricesteal_competitor && $pricesteal_competitor_regex)
			{
				Pricesteal_Settings::addSettings($pricesteal_competitor, $pricesteal_competitor_regex);
			}
		}
		return $output.$this->displayForm();
	}
	
	public function displayForm()
	{
		// Get default Language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Pricesteal Settings'),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);

		// Get existing settings
		$pricestealSettings = Pricesteal_Settings::fetchSettings();
		if($pricestealSettings)
		{
			foreach($pricestealSettings as $value)
			{
				$fields_form[0]['form']['input'][] = array(
					'type'		=> 'text',
					'label'		=> $this->l('Competitor'),
					'name'		=> 'pricesteal_competitor_'.$value['id'],
					'size'		=> 50,
					'required'	=> true
				);
				$fields_form[0]['form']['input'][] = array(
					'type'		=> 'text',
					'label'		=> $this->l('Regex'),
					'name'		=> 'pricesteal_competitor_regex_'.$value['id'],
					'size'		=> 50,
					'required'	=> true
				);
				$helper->fields_value['pricesteal_competitor_'.$value['id']] = $value['competitor'];
				$helper->fields_value['pricesteal_competitor_regex_'.$value['id']] = $value['regex'];
			}
		}
		
		// Add new settings
		$fields_form[0]['form']['input'][] = array(
			'type'			=> 'text',
			'label'			=> $this->l('New competitor'),
			'name'			=> 'pricesteal_competitor',
			'size'			=> 50,
			'placeholder'	=> 'Add new competitor',
			'required'		=> true
		);
		$fields_form[0]['form']['input'][] = array(
			'type'			=> 'text',
			'label'			=> $this->l('New regex'),
			'name'			=> 'pricesteal_competitor_regex',
			'size'			=> 50,
			'placeholder'	=> 'Add new regex',
			'required'		=> true
		);

		return $helper->generateForm($fields_form);
	}
	
	/**
	 * function hookDisplayAdminProductsExtra
	 * @param type $params
	 * @return type
	 */
	public function hookDisplayAdminProductsExtra($params)
	{	
        $id_product = Tools::getValue('id_product');
		$inputElements = '';
		
		$pricestealSettings = Pricesteal_Settings::fetchSettings();
		if($pricestealSettings)
		{
			foreach($pricestealSettings as $value)
			{
				$pricestealProduct = Pricesteal_Product::loadByIdProduct($id_product, $value['id']);
				$productUrl = $pricestealProduct->product_url;
				$price = '&nbsp;';
				if($productUrl)
				{
					$price = $this->fetchPrice($value['regex'], $productUrl);
				}
				$inputElements .= "
					<fieldset style=\"border:none;\">
						<textarea placeholder=\"Product URL (with http://) from {$value['competitor']}\" name=\"product_url_{$value['id']}\" rows=\"2\" cols=\"45\">{$productUrl}</textarea>
					</fieldset>
					<div class=\"separation\">{$price}</div>
				";
			}
		}
		$this->context->smarty->assign(array(
			'input_elements'	=> $inputElements,
		));
        return $this->display(__FILE__, 'views/admin/form.tpl');
    }
	
	/**
	 * function fetchPrice
	 * reads external html page, and finds it's price by the given DOM element
	 * @param string $domElem, ex: div.prices span.money-int
	 * @param string $url ex: http://www.emag.ro/sistem-desktop-pc-mac-pro-cu-procesor-intel-174-xeon-174-six-core-e5-3-50ghz-16gb-ssd-256gb-amd-firepro-d500-3gb-md878ro-a/pd/DR9FCBBBM/
	 * @return null or int, price of competitor's product
	 */
	public function fetchPrice($domElem, $url)
	{
		require_once(dirname(__FILE__) . '/classes/simple_html_dom.php'); // required simple dom library
		
		$html = new simple_html_dom(); // instantiate class
		$html->load_file($url); // read external html page
		$price = $html->find($domElem, 0)->innertext; // find required dom element text
		
		if((int) $price) // check if valid price, non null
		{
			return (float) $price; // return price
		}
		return NULL; // return null if not found
	}
	
	
    public function hookActionProductUpdate($params) {
        $id_product = Tools::getValue('id_product');
		
		$pricestealSettings = Pricesteal_Settings::fetchSettings();
		if($pricestealSettings)
		{
			foreach($pricestealSettings as $value)
			{
				$pricestealProduct				= Pricesteal_Product::loadByIdProduct($id_product, $value['id']);
				$pricestealProduct->id_product	= $id_product;
				$pricestealProduct->id_settings	= $value['id'];
				$pricestealProduct->product_url	= Tools::getValue('product_url_' . $value['id']);
				if(!empty($pricestealProduct) && isset($pricestealProduct->id)){
					$pricestealProduct->update();
				} else {
					$pricestealProduct->add();
				}
			}
		}
    }
	
//	public function hookDisplayLeftColumnProduct($params)
//	{
//		return $this->display(__FILE__, 'views/admin/form.tpl');
//	}
}