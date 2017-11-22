<?php

namespace Municipio\Controller;

class E404 extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $searchKeyword = $_SERVER['REQUEST_URI'];
        $searchKeyword = str_replace('/', ' ', $searchKeyword);
        $searchKeyword = trim($searchKeyword);

        $this->data['keyword'] = $searchKeyword;


        //Checks if attempt to access event.

        $urlPaths = explode('/', $_SERVER['REQUEST_URI']);
		$firstPath = $urlPaths[1];

		if($firstPath == "event")
		{
			$this->data['is_event'] = TRUE;
			
			//Sends status 200 to prevent Google Search Console 404-error.
			http_response_code(200);

		
			//Link to archive for events .
			$url = get_post_type_archive_link( 'event' );

			$this->data['event_redirect'] = $url;
			


		}
    }
}
