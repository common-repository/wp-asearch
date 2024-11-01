<?php
if(!class_exists('IrbAsHandler')):
    class IrbAsHandler{
        private $root;
        private $messages;
        
        function __construct($root) {
            $this->root = $root;

            $this->root->orientedTitle = '<b class="text-primary">' . $this->root->company . '</b> ' . $this->root->title;
            $this->root->companySiteTitle = 'Phalcosoft by IRB';
            $this->root->companyUrl = 'http://www.phalcosoft.com';
            if( $this->root->installer()->checkInstallationStatus() ) {
                $licenseCode = $this->root->db()->getOption('codeObject');
                if($licenseCode === false) $this->lCheck = 1;
                $this->root->licenseCode = (!is_bool($licenseCode)) ? json_decode($licenseCode, true) : false;
            } else {
                $this->root->licenseCode = false;
            }
			
			$this->initHooks();
        }
        
		function initHooks(){
			$this->actionsHook();
			add_action('init', array($this, 'processCustomRequests'));
		}
		
		function processCustomRequests(){
			if(isset($_REQUEST['irbaction'])){
				$this->processRequest('irb_as_verification', array($this, 'runValidator'));
				$this->processRequest('irb_as_saveSettings', array($this->root->controller(), 'irbAsSaveSettings'));
			}
		}
			
        function processRequest($action, $callBackFunc) {
			if($this->root->licenseCode === false) {
                $this->lCheck = 1;
            } else if(strtotime('now') >= $this->root->licenseCode['response']['rv']){
                $this->runValidator();
            }
            if(empty($action) && empty($callBackFunc) && empty($_REQUEST['irbaction'])) return false;
            if(isset($_REQUEST['irbaction']) && $_REQUEST['irbaction'] == $action) {
                if(!call_user_func_array($callBackFunc, array())){
                    return false;
                }
            }
        }
        
		function actionsHook(){
			add_action('admin_menu', array($this, 'menuBarsHook'));
			if($this->root->licenseCode === false) {
                $this->lCheck = 1;
            } else if(strtotime('now') >= $this->root->licenseCode['response']['rv']){
                $this->runValidator();
            }
			add_action('wp_enqueue_scripts', array($this, 'enqueuedScriptsHook'));
			add_action('wp_head', array($this, 'inlineHeaderJs'));
			add_filter('pre_get_posts', array($this->root->controller(), 'irbAsExtendedSearch'), 500);
			
			//ajax requests handler
			add_action('wp_ajax_irb_as_search', array($this->root->controller(), 'irbAsSearchManager'));
		}
		
		function adminEnqueuedScriptsHook(){
			//wp_enqueue_script('jquery');
			//wp_enqueue_script($this->root->prefix . '-frontendScript', $this->root->jsUrl . 'aSearchFrontent.js', array('jquery'));
			
			wp_enqueue_style($this->root->prefix . '-backendStyle', $this->root->cssUrl . 'backend.css');
		}
		
		function storeAdminEnqueuedScriptsHook(){
			wp_enqueue_style($this->root->prefix . '-bootstrap', $this->root->cssUrl . 'bootstrap/bootstrap.min.css');
			wp_enqueue_style($this->root->prefix . '-backendStyle', $this->root->cssUrl . 'backend.css');
		}
		
		function enqueuedScriptsHook(){
			wp_enqueue_script('jquery');
			wp_enqueue_script($this->root->prefix . '-frontendScript', $this->root->jsUrl . 'aSearchFrontent.js', array('jquery'));
			
			wp_enqueue_style($this->root->prefix . '-frontendStyle', $this->root->cssUrl . 'frontend.css');
		}
		
		function inlineHeaderJs(){
			$settings = $this->root->controller()->irbAsGetSettings();
			$irb_as_globals = array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'wordsLimit' => $settings['irb_as_min_char_req'],
				'template' => $settings['irb_as_template']
			);
			echo '<script type="text/javascript">var irb_as_globals = ' . json_encode($irb_as_globals) . '</script>';
		}
		
		function menuBarsHook(){
			$mainPage = add_menu_page( 
				$this->root->company . ' ' . $this->root->title,
				$this->root->company . ' ' . $this->root->title,
				'manage_options',
				str_replace('_', '-', $this->root->prefix) . '-asearch',
				array($this, 'loadMainPage')
			);
			$storePage = add_submenu_page( 
				str_replace('_', '-', $this->root->prefix) . '-asearch',
				'IRB Store',
				'IRB Store',
				'manage_options',
				str_replace('_', '-', $this->root->prefix) . '-irb-store',
				array($this, 'loadStorePage')
			);
			
			add_action('admin_print_styles-' . $mainPage, array($this, 'adminEnqueuedScriptsHook'));
			add_action('admin_print_styles-' . $storePage, array($this, 'storeAdminEnqueuedScriptsHook'));
		}
		
		function loadMainPage(){
			$root = $this->root;
			$licenseCode = $this->root->db()->getOption('codeObject');
			if($licenseCode === false) $this->lCheck = 1;
			$this->root->licenseCode = (!is_bool($licenseCode)) ? json_decode($licenseCode, true) : false;
			if(!$root->licenseCode){
				include_once( $this->root->backendDir . '/verification.php' );
			} else {
				include_once( $this->root->backendDir . '/settings.php' );
			}
		}
			
		function loadStorePage(){
			$root = $this->root;
			include_once( $this->root->backendDir . '/irb_store.php' );
		}
			
        function getTemplateFilePath($file) {
            if(is_admin()){
                $url = $this->root->backendDir;
            } else {
                $url = $this->root->frontendDir;
            }
            return $url . $file;
        }
        
        function encodeArrayData($array) {
            $resp = array();
            foreach($array as $key => $value) {
                if(is_array($value)){
                    $resp[$key] = $this->encodeArrayData($value);
                } else {
                    $value = str_replace("'", " ", $value);
                    $resp[$key] = htmlentities($value,  ENT_COMPAT, 'UTF-8');
                }
            }
            return $resp;
        }
        
        function getFilesInFolder($directory, $nameInKey=false) {
            $files = array();
            if($handle = opendir($directory)){
                while(false !== ($file = readdir($handle))){
                    if($file <> "." && $file <> ".."){
                        if(!is_dir($directory . "/" . $file)){
                            if($nameInKey) {
                                $files[$file] = $file;
                            } else {
                                $files[] = $file;
                            }
                        } else {
                            $files[$file] = $this->getFilesInFolder($directory . "/" . $file);
                        }
                    }
                }
                closedir($handle);
            }
            return $files;
        }
        
        function getIpAddr(){
            return $_SERVER['REMOTE_ADDR'];
        }
        
        function runValidator(){
		   if(isset($_REQUEST['lic'])){
				if(!isset($_REQUEST[$this->root->prefix . '_verificationField']) || !wp_verify_nonce($_REQUEST[$this->root->prefix . '_verificationField'], $this->root->prefix . '_verification'))
					return false;
                $reqData = array('name' => $_REQUEST['name'], 'app' => $this->root->title, 'version' => $this->root->version, 'prefix' => $this->root->prefix, 'app_uniqueid' => $this->root->app_uniqueid, 'lic' => $_REQUEST['lic'], 'ip_addr' => $this->getIpAddr(), 'language' => $this->root->activeLanguage, 'url' => $this->root->rootUrl);
                $reqData = $this->preparingRequestData(json_encode($reqData));
            } else {
                $reqData = $this->root->db()->getOption('codeObject');
                if(is_bool($reqData))
                    return false;
                $reqData = json_decode($reqData, true);
                $reqData = $this->preparingRequestData(json_encode($reqData['response']));
            }
            $url = $this->root->apiUrl . 'license/validateLicense/' . $reqData;
            $data = $this->requestUrl($url);
            $data = json_decode($data, true);
            if($data['status'] == 1) {
                $this->root->db()->setOption('codeObject', json_encode($data));
                if(isset($_REQUEST['lic'])){
                    $this->root->lic_response = array('status'=> 1, 'response'=> $data['response']['message']);
                }
            } else {
                if(isset($_REQUEST['lic'])){
                    $this->root->lic_response = array('status'=> 0, 'response'=> $data['response']);
                } else {
                    $this->root->db()->deleteOption('codeObject');
                }
            }
        }
        
        function fetchIRBStoreItems(){
            $url = $this->root->apiUrl . 'products/productsShop/' . $this->root->app_uniqueid;
            $data = $this->requestUrl($url);
            if($data !== false) {
                $data = json_decode($data, true);
                $release = (empty($data['latest'])) ? 0 : 1;
                $this->root->db()->getOption('latest_release');
            }
            return $data;
        }
        
        function getCurrentPageURL() {
            $protocol = 'http';
            if($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')){
                $protocol .= 's';
                $protocol_port = $_SERVER['SERVER_PORT'];
            } else {
                $protocol_port = 80;
            }
            $host = $_SERVER['HTTP_HOST'];
            $port = $_SERVER['SERVER_PORT'];
            $request = $_SERVER['PHP_SELF'];
            $query = (isset($_SERVER['argv']) && isset($_SERVER['argv'][0])) ? substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1) : '';
            $currUrl = $protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request . (empty($query) ? '' : '?' . $query);
            return $currUrl;
        }
		
        function setMessage($msgType, $messageArray, $page=null) {
            $page = (is_null($page)) ? $this->getCurrentPageURL() : $page;
            $msgArray = array(
                $page => array(
                    $msgType => (is_string($messageArray)) ? array($messageArray) : $messageArray
                )
            );
            $_SESSION['irb_sc_msg'] = $msgArray;
            $this->messages = (is_array($this->messages)) ? array_merge($this->messages, $msgArray) : $msgArray;
        }
        
        function getMessage($delMsg=true) {
            $this->messages = (empty($_SESSION['irb_sc_msg'])) ? '' : $_SESSION['irb_sc_msg'];
            $page = $this->getCurrentPageURL();
            $messages = (isset($this->messages[$page])) ? $this->messages[$page] : '';
            if($delMsg) {
                if(isset($_SESSION['irb_sc_msg'][$page])) 
                    unset($_SESSION['irb_sc_msg'][$page]);
                if(isset($this->messages[$page])) 
                    unset($this->messages[$page]);
            }
            $msg = '';
            if(is_array($messages)){
                foreach($messages as $type => $messageArr) {
                    if(!empty($messageArr)){
                        switch($type) {
                            case "error":
                                $class = 'danger';
                                break;
                            case "warning":
                                $class = 'warning';
                                break;
                            case "success":
                                $class = 'success';
                                break;
                            default:
                                $class = 'info';
                                break;
                        }
                        $msg .= '<div class="alert alert-' . $class . '">';
                            $msg .= '<ul class="list-group msgList">';
                            foreach($messageArr as $name => $error) {
                                $msg .= '<li>';
                                $msg .= __($error);
                                $msg .= '</li>';
                            }
                            $msg .= '</ul>';
                        $msg .= '</div>';
                    }
                }
            }
            return $msg;
        }
        
        function requestUrl($url, $posts=null) {
            try{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, array());
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                if(!is_null($posts)){
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
                }
                $data = curl_exec($ch);
                if($data === false) {
                    $data = file_get_contents($url);
                }
            } catch (Exception $e) {
                $data = false;
            }
            return $data;
        }
        
        function alphanumeric_rand($length=15) {
            $list = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $ch = '';
            for($i=0; $i<$length; $i++){
                $ch .= $list[mt_rand(0, (strlen($list) - 1))];
            }
            return $ch;
        }
        
        function preparingRequestData($data) {
            $rand0 = $this->alphanumeric_rand(4);
            $rand1 = $this->alphanumeric_rand(4);
            $data = $this->encodeString(strlen($rand0) . $rand0 . $this->encodeString(json_encode($data)) . $rand1 . strlen($rand1));
            return str_replace('=', '~', $data);
        }
        
        function encodeString($string) {
            return base64_encode(mt_rand(0, 100) . '|'.$string.'|' . mt_rand(0, 100));
        }
        
        function decodeString($string) {
            $decodedStr = base64_decode($string);
            $decodedStr = explode('|', $decodedStr);
            return (isset($decodedStr[1])) ? $decodedStr[1] : null;
        }
    }
endif;
