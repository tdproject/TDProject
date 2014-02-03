<?php

require_once "TechDivision/Lang/Object.php";
require_once "TechDivision/Collections/HashMap.php";
require_once "TechDivision/HttpUtils/Interfaces/Request.php";
require_once "Mock/Session.php";

/**
 * This class is a Mock object for testing purposes only.
 *
 * @package TDProject
 * @author Tim Wagner <tw@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class Mock_Request
	extends TechDivision_Lang_Object
	implements TechDivision_HttpUtils_Interfaces_Request {

	/**
	 * Holds the key for a get request.
	 * @var string
	 */
	public static $REQUEST_METHOD_GET = "GET";

	/**
	 * Holds the key for a post request.
	 * @var string
	 */
	public static $REQUEST_METHOD_POST = "POST";

	/**
	 * Holds the class name of the default session handler.
	 * @var string
	 */
	const HTTP_SESSION_HANDLER = "Mock_Session";

	/**
	 * Holds the class name of the MySQLi based session handler.
	 * @var string
	 */
	const HTTP_MYSQLI_SESSION_HANDLER =
		"Mock_Session";

	/**
	 * The dummy request values.
	 * @var array
	 */
	protected $_request = array();

	/**
	 * The class name for the session handler, defaults to HttpSessionHandler.
	 * @var string
	 */
	private $sessionHandler = self::HTTP_SESSION_HANDLER;

	/**
	 * The arguments for the constructor of the selected session handler.
	 * @var array
	 */
	private $sessionArgs = array();

    /**
     * Holds the actual session object.
     * @var Session
     */
    private $session = null;

	/**
	 * Holds the attributes of the actual request.
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Holds a list with available filters.
	 * @var array
	 */
	private $filterList = array();

    /**
     * Holds the actual HttpRequest instance.
     * @var HttpRequest
     */
    private static $INSTANCE = null;

	/**
	 * Constructor to initialize the internal list
	 * with filters available in the system.
	 *
	 * @return void
	 */
    public function __construct(array $request = array())
    {
        // initialize the dummy request parameters
        $this->_request = $request;
		// get the list with registered filters
		foreach (filter_list() as $filterName) {
			$this->filterList[filter_id($filterName)] = $filterName;
		}
    }

    /**
     * @see Request::getAttribute($name)
     */
    public function getAttribute($name)
    {
		// check if a attribute exists
		if (array_key_exists($name, $this->attributes)) {
			// if yes, return it
			return $this->attributes[$name];
		}
		// else return nothing
		return;
    }

    /**
     * @see Request::setAttribute($name, $attribute)
     */
    public function setAttribute($name, $attribute)
    {
		$this->attributes[$name] = $attribute;
    }

    /**
     * Sets the session handler to use.
     *
     * @param string $sessionHandler The class name of the session handler to use
     * @return void
     */
    public function setSessionHandler($sessionHandler)
    {
    	$this->sessionHandler = $sessionHandler;
    }

    /**
     * Sets the arguments passed to the constructor of
     * the session handler to use.
     *
     * @param array $sessionArgs The arguments passed to the constructor of the session handler
     * @return void
     */
    public function setSessionArgs($sessionArgs)
    {
    	$this->sessionArgs = $sessionArgs;
    }

    /**
     * @see Request::getSession($create = true)
     */
    public function getSession($create = true)
    {
    	// check if the Session is already initialized
		if ($this->session == null && $create) {
		    // if not, initialized it by reflection
			$reflectionClass = new ReflectionClass($this->sessionHandler);
			$this->session = $reflectionClass
			    ->newInstanceArgs($this->sessionArgs);
		}
		// return the initialized Session
		return $this->session;
    }

    /**
     * @see Request::removeAttribute($name)
     */
    public function removeAttribute($name)
    {
		unset($this->attributes[$name]);
	}

	/**
	 * @see Request::getQueryString()
	 */
	public function getQueryString()
	{
		return $this->_request["QUERY_STRING"];
	}

	/**
	 * @see Request::getRequestURI()
	 */
	public function getRequestURI()
	{
		return $this->_request["REQUEST_URI"];
	}

	/**
	 * @see Request::getRequestURL()
	 */
	public function getRequestURL()
	{
		return $this->_request["SCRIPT_NAME"];
	}

	/**
	 * @see Request::getServerName()
	 */
	public function getServerName()
	{
		return $this->_request["SERVER_NAME"];
	}

	/**
	 * @see Request::getServerAddr()
	 */
	public function getServerAddr()
	{
		return $this->_request["SERVER_ADDR"];
	}

	/**
	 * @see Request::getServerPort()
	 */
	public function getServerPort()
	{
		return $this->_request["SERVER_PORT"];
	}

	/**
	 * @see Request::getRequestMethod()
	 */
	public function getRequestMethod()
	{
		return $this->_request["REQUEST_METHOD"];
	}

	/**
	 * @see Request::getRedirectUrl()
	 */
	public function getRedirectUrl()
	{
		return $this->_request["REDIRECT_URL"];
	}

	/**
	 * @see Request::getRemoteHost()
	 */
	public function getRemoteHost()
	{
		// @todo Has to be implemented
		return null;
	}

	/**
	 * @see Request::getRemoteAddr()
	 */
	public function getRemoteAddr()
	{
		return $this->_request["REMOTE_ADDR"];
	}

	/**
	 * @see Request::getScriptFilename()
	 */
	public function getScriptFilename()
	{
		return $this->_request["SCRIPT_FILENAME"];
	}

	/**
	 * @see Request::getScriptName()
	 */
	public function getScriptName()
	{
		return $this->_request["SCRIPT_NAME"];
	}

	/**
	 * @see Request::getUserAgent()
	 */
	public function getUserAgent()
	{
		return $this->_request["HTTP_USER_AGENT"];
	}

	public function getReferer()
	{
		return $this->_request["HTTP_REFERER"];
	}

	/**
	 * @see Request::getParameter($name, $filter = null, $filterOptions = null)
	 */
	public function getParameter($name, $filter = null, $filterOptions = null)
	{
		// get the value
		if(array_key_exists($name, $this->_request)) {
			if(!is_array($this->_request[$name])) {
				// return the filtered / sanitized value if it is not an array
				return $this->filter($name, $filter);
			}
		}
		// else return nothing
		return;
	}

	/**
	 * This method filters the passed value with the PHP filter_var
	 * function and the filter specified as parameter.
	 *
	 * If the specified filter is null, the original untouched
	 * value is returned. If the filter fails, the method returns
	 * false.
	 *
	 * @param string $name Holds the name of the value that should be filtered
	 * @param integer $filter Holds the filter to apply to the value
	 * @param mixed Holds the filter options if available
	 * @throws Exception Is thrown if an invalid filter was specified or if
	 * 		the specified filter can't be applied on the passed value
	 */
	protected function filter($name, $filter = null, $filterOptions = null)
	{
		// return the filtered value
		if (!empty($filter) && !empty($this->_request[$name])) {
			// check if a valid filter was specified
			if (!array_key_exists($filter, $this->filterList)) {
				throw new Exception(
					"Try to apply not existing filter $filter on value " .
				    $this->_request[$name]
				);
			}
			// load the value to filter
			$filteredValue = filter_var(
			    $this->_request[$name],
			    $filter,
			    $filterOptions
			);
			// return the filtered / sanitized value if it is not an array
			if ($filteredValue != false) {
				// return the filtered value
				return $filteredValue;
			}
			// throw an exception if the specified filter can not
			// be applied on the passed value
			throw new Exception(
				"Error when invoking filter " .
			    $this->filterList[$filter] . " on value " .
			    $this->_request[$name]
			);
		}
		// else return the value untouched
		return $this->_request[$name];
	}

	/**
	 * @see Request::getParameterMap()
	 */
	public function getParameterMap()
	{
		// return a HashMap with the request parameters
		return new TechDivision_Collections_HashMap($this->_request);
	}

	/**
	 * @see Request::getParameterNames()
	 */
	public function getParameterNames()
	{
		// return the keys as array
		return array_keys($this->_request);
	}

	/**
	 * @see Request::getParameterValues($name)
	 */
	public function getParameterValues($name)
	{
		// get the value
		if (array_key_exists($name, $this->_request)) {
			if (is_array($this->_request[$name])) {
				// get the value if it is an array
				return $this->_request[$name];
			}
		}
		// check if it is a upload, then get the values
		if (array_key_exists($name, $_FILES)) {
			return $_FILES[$name];
		}
		// else return nothing
		return;
	}

	/**
	 * @see Request::getRequestedSessionId()
	 */
	public function getRequestedSessionId()
	{
		// @todo Still to implement
	}

	/**
	 * @see Request::isRequestedSessionIdValid()
	 */
	public function isRequestedSessionIdValid()
	{
		// @todo Still to implement
	}

	/**
	 * @see Request::isRequestedSessionIdFromURL()
	 */
	public function isRequestedSessionIdFromURL()
	{
		// @todo Still to implement
	}

	/**
	 * @see Request::isRequestedSessionIdFromCookie()
	 */
	public function isRequestedSessionIdFromCookie()
	{
		// @todo Still to implement
	}

	/**
	 * This method sets all items of the internal array
	 * in the global $this->_request variable.
	 *
	 * @return void
	 */
	public function toRequest()
	{
		// add all attributes back to the request
		foreach ($this->attributes as $key => $attribute) {
			$this->_request[$key] = $attribute;
		}
	}
}