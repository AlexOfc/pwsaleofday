<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwsaleofday extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Скидка дня");
        $this->description = $this->l("Скидка дня");
        
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if ( !parent::install() 
			OR !$this->registerHook(Array(
				'pwsaleofday',
			))
            
        ) return false;

        return true;
    }

    

    //start_helper
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Настройки'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('id'),
                        'name' => 'PWSALEOFDAY_CAT_ID',
                        'desc' => $this->l('Укажите название категории'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPWSALEOFDAY';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'PWSALEOFDAY_CAT_ID' => Tools::getValue('PWSALEOFDAY_CAT_ID', Configuration::get('PWSALEOFDAY_CAT_ID')),
        );
    }
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitPWSALEOFDAY'))
        {
            $maxDepth = (int)(Tools::getValue('PWSALEOFDAY_CAT_ID'));
            if ($maxDepth < 0)
                $output .= $this->displayError($this->l('Опция не прошла проверку, убирите её из кода если не нужна'));
            else{
                Configuration::updateValue('PWSALEOFDAY_CAT_ID', Tools::getValue('PWSALEOFDAY_CAT_ID'));
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=6');
            }
        }
        return $output.$this->renderForm();
    }
    //end_helper

    public function getRandomProduct()
    {
        $prod_id = Db::getInstance()->getRow('
            SELECT DISTINCT cp.id_product, sp.id_product FROM `'._DB_PREFIX_.'category_product` cp 
            RIGHT JOIN `' ._DB_PREFIX_.'specific_price` sp ON (cp.id_product = sp.id_product)
            WHERE id_category = '.Configuration::get('PWSALEOFDAY_CAT_ID').' ORDER BY RAND()');
        if (!empty($prod_id['id_product'])) {
            return $prod_id['id_product'];
            $this->getValue('id_product');
        } else
            return false;
    }
    
    public function getProdData()
    {
        if ($this->getRandomProduct())
        {
            $id = $this->getRandomProduct();
        } else 
            return;
        $product = new Product($id);
        $product->name = $product->name[$this->context->cookie->id_lang];
        $product->available_now = $product->available_now[$this->context->cookie->id_lang];
        $img = Image::getCover($id);
        $img_link = $this->context->link->getImageLink($product->link_rewrite, $img['id_image']);
        $product->image = $img_link;
        $specificPrice = SpecificPrice::getByProductId($product->id);
        
        
        $product->specificPrice = $specificPrice;
        return $product;
    }

	public function hookdisplaypwsaleofday($params){
        $rand_prod = $this->getProdData();
        $this->smarty->assign(array(
            'id_cat' => Configuration::get('PWSALEOFDAY_CAT_ID'),
            'product' => $rand_prod,
        ));
        if (empty($rand_prod)) {
            return;
        } else
		return $this->display(__FILE__, 'pwsaleofday.tpl');
	}


}


