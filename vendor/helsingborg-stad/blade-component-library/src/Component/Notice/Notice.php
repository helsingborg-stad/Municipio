<?php

namespace BladeComponentLibrary\Component\Notice;

class Notice extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Message
        if(isset($message) && is_array($message) && !empty($message)){
            $message['text'] = ucfirst($message['text']);
            $this->data['message'] = $message;
        }

        if(isset($message['size'])){
            $this->data['messageSize'] = $this->getBaseClass() . '__message--' . $message['size'];                
        }

        //Set icon size (depending on avatar size)
        if(isset($icon) && is_array($icon) && !empty($icon) && isset($icon['name'])) {
            $this->data['icon'] = $icon;
        }

        //Success
        if($type === 'success') {
            $this->data['classList'][] = $this->getBaseClass() . "--success";                
        }

        //Warning
        if($type === 'warning') {      
            $this->data['classList'][] = $this->getBaseClass() . "--warning"; 
        }

        //Danger
        if($type === 'danger') {
            $this->data['classList'][] = $this->getBaseClass() . "--danger"; 
        }

        //Info
        if($type === 'info') {
            $this->data['classList'][] = $this->getBaseClass() . "--info"; 
        }
    }
}