<?php
/**
 * Jibres Domain PHP SDK
 * This function work by version 10 of jibres core api
 * @see https://core.jibres.com/r10/doc
 */

class jibres_domain
{
	private function run($_path, $_method, $_data = null)
	{

		$appkey    = '[YOUR APP KEY]';
		$apikey    = '[YOUR API KEY]';
		$registrar = '[Domain registrar]';
		$master_url = "https://core.jibres.com/%s/%s/%s";


		$url = sprintf($master_url, 'r10', $registrar, $_path);

		$header =
		[
        	'Content-Type:application/json',
        	'appkey: '. $appkey,
        	'apikey: '. $apikey,
		];


		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, mb_strtoupper($_method));
		curl_setopt($ch, CURLOPT_URL, $url);

		if($_data)
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_data, JSON_UNESCAPED_UNICODE));
		}


		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = curl_exec($ch);

		$CurlError = curl_error($ch);

		curl_close ($ch);

		if(!$response)
		{
			return false;
		}

		$response = json_decode($response, true);

		if(isset($response['result']))
		{
			return $response['result'];
		}

		return $response;
	}

	/**
	 * get contact list
	 */
	public function contact_fetch()
	{
		$result = self::run('contact/fetch', 'get');
		return $result;
	}

}
?>