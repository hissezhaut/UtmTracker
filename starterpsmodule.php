<?php

if ( !defined( '_PS_VERSION_' ) ) {
	exit;
}

class StarterPsModule extends Module {

	/** @var array Use to store the configuration from database */
	public $config_values;

	/** @var array submit values of the configuration page */
	protected static $config_post_submit_values = array('saveConfig');

	public function __construct () {
		dd( __DIR__, true );
		$this->name = 'starterpsmodule'; // internal identifier, unique and lowercase
		$this->tab = 'front_office_features'; // backend module coresponding category
		$this->version = '0.0.1'; // version number for the module
		$this->author = 'PremiumPresta'; // module author
		$this->need_instance = 0; // load the module when displaying the "Modules" page in backend
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l( 'Starter PrestaShop Module' ); // public name
		$this->description = $this->l( 'Starter Module for PrestaShop 1.6.x' ); // public description

		$this->confirmUninstall = $this->l( 'Are you sure you want to uninstall?' ); // confirmation message at uninstall

		$this->ps_versions_compliancy = [ 'min' => '1.6', 'max' => _PS_VERSION_ ];
	}

	public function install () {
		include dirname(__FILE__) . '/sql/install.php';

		return parent::install()
			&& $this->initConfig() 
			&& $this->registerHook( 'actionAdminControllerSetMedia' )
			&& $this->registerHook( 'actionFrontControllerSetMedia' )
			&& $this->registerHook( 'displayHome' );
	}

	public function uninstall () {
		include dirname(__FILE__) . '/sql/uninstall.php';

		return Configuration::deleteByName($this->name)
			&& parent::uninstall();
	}

	public function hookActionAdminControllerSetMedia () {
		if ( $this->isConfigPage() ) {
			$this->context->controller->addJS( $this->_path . 'views/js/back.js' );
			$this->context->controller->addCSS( $this->_path . 'views/css/back.css' );
		}
	}

	protected function initConfig () {
		$this->config_values = [
			'author' => 'Mark Twain',
			'show_author' => true
		];

		$languages = Language::getLanguages( false );

		foreach ( $languages as $lang ) {
			$this->config_values['quote'][$lang['id_lang']] = 'The secret of getting ahead is getting started. The secret of getting started is breaking your complex overwhelming tasks into small manageable tasks, and then starting on the first one.';
		}

		return $this->setConfigValues( $this->config_values );
	}

	public function getContent () {
		$this->config_values = $this->getConfigValues();

		$this->context->smarty->assign([
			'module' => [
				'class' => get_class($this),
				'name' => $this->name,
				'displayName' => $this->displayName,
				'dir' => $this->_path
			]
		]);

		return $this->postProcess();
	}

	protected function postProcess () {
		$output = '';

		switch ( $this->getPostSubmitValue() ) {
			/* save module configuration */
			case 'saveConfig':
				$languages = Language::getLanguages();

				foreach ( $languages as $lang ) {
					$this->config_values['quote'][$lang['id_lang']] = Tools::getValue( 'quote_' . $lang['id_lang'] );
				}

				$config_keys = array_keys( $this->config_values );
				unset( $config_keys['quote'] ); // language field was set

				foreach ( $config_keys as $key ) {
					$this->config_values[$key] = Tools::getValue( $key, $this->config_values[$key] );
				}

				if ( $this->setConfigValues( $this->config_values ) ) {
					$output .= $this->displayConfirmation( $this->l('Paramêtres enrigistrés') );
				}
			break;

			default:
				$output .= $this->renderForm();
			break;
		}

		return $output;
	}

	protected function getConfigForm () {
		return [
			'form' => [
				'legend' => [
					'title' => $this->displayName,
					'icon' => 'icon-cogs'
				],
				'input' => [
					[
						'label' => $this->l('Quote'),
						'name' => 'quote',
						'type' => 'textarea',
						'cols' => 10,
						'rows' => 10,
						'autoload_rte' => true,
						'lang' => true,
						'required' => true
					],
					[
						'label' => $this->l('Author'),
						'name' => 'author',
						'type' => 'text',
						'class' => 'fixed-width-lg',
					],
					[
						'label' => $this->l('Show Author'),
						'name' => 'show_author',
						'type' => 'switch',
						'is_bool' => true,
						'values' => [
							[
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							],
							[
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							]
						],
					],
				],
				'submit' => [
					'name' => 'saveConfig',
					'title' => $this->l('Save'),
					'class' => 'btn btn-success pull-right'
				]
			]
		];
	}

	protected function renderForm () {
		$helper = new HelperForm;

		$helper->show_toolbar = false;
		$helper->table = $this->name;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&module_name=' . $this->name . '&tab_module=' . $this->tab;

		$helper->tpl_vars = [
			'fields_value' => $this->config_values, /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		];

		return $helper->generateForm(array($this->getConfigForm()));
	}

	public function getConfigValues () {
		return json_decode( Configuration::get( $this->name ), true );
	}

	public function setConfigValues ( $config, $merge = false ) {
		if ( $merge ) {
			$config = array_merge( $this->getConfigValues(), $config );
		}

		if ( Configuration::updateValue( $this->name, json_encode( $config ) ) ) {
			return $config;
		}

		return false;
	}

	protected function getPostSubmitValue () {
		foreach ( self::$config_post_submit_values as $value ) {
			if ( Tools::isSubmit( $value ) ) {
				return $value;
			}
		}

		return false;
	}

	public function isConfigPage () {
		return self::isAdminPage( 'modules' ) && Tools::getValue( 'configure' ) === $this->name;
	}

	public static function isAdminPage ( $page ) {
		return Tools::getValue( 'controller' ) === 'Admin' . ucfirst( $page );
	}

	public function hookActionFrontControllerSetMedia () {
		$this->context->controller->addJS( $this->_path . '/views/js/front.js' );
		$this->context->controller->addCSS( $this->_path . '/views/css/front.css' );
	}

	public function hookDisplayHome ( $params ) {
		!isset( $params['tpl'] ) && $params['tpl'] = 'displayHome';

		$this->config_values = $this->getConfigValues();
		$this->smarty->assign( $this->config_values );

		return $this->display( __FILE__, $params['tpl'] . '.tpl' );
	}
}
