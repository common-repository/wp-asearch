<?php
if(!class_exists('IrbAsInstaller')):
    class IrbAsInstaller{
        private $root;
        
        function __construct($root) {
            $this->root = $root;
        }
        
        function checkInstallationStatus(){
            $result = array();
            $tablesArray = array();
			if(empty($tablesArray))
				return true;
            foreach($tablesArray as $table) {
                $table = $table . 'Table';
                $table = $this->root->$table;
                $result[$table] = $this->checkTableExistance( $table );
            }
            return (array_search(false, $result)) ? false : true;
        }
        
        function checkTableExistance($tableName) {
            global $wpdb;
            $result = $wpdb->rawQuery( 'DESC '.$tableName.';' );
            return count ($result) > 0;
        }
        
        function runInstaller(){
            if(!$this->checkInstallationStatus()){
                $this->installTables();
                $this->addDefaultValues();
            }
        }
        
        function installTables(){
            global $wpdb;
            $tablesArray = array();
            try{
				if(!empty($tablesArray)){
					foreach($tablesArray as $table) {
						$table = $table . 'TableStruct';
						$wpdb->rawQuery( $this->$table() );
					}
				}
            } catch (Exception $e) {
                $this->root->handler()->setMessage('danger', $e->getMessage());
                //$this->root->handler()->getMessage();
            }
        }
        
        function addDefaultValues(){
            
        }
        
    }
endif;
