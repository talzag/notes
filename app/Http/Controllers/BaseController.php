<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    /**
     * Find and return the title of a note using embedded HTML tags
     *
     * @return title
     */
	protected function findTitle($text) {
        $html = str_get_html($text);
        $h1s = $html->find('h1');
        $h2s = $html->find('h2');
        // Look for h1 tags first
        if(count($h1s) > 0) {
            $title = $h1s[0]->innertext;
        // Look for h2 tags second
        } else if(count($h2s) > 0) {
            $title = $h2s[0]->innertext;
        // Else look for anything else that could work
        } else {
            $title = $html->find("h3,p,h4,h5,li")[0]->innertext;
            // Truncate the title if longer than 60 characters
            if (strlen($title) > 60) {
                $title = substr($title, 0, 59).".....s";
            }
        }
        return $title;
	}
}
