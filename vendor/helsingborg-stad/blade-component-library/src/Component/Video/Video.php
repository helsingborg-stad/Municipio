<?php

namespace BladeComponentLibrary\Component\Video;

class Video extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Controls
        if($hasControls) {
            $this->data['controls'] = "controls"; 
        } else {
            $this->data['controls'] = ""; 
        }

        //Muted
        if($isMuted) {
            $this->data['muted'] = "muted"; 
        } else {
            $this->data['muted'] = ""; 
        }

        //Autoplay
        if($shouldAutoplay) {
            $this->data['autoplay'] = "autoplay"; 
        } else {
            $this->data['autoplay'] = ""; 
        }
        
    }
}