<?php
class ValvePress_APIFY {
	
	public $token;
	public $link;
	public $ch;
	private $json_raw;
 

	function __construct($token, $link,$ch){
		$this->json_raw =   file_get_contents( dirname(__FILE__) . '/apify-template.json' );
		$this->token = $token;
		$this->link = $link;
		$this->ch = $ch;
		curl_setopt ( $this->ch, CURLOPT_TIMEOUT, 90 );
		
	}
	
	/**
	 * Fetches the content from APIFY.COM
	 * @param string $apify_wait_for_mills - wait for x milliseconds before fetching the content from APIFY default 0
	 * @return string - the content from APIFY.COM
	 * @throws Exception - if the APIFY token is empty or the reply is empty or the reply contains an error or the reply does not contain pageContent 
	 */
	function apify($apify_wait_for_mills = 0){
		
		//empty reply
		if(trim($this->token) == '' ){
			throw new Exception( '<span style="color:red">ERROR: You have enabled the option to use APIFY.COM, please visit the plugin settings page and add the required APIFY API token</span>'   );
		}
		
		
		$json_to_post =str_replace('https://www.example.com', $this->link, $this->json_raw);

		//parse int $api_wait_for_mills
		$apify_wait_for_mills = intval($apify_wait_for_mills);

		//if apify_wait_for_mills is not 0 and is a number 
		if($apify_wait_for_mills != 0 && is_numeric($apify_wait_for_mills) ){
			
			// add await context.waitFor(1000); to the json before const pageTitle
			$json_to_post = str_replace('const pageTitle', 'await context.waitFor(' . $apify_wait_for_mills . ');\n    const pageTitle', $json_to_post);
		
			//echo $json_to_post;exit;
		}


		$curlurl="https://api.apify.com/v2/acts/apify~web-scraper/run-sync-get-dataset-items?token=" . $this->token;
		 
		curl_setopt($this->ch, CURLOPT_URL, $curlurl);
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json_to_post );
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json") );
		$x='error';
		$exec=curl_exec($this->ch);
		$x=curl_error($this->ch);
		
		//empty reply 
		if(trim($exec) == '' ){
			throw new Exception( 'Empty reply from APIFY ' . $x  );
		}
		
		
		$json = json_decode($exec);
		
		
		//error 
		if( isset($json->error)  ){
			throw new Exception( 'Error from APIFY ' . $json->error->message );
		}
		
		 
		//no content pageContent
		if( ! isset($json[0]->pageContent)  ){
			throw new Exception( 'No content returned from APIFY '  );
		}
	 	
		//hotfix for feed encoded html entities &lt;?xml 
		if(stristr( $json[0]->pageContent , '&lt;?xml ')){
			$json[0]->pageContent = html_entity_decode($json[0]->pageContent);
		}
		
		return $json[0]->pageContent;
		 
	}
 
}