<?php
if(!class_exists('IrbAsDbManager')):
    class IrbAsDbManager{
        private $root;
        
        function __construct($root) {
            $this->root = $root;
        }
        
		function setOption($name, $value){
			$name = $this->root->prefix . '_' . $name;
			$result = update_option($name, $value);
			return $result;
		}
		
		function getOption($name){
			$name = $this->root->prefix . '_' . $name;
			$result = get_option($name);
			return $result;
		}
		
		function deleteOption($name){
			$name = $this->root->prefix . '_' . $name;
			$result = delete_option($name);
			return $result;
		}
		
    }
endif;
