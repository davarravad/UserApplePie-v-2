<?php
/**
* Model to get all pages Data from pages database
*/

namespace Models;

class LoadPages extends \Core\Model {

  /**
	 * Get page title from database
	 */
	public function getPageTitle($where_page){
    echo "$where_page";
		$data = $this->db->select('SELECT page_title FROM '.PREFIX.'pages WHERE page_url = :page_url',
			array(':page_url' => $where_page));
    if(count($data) > 0){
		    return $data[0]->page_title;
    }else{
        return "Page Error";
    }
	}

  /**
	 * Get page content from database
	 */
	public function getPageContent($where_page){
		$data = $this->db->select('SELECT page_content FROM '.PREFIX.'pages WHERE page_url = :page_url',
			array(':page_url' => $where_page));
      if(count($data) > 0){
  		    return $data[0]->page_content;
      }else{
          return "Page Error - The Page You Requested Does Not Exist.";
      }
	}

}
