<?php

class CDNRewriteRequestFilter implements RequestFilter {

	/**
	 * Enable rewriting of asset urls
	 * @var bool
	 */
	private static $cdn_rewrite = false;

	/**
	 * The cdn domain incl. protocol
	 * @var string
	 */
	private static $cdn_domain = 'http://cdn.mysite.com';

	/**
	 * Filter executed before a request processes
	 *
	 * @param SS_HTTPRequest $request Request container object
	 * @param Session $session Request session
	 * @param DataModel $model Current DataModel
	 * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
	 */
	public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
	{
		return true;
	}

	/**
	 * Filter executed AFTER a request
	 *
	 * @param SS_HTTPRequest $request Request container object
	 * @param SS_HTTPResponse $response Response output object
	 * @param DataModel $model Current DataModel
	 * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
	 */
	public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
	{
		if (!Config::inst()->get('CDNRewriteRequestFilter', 'cdn_rewrite')) {
			return true;
		}

		if (Versioned::current_stage() == 'dev') {
			return true;
		}

		$body = $response->getBody();
		$response->setBody(self::replaceCDN($body));
		return true;

	}

	/**
	 * replaces links to assets in src and href attributes to point to a given cdn domain
	 *
	 * @param $body
	 * @return mixed|void
	 */
	static function replaceCDN($body)
	{
		$cdn = Config::inst()->get('CDNRewriteRequestFilter','cdn_domain');


		$body = str_replace('src="/assets/', 'src="' . $cdn . '/assets/', $body);
		$body = str_replace('src=\"/assets/', 'src=\"' . $cdn . '/assets/', $body);
		$body = str_replace('href="/assets/', 'href="' . $cdn . '/assets/', $body);
		$body = str_replace(Director::absoluteBaseURL() . 'assets/', $cdn . '/assets/', $body);

		return $body;
	}

}