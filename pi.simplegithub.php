<?php

	$plugin_info = array(
	  'pi_name' => 'CKI Simple Github',
	  'pi_version' =>'1.0',
	  'pi_author' =>'Christopher Imrie',
	  'pi_author_url' => 'http://www.christopherimrie.com/',
	  'pi_description' => 'Returns an unordered list of all github repositories for the authenticating user',
	  );

class Simplegithub
{
	var $username 		= '';
	var $token			= '';
	var $return_data 	= '';
	var $xml 			= '';
	var $data 			= array();
	
	function Simplegithub()
	{
		$this->EE =& get_instance();
		
		if($this->username = $this->EE->TMPL->fetch_param('username') AND $this->token = $this->EE->TMPL->fetch_param('token'))
		{
			$this->fetch();
			$this->parse();
			$this->format_ul();
		}else{
			return NULL;
		}
		
	}
	
	private function fetch()
	{
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, 'http://github.com/api/v2/xml/repos/show/'.$this->username); 
		curl_setopt($ch, CURLOPT_POSTFIELDS,'login='.$this->username.'&token='.$this->token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

		$this->xml = curl_exec($ch);
		curl_close($ch);
		
		$this->parse();
	}
	
	private function parse(){
		$xmlObject = simplexml_load_string($this->xml);

		foreach($xmlObject->repository as $repo){
			$this->data[(string)$repo->name]['name'] = (string)$repo->name;
			$this->data[(string)$repo->name]['description'] = (string)$repo->description;
			$this->data[(string)$repo->name]['url'] = (string)$repo->url;
		}
	}
	
	private function format_ul()
	{
		if(count($this->data) > 0)
		{
			$this->return_data = "<ul class='github-repos'>";
			
			foreach($this->data as $repo)
			{
				$this->return_data .= "<li><h5><a href='{$repo['url']}'>{$repo['name']}</a></h5><br/>
										<p>{$repo['description']}</p></li>";
			}
			
			$this->return_data .= "</ul>" ;
		}
	}

}