<?php
if(!class_exists('IrbAsGlobals')):
    class IrbAsGlobals{
        
        private $handlerClass;
        private $controllerClass;
        private $dbClass;
        private $installerClass;
        private $handlerObject;
        private $controllerObject;
        private $dbObject;
        private $installerObject;
        
        function __construct(){
            // Application Settings
            $this->company      = 'IRB';
            $this->title        = 'WP Asearch';
            $this->prefix       = 'irb_as';
            $this->version      = '1.0.1';
            $this->app_uniqueid = 'qK7n-dnLO0D-7Sj-N4dF';
			$this->activeLanguage = 'en';
            $this->apiUrl       = 'http://manage.phalcosoft.com/index.php/';
            
            // Paths
            $this->DS           = DIRECTORY_SEPARATOR;
            $this->rootDir      = dirname(dirname(__FILE__)) . $this->DS;
            $this->rootDirName  = basename($this->rootDir);
            $this->assetsDir    = $this->rootDir . 'assets' . $this->DS;
            $this->classesDir   = $this->rootDir . 'classes' . $this->DS;
            $this->coreDir      = $this->classesDir . 'core' . $this->DS;
            $this->templateDir  = $this->rootDir . 'template' . $this->DS;
            $this->frontendDir  = $this->templateDir . 'frontend' . $this->DS;
            $this->backendDir   = $this->templateDir . 'backend' . $this->DS;
            
            // URLs
            $this->rootUrl      = plugins_url() . '/' . str_replace(' ', '-', strtolower($this->title)) . '/';
            $this->adminUrl     = get_admin_url() . '/';
            $this->assetsUrl    = $this->rootUrl . 'assets/';
            $this->cssUrl       = $this->assetsUrl . 'css/';
            $this->jsUrl        = $this->assetsUrl . 'js/';
            $this->imgUrl       = $this->assetsUrl . 'img/';
            
			$this->settingsOption = 'settings';
			
            // Core Objects
			$this->classPrefix		 = implode('', array_map('ucfirst', explode('_', $this->prefix)));
            $this->dbClass           = $this->classPrefix . 'DbManager';
            $this->handlerClass      = $this->classPrefix . 'Handler';
            $this->controllerClass   = $this->classPrefix . 'Controller';
            $this->installerClass    = $this->classPrefix . 'Installer';
        }
        
        function handler(){
            if(!isset($this->handlerObject)) $this->handlerObject = new $this->handlerClass($this);
            return $this->handlerObject;
        }
        
        function controller(){
            if(!isset($this->controllerObject)) $this->controllerObject = new $this->controllerClass($this);
            return $this->controllerObject;
        }
        
        function db(){
            if(!isset($this->dbObject)) $this->dbObject = new $this->dbClass($this);
            return $this->dbObject;
        }
        
        function installer(){
            if(!isset($this->installerObject)) $this->installerObject = new $this->installerClass($this);
            return $this->installerObject;
        }
    }
endif;
