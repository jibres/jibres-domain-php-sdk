<?php
/**
 * Jibres Domain PHP SDK
 * This function work by version 10 of jibres core api
 * @see https://core.jibres.com/r10/doc
 */
class Jibres_domain
{

	private $result_raw = [];


	private function run($_path, $_method, $_param = null, $_body = null, $_option = [])
	{
		$appkey     = '[YOUR APP KEY]';
		$apikey     = '[YOUR API KEY]';
		$registrar  = '[Domain registrar]';
		$master_url = "https://core.jibres.com/%s/%s/%s";

		$default_option =
		[
			'apikey' => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		// set headers
		$header   = [];
		$header[] = 'Content-Type:application/json';
		$header[] = 'appkey: '. $appkey;

		// set header apikey if need
		if($_option['apikey'])
		{
			$header[] = 'apikey: '. $apikey;
		}


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
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
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

		$this->result_raw = $result;

		$this->make_notif();

		if(!$result['ok'])
		{
			// build error
			return false;
		}

		if(isset($result['result']))
		{
			return $result['result'];
		}

		return false;
	}


	private function make_notif()
	{
		if(empty($this->result_raw))
		{
			return;
		}

		$msg = [];

		if(isset($this->result_raw['msg']) && is_array($this->result_raw['msg']))
		{
			$msg = $this->result_raw['msg'];
		}

		$notif = [];

		foreach ($msg as $key => $value)
		{
			if(isset($value['text']) && isset($value['type']) && in_array($value['type'], ['info','ok','warn','error']))
			{
				// make notif
			}
		}
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


	// ---------------------------------------- CONTACT ---------------------------------------- //
	public function contact_fetch()
	{
		$result = self::run('contact/fetch', 'get');
		return $result;
	}


	public function contact_fetch_all()
	{
		$result = self::run('contact/fetch', 'get', ['all' => true]);
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


	// ---------------------------------------- DOMAIN ---------------------------------------- //

	public function domain_check($_domin)
	{
		$result = self::run('domain/check', 'get', ['domain' => $_domin], null, ['apikey' => false]);
		return $result;
	}


	public function domain_fetch($_param)
	{
		$result = self::run('domain/fetch', 'get', $_param);
		return $result;
	}


	public function domain_available($_domin)
	{
		$result = self::run('domain/available', 'get', ['domain' => $_domin], null, ['apikey' => false]);
		return $result;
	}


	public function domain_detail($_domin)
	{
		$result = self::run('domain/detail', 'get', ['domain' => $_domin]);
		return $result;
	}


	public function domain_autorenew($_domin_id, $_autorenew)
	{
		$result = self::run('domain', 'patch', ['id' => $_domin_id], ['autorenew' => $_autorenew]);
		return $result;
	}


	public function domain_update_holder($_domin_id, $_args)
	{
		$result = self::run('domain/holder', 'patch', ['id' => $_domin_id], $_args);
		return $result;
	}


	public function domain_update_dns($_domin_id, $_args)
	{
		$result = self::run('domain/dns', 'patch', ['id' => $_domin_id], $_args);
		return $result;
	}


	public function domain_delete($_domin_id)
	{
		$result = self::run('domain', 'delete', ['id' => $_domin_id]);
		return $result;
	}


	public function domain_lock($_domin_id)
	{
		$result = self::run('domain', 'put', ['id' => $_domin_id], ['lock' => true]);
		return $result;
	}


	public function domain_unlock($_domin_id)
	{
		$result = self::run('domain', 'put', ['id' => $_domin_id], ['lock' => false]);
		return $result;
	}


	public function domain_action($_domin_id)
	{
		$result = self::run('domain/action', 'get', ['id' => $_domin_id]);
		return $result;
	}


	public function domain_buy($_args)
	{
		$result = self::run('domain/buy', 'post', null, $_args);
		return $result;
	}

	public function domain_renew($_args)
	{
		$result = self::run('domain/renew', 'post', null, $_args);
		return $result;
	}


	public function domain_transfer($_args)
	{
		$result = self::run('domain/transfer', 'post', null, $_args);
		return $result;
	}


	public function domain_history()
	{
		$result = self::run('domain/history', 'get');
		return $result;
	}


	// ---------------------------------------- POLL ---------------------------------------- //
	public function poll_fetch()
	{
		$result = self::run('poll', 'get');
		return $result;
	}

	// ---------------------------------------- DNS ---------------------------------------- //
	public function dns_fetch()
	{
		$result = self::run('dns/fetch', 'get');
		return $result;
	}


	public function dns_fetch_all()
	{
		$result = self::run('dns/fetch', 'get', ['all' => true]);
		return $result;
	}


	public function dns_load($_id)
	{
		$result = self::run('dns', 'get', ['id' => $_id]);
		return $result;
	}


	public function dns_remove($_id)
	{
		$result = self::run('dns', 'delete', ['id' => $_id]);
		return $result;
	}


	public function dns_edit($_args, $_id)
	{
		$result = self::run('dns', 'patch', ['id' => $_id], $_args);
		return $result;
	}


	public function dns_create($_args)
	{
		$result = self::run('dns/create', 'post', null, $_args);
		return $result;
	}

}
?>