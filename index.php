<?php
/**
 * Jibres Domain PHP SDK
 * This function work by version 10 of jibres core api
 * @see https://core.jibres.com/r10/doc
 */

class jibres_domain
{

	private $result_raw = [];


	private function run($_path, $_method, $_param = null, $_body = null)
	{

		$appkey    = '[YOUR APP KEY]';
		$apikey    = '[YOUR API KEY]';
		$registrar = '[Domain registrar]';
		$master_url = "https://core.jibres.com/%s/%s/%s";

		$header =
		[
        	'Content-Type:application/json',
        	'appkey: '. $appkey,
        	'apikey: '. $apikey,
		];

		$url = sprintf($master_url, 'r10', $registrar, $_path);

		if($_param && is_array($_param))
		{
			$url .= '?'. http_build_query($_param);
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, mb_strtoupper($_method));
		curl_setopt($ch, CURLOPT_URL, $url);

		if($_body && is_array($_body))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_body, JSON_UNESCAPED_UNICODE));
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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

		$result = json_decode($response, true);

		if(!is_array($result))
		{
			return false;
		}

		if(!array_key_exists('ok', $result))
		{
			return false;
		}

		if(!$result['ok'])
		{
			// build error
			return false;
		}

		$this->result_raw = $result;

		if(isset($result['result']))
		{
			return $result['result'];
		}

		return false;
	}


	/**
	 * Get result meta
	 *
	 * @param      <type>  $_key   The key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function meta($_key)
	{
		if(isset($this->result_raw['meta'][$_key]))
		{
			return $this->result_raw['meta'][$_key];
		}

		return null;
	}

	/**
	 * get contact list
	 */
	public function contact_fetch()
	{
		$result = self::run('contact/fetch', 'get');
		return $result;
	}


	public function contact_load($_id)
	{
		$result = self::run('contact', 'get', ['id' => $_id]);
		return $result;
	}


	public function contact_remove($_id)
	{
		$result = self::run('contact', 'delete', ['id' => $_id]);
		return $result;
	}


	public function contact_edit($_args, $_id)
	{
		$result = self::run('contact', 'patch', ['id' => $_id], $_args);
		return $result;
	}


	public function contact_add_exists($_contact_id)
	{
		$result = self::run('contact/add', 'post', null, ['contact_id' => $_contact_id]);
		return $result;
	}


	public function contact_create_new($_args)
	{
		$result = self::run('contact/create', 'post', null, $_args);
		return $result;
	}

}
?>