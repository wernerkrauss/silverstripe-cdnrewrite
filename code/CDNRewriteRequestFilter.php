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
	 * Enable rewrite in admin area
	 * @var bool
	 */
	private static $enable_in_backend = false;

	/**
	 * Enable rewrite in dev mode
	 * @var bool
	 */
	private static $enable_in_dev = false;

	/**
	 * Filter executed before a request processes
	 *
	 * @param SS_HTTPRequest $request Request container object
	 * @param Session $session Request session
	 * @param DataModel $model Current DataModel
	 * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
	 */
	public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model) {
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
	public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model) {
		if (!self::isEnabled()) {
			return true;
		}

		$body = $response->getBody();
		$response->setBody(self::replaceCDN($body));

		return true;
	}

	/**
	 * Checks if cdn rewrite is enabled
	 * @return bool
	 */
	static function isEnabled() {
		$general = Config::inst()->get('CDNRewriteRequestFilter', 'cdn_rewrite');
		$notDev = !Director::isDev() || Config::inst()->get('CDNRewriteRequestFilter', 'enable_in_dev');
		$notBackend = !self::isBackend() ||  Config::inst()->get('CDNRewriteRequestFilter', 'enable_in_backend');

		return $general && $notDev && $notBackend;
	}

	/**
	 * Helper method to check if we're in backend (LeftAndMain) or frontend
	 * Controller::curr() doesn't return anything, so i cannot check it...
	 * @return bool
	 */
	static function isBackend() {
		return !Config::inst()->get('SSViewer', 'theme_enabled') || strpos($_GET['url'], 'admin') === 1;
	}

	/**
	 * replaces links to assets in src and href attributes to point to a given cdn domain
	 *
	 * @param $body
	 * @return mixed|void
	 */
	static function replaceCDN($body) {
		$cdn = Config::inst()->get('CDNRewriteRequestFilter','cdn_domain');

		$body = str_replace('src="assets/', 'src="' . $cdn . '/assets/', $body);
		$body = str_replace('src="/assets/', 'src="' . $cdn . '/assets/', $body);
		$body = str_replace('src=\"/assets/', 'src=\"' . $cdn . '/assets/', $body);

		$body = str_replace('href="/assets/', 'href="' . $cdn . '/assets/', $body);
		$body = str_replace(Director::absoluteBaseURL() . 'assets/', $cdn . '/assets/', $body);

		$body = str_replace('src="/themes/', 'src="' . $cdn . '/themes/', $body);
		$body = str_replace('src="' . Director::absoluteBaseURL() . 'themes/', 'src="' . $cdn . '/themes/', $body);

		$body = str_replace('href="/themes/', 'href="' . $cdn . '/themes/', $body);
		$body = str_replace('href="' . Director::absoluteBaseURL() . 'themes/', 'href="' . $cdn . '/themes/', $body);

		return $body;
	}
}
