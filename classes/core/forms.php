<?php

if(!function_exists('irbOrientateFields')):
    function irbOrientateFields($fieldsArray, $title=true, $fieldsCol=''){
        $form = '';
        foreach($fieldsArray as $name => $properties){
            //adding required things in properties attr
            $properties['name'] = $name;
            $properties['value'] = (isset($properties['value'])) ? $properties['value'] : '';
            $properties['attr']['class'] = (isset($properties['attr']['class'])) ? 'form-control ' . $properties['attr']['class'] : 'form-control';
            $properties['attr']['placeholder'] = (isset($properties['attr']['placeholder'])) ? $properties['attr']['placeholder'] : (isset($properties['title']) ? $properties['title'] : '');
            $properties['attr'] = array_merge($properties['attr'], (empty($properties['verify']) ? array() : $properties['verify']));
            $cols = (isset($properties['col'])) ? $properties['col'] : '';
            
            $attributes = '';
            foreach($properties['attr'] as $attrKey => $attrValue){
                $attributes .= $attrKey . '="' . $attrValue . '" '; 
            }
            $properties['attr'] = $attributes;
            if(isset($properties['btnAttr'])){
                $attributes = '';
                foreach($properties['btnAttr'] as $attrKey => $attrValue){
                    $attributes .= $attrKey . '="' . $attrValue . '" '; 
                }
                $properties['btnAttr'] = $attributes;
            }
            $fieldFunc = 'irb' . ucfirst($properties['type']) . 'Field';
            
            if(!in_array($properties['type'], array('hidden'))){
                $form .= '<div class="form-group has-feedback ' . $fieldsCol . ' ' . $name . '-field">';
                    if($title) 
                        $form .= '<label for="' . $name . '">' . $properties['title'] . ((isset($properties['verify']['required'])) ? ' *' : '') . '</label>';
                    $form .= '<div class="' . $cols . '">';
                        $form .= $fieldFunc($properties);
                        if(isset($properties['icon']))
                            $form .= '<span class="' . $properties['icon'] . '"></span>';
                    $form .= '</div>';
                    if(isset($properties['helpText']))
                        $form .= '<div class="helpText">' . $properties['helpText'] . '</div>';
                $form .= '</div>';
                
            } else {
                $form .= $fieldFunc($properties);
            }
        }
        return $form;
    }
endif;

if(!function_exists('irbTextField')):
    function irbTextField($args){
        extract($args);
        return '<input type="text" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbHiddenField')):
    function irbHiddenField($args){
        extract($args);
        return '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbPasswordField')):
    function irbPasswordField($args){
        extract($args);
        return '<input type="password" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbTextareaField')):
    function irbTextareaField($args){
        extract($args);
        return '<textarea id="' . $name . '" name="' . $name . '" ' . $attr . '>' . $value . '</textarea>';
    }
endif;

if(!function_exists('irbSwitchField')):
    function irbSwitchField($args){
        extract($args);
        if(!empty($value))
            $attr .= ' checked="checked"';
        return '<input data-toggle="toggle" type="checkbox" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbBtnTextField')):
    function irbBtnTextField($args){
        extract($args);
        return '<input type="text" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' /><a href="' . $btnLink . '" class="' . $btnClass . '" ' . $btnAttr . '>' . $btnText . '</a>';
    }
endif;

if(!function_exists('irbRadioField')):
    function irbRadioField($args){
        extract($args);
        $field = '';
        if(is_array($options) && count($options) > 0):
            foreach($options as $radioKey=> $radioValue):
                $field .= '<label><input type="radio" id="' . $name . '" name="' . $name . '" value="' . $radioKey .'" ' . $attr . (($radioKey == $value) ? ' checked="checked"' : '') . ' />' . $radioValue . '</label>';
            endforeach;
        endif;
        return $field;
    }
endif;

if(!function_exists('irbCheckboxField')):
    function irbCheckboxField($args){
        extract($args);
        $field = '';
        if(is_array($options) && count($options) > 0):
            $value = (is_array($value) ? $value : explode(',', $value));
            foreach($options as $radioKey=> $radioValue):
                $field .= '<label><input type="checkbox" id="' . $name . '" name="' . $name . '" value="' . $radioKey .'" ' . $attr . ((is_int(array_search($radioKey, $value))) ? ' checked="checked"' : '') . ' />' . $radioValue . '</label>';
            endforeach;
        endif;
        return $field;
    }
endif;

if(!function_exists('irbSelectField')):
    function irbSelectField($args){
        extract($args);
        $field = '<select id="' . $name . '" name="' . $name . '" ' . $attr . '>';
        if(is_array($options) && count($options) > 0):
            $value = explode(',', (isset($value) ? $value : ''));
            foreach($options as $radioKey=> $radioValue){
                $field .= '<option value="' . $radioKey .'" ' . ((is_int(array_search($radioKey, $value))) ? ' selected="selected"' : '') . '>' . $radioValue . '</option>';
            }
        endif;
        $field .= '</select>';
        return $field;
    }
endif;

if(!function_exists('irbNumberField')):
    function irbNumberField($args){
        extract($args);
        return '<input type="number" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbDateField')):
    function irbDateField($args){
        extract($args);
        return '<input type="date" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbColorField')):
    function irbColorField($args){
        extract($args);
        return '<input type="color" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbRangeField')):
    function irbRangeField($args){
        extract($args);
        return '<input type="range" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbMonthField')):
    function irbMonthField($args){
        extract($args);
        return '<input type="month" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbWeekField')):
    function irbWeekField($args){
        extract($args);
        return '<input type="week" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbTimeField')):
    function irbTimeField($args){
        extract($args);
        return '<input type="time" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbDatetimeField')):
    function irbDatetimeField($args){
        extract($args);
        return '<input type="datetime" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbDatetimeLocalField')):
    function irbDatetimeLocalField($args){
        extract($args);
        return '<input type="datetime-local" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbEmailField')):
    function irbEmailField($args){
        extract($args);
        return '<input type="email" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbSearchField')):
    function irbSearchField($args){
        extract($args);
        return '<input type="search" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbTelField')):
    function irbTelField($args){
        extract($args);
        return '<input type="tel" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbUrlField')):
    function irbUrlField($args){
        extract($args);
        return '<input type="url" id="' . $name . '" name="' . $name . '" value="' . $value .'" ' . $attr . ' />';
    }
endif;

if(!function_exists('irbValidateFields')):
    function irbValidateFields($formFields){
        $result = array();
        foreach($formFields as $name => $properties):
            $error = array();
            $verify = $properties['verify'];
            $value = $properties['value'];
            $error['title'] = $properties['title'];
            
            if(!empty($verify['required']) && (empty($value))){
                $error['heading'] = 'REQUIRED_ERROR_HEADING';
                $error['msg'] = 'REQUIRED_ERROR_MSG';
            
            } 
            if(!empty($verify['verify_pass']) && ($value <> $formFields[$verify['verify_pass']]['value'])){
                $error['otherField'] = $verify['verify_pass'];
                $error['heading'] = 'VERIFYPASS_ERROR_HEADING';
                $error['msg'] = 'VERIFYPASS_ERROR_MSG';
            
            } 
            if(!empty($verify['integer']) && (!is_int((int)$value))){
                $error['heading'] = 'INT_ERROR_HEADING';
                $error['msg'] = 'INT_ERROR_MSG';
            
            } 
            if(!empty($verify['string']) && (!is_string($value))){
                $error['heading'] = 'STRING_ERROR_HEADING';
                $error['msg'] = 'STRING_ERROR_MSG';
            
            } 
            if(!empty($verify['max']) && $verify['max'] > $value){
                $error['otherField'] = $formFields[$name]['attr']['max'];
                $error['heading'] = 'MAXIMUM_ERROR_HEADING';
                $error['msg'] = 'MAXIMUM_ERROR_MSG';
            
            } 
            if(!empty($verify['min']) && $verify['min'] > $value){
                $error['otherField'] = $formFields[$name]['attr']['min'];
                $error['heading'] = 'MINIMUM_ERROR_HEADING';
                $error['msg'] = 'MINIMUM_ERROR_MSG';
                
            }
            
            if(count($error) > 1)
                $result[$name] = $error;
            
        endforeach;
        return (empty($result)) ? true : $result;
    }
endif;

if(!function_exists('irbOrientateError')):
    function irbOrientateError($errorList, $headingReq=false){
        $list = '<ul class="errorList">';
        foreach($errorList as $name => $error){
            $list .= '<li for="' . $name . '">';
            if($headingReq === true) $list .= '<strong>' . $error['heading'] . '</strong> ';
            if(isset($error['otherField'])){
                $list .= sprintf(__($error['msg']), $error['title'], $error['otherField']);
            } else {
                $list .= sprintf(__($error['msg']), $error['title']);
            }
            $list .= '</li>';
        }
        $list .= '</ul>';
        return $list;
    }
endif;
