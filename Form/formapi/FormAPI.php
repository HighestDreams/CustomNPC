<?php

namespace HighestDreams\CustomNPC\Form\formapi;

class FormAPI {
	
	public function createCustomForm(callable $function = null) : CustomForm {
        $form = new CustomForm($function);
        return $form;
	}
	
	public function createSimpleForm(callable $function = null) : SimpleForm {
     	$form = new SimpleForm($function);
    	return $form;
	}
	
	public function createModalForm(callable $function = null) : ModalForm {
    	$form = new ModalForm($function);
    	return $form;
	}
	
}
