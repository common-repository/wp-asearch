<?php
if(!class_exists('IrbAsController')):
    class IrbAsController{
        private $root;
        
        function __construct($root) {
            $this->root = $root;
        }
        
		function irbAsExtendedSearch(&$wp_query){
			if(!empty($wp_query->query['s'])){
				$settings = $this->irbAsGetSettings();
				$wp_query->query_vars['post_type'] = $settings['irb_as_searchFrom'];
				$wp_query->query_vars['post_status'] = $settings['irb_as_postStatus'];
				return $wp_query;
			}
		}
		
		function irbAsSearchManager(){
			$args = $_REQUEST;
			if(isset($args['s'])){
				global $wp_version;
				$settings = $this->irbAsGetSettings();
				$search_query = new WP_Query(array(
					's' => $args['s'],
					'post_type' => $settings['irb_as_searchFrom'],
					'post_status' => $settings['irb_as_postStatus'],
					'showposts' => $settings['irb_as_display_records'],
					'paged' => $paged,
				));
				$search_results = array();
				if($search_query->get_posts()) {
					foreach($search_query->get_posts() as $the_post) {
						$attachementKey = (empty($attachement)) ? -1 : key($attachement);
						$search_results['results'][] = array(
							'title' => $the_post->post_title,
							'text' => substr($the_post->post_content, 0, 30) . ' <small>(Read More...)</small>',
							'url' => get_permalink( $the_post->ID ),
							'type' => $the_post->post_type,
							'image' => wp_get_attachment_url( get_post_thumbnail_id($the_post->ID) )
						);
					}
					$search_results['total'] = $search_query->found_posts;
					$results = array(
						'status' => 1,
						'response' => $search_results
					);
				} else {
					$results = array(
						'status' => 1,
						'response' => 'Sorry, No match found.'
					);
				}
		 
			} else {
				$results = array(
					'status' => 1,
					'response' => 'Sorry, No match found.'
				);
			}
			echo json_encode( $results );
			exit;
		}
        
		function irbAsGetSettings(){
			$settings = $this->root->db()->getOption($this->root->settingsOption);
			$default = array(
				'irb_as_display_records' => 10,
				'irb_as_min_char_req' => 3,
				'irb_as_template' => 'template1',
				'irb_as_searchFrom' => array('post', 'page', 'product'),
				'irb_as_postStatus' => array('publish')
			);
			$settings = ((!$settings) ? array() : json_decode($settings, true));
			$settings = wp_parse_args($settings, $default);
			return $settings;
		}
		
        function irbAsSaveSettings(){
            global $irbdb;
            $args = $_REQUEST;
			if(!isset($args[$this->root->prefix . '_field']) || !wp_verify_nonce($args[$this->root->prefix . '_field'], $this->root->prefix . '_action'))
				return false;
            $metaData = array();
            foreach($args as $key => $value) {
				if(is_int(strpos($key, $this->root->prefix)) && $key != $this->root->prefix . '_field')
					$metaData[$key] = $value;
			}
			$check = $this->root->db()->setOption($this->root->settingsOption, json_encode($metaData));
            if($check) {
                $result = array('status' => 1, 'response' => 'Settings has been saved successfully.');
            } else {
                $result = array('status' => 0, 'response' => 'Error while saving changes');
            }
			$this->root->requestResult = $result;
        }
        
        //Other functions
        function irbScExpireCookie($name) {
            $cookie = $this->irbScSetCookie($name, '', time()-3600);
            return $cookie;
        }
        
        function irbScSetCookie($name, $value, $expiry=null) {
            $value = $this->root->handler()->encodeString($value);
            $expiry = (is_null($expiry)) ? $this->root->cookieExpiry : $expiry;
            $cookie = setcookie($name, $value, $expiry, '/');
            return $cookie;
        }
        
        function irbScGetCookie($name) {
            $value = (isset($_COOKIE[$name])) ? $_COOKIE[$name] : false;
            if(!is_bool($value))
                $value = $this->root->handler()->decodeString($value);
            return $value;
        }
        
        function irbScSupportMail(){
            $args = $_REQUEST;
            $args['support_puid'] = $this->root->app_uniqueid;
            $args['support_lc'] = $this->root->licenseCode;
            $reqData = $this->root->handler()->preparingRequestData($args);
            $url = $this->root->apiUrl . 'support/submitMessage/' . $reqData;
            $data = $this->root->handler()->requestUrl($url);
            if($data == 1) {
                $this->root->handler()->setMessage('success', 'SUPPORT_SUCCESS_MSG');
                $result = '1|' . $this->root->handler()->getMessage();
            } else {
                $this->root->handler()->setMessage('error', 'SUPPORT_FAILED_MSG');
                $result = '0|' . $this->root->handler()->getMessage();
            }
            echo $result;
            exit;
        }
    }
endif;
