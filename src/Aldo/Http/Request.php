<?php
namespace Aldo\Http;

/**
* Request class for making HTTP Web Requests
*/
class Request
{
	/**
	 * @var string URL being used for current request
	 */
	private $_url;

	/**
	 * @var Curl Handle being used for current request
	 */
	private $_handle;

	/**
	 * @var array Curl options for current CURL request
	 */
	private $_options;

	/**
	 * Initialize the CURL request with provided parameters
	 * @param string 	$url 		URL to be used with CURL
	 * @param array 	$options	Options for CURL
	 */
	public function __construct($url, $options = array())
	{
		// check if there was a custom options provided first
		if(empty($options)) {
			$options = array(
				CURLOPT_FOLLOWLOCATION 	=> true,
				CURLOPT_RETURNTRANSFER 	=> true,
				CURLOPT_URL				=> $url
			);
		}

		$this->_url 	= $url;
		$this->_options = $options;

		$this->_createNewHandle();
	}

	/**
	 * Fetch the requested page using the URL provided
	 * @return string
	 */
	public function fetch()
	{
		$results = $this->_createCurlRequest();

		return $results;
	}

	/**
	 * @param string $url URL for CURL request
	 */
	public function setUrl($url)
	{
		$this->_url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @param array $options CURL options
	 */
	public function setOptions(array $options)
	{
		$this->_options = $options;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Create a new handle for CURL
	 */
	private function _createNewHandle()
	{
		$this->_handle = curl_init();
	}

	/**
	 * Create the CURL request using options and URL
	 * @return string
	 */
	private function _createCurlRequest()
	{
		curl_setopt_array($this->_handle, $this->_options);

		$results = curl_exec($this->_handle);

		if(curl_error($this->_handle)) {
			// handle error
		}

		curl_close($this->_handle);

		return $results;
	}
}