<?php

namespace BladeComponentLibrary\Component\Comment;

class Comment extends \BladeComponentLibrary\Component\BaseController {
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->data['id'] = uniqid("", true);

        $this->is_reply($is_reply);
    }

    /**
	 * Check if comment is a reply
	 */
    public function is_reply($is_reply) {
        if ($is_reply) {
            $this->data['classList'][] = $this->getBaseClass() . '__is-reply';
        }
    }
}
