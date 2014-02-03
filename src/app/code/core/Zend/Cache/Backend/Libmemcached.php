<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Libmemcached.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * @see Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/ExtendedInterface.php';

/**
 * @see Zend_Cache_Backend
 */
require_once 'Zend/Cache/Backend.php';


/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_Libmemcached extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Default Server Values
     */
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT =  11211;
    const DEFAULT_WEIGHT  = 1;
    
    /**
     * Available options
     *
     * =====> (array) servers :
     * an array of memcached server ; each memcached server is described by an associative array :
     * 'host' => (string) : the name of the memcached server
     * 'port' => (int) : the port of the memcached server
     * 'weight' => (int) : number of buckets to create for this server which in turn control its
     *                     probability of it being selected. The probability is relative to the total
     *                     weight of all servers.
     * =====> (array) client :
     * an array of memcached client options ; the memcached client is described by an associative array :
     * @see http://php.net/manual/memcached.constants.php
     * - The option name can be the name of the constant without the prefix 'OPT_'
     *   or the integer value of this option constant
     *
     * @var array available options
     */
    protected $_options = array(
        'servers' => array(array(
            'host'   => self::DEFAULT_HOST,
            'port'   => self::DEFAULT_PORT,
            'weight' => self::DEFAULT_WEIGHT,
        )),
        'client' => array()
    );

    /**
     * Memcached object
     *
     * @var mixed memcached object
     */
    protected $_memcache = null;

    /**
     * Constructor
     *
     * @param array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('memcached')) {
            Zend_Cache::throwException('The memcached extension must be loaded for using this backend !');
        }

        // override default client options
        $this->_options['client'] = array(
            Memcached::OPT_DISTRIBUTION         => Memcached::DISTRIBUTION_CONSISTENT,
            Memcached::OPT_HASH                 => Memcached::HASH_MD5,
            Memcached::OPT_LIBKETAMA_COMPATIBLE => true
        );

        parent::__construct($options);

        if (isset($this->_options['servers'])) {
            $value = $this->_options['servers'];
            if (isset($value['host'])) {
                // in this case, $value seems to be a simple associative array (one server only)
                $value = array(0 => $value); // let's transform it into a classical array of associative arrays
            }
            $this->setOption('servers', $value);
        }
        
        $this->_memcache = new Memcached();

        // setup memcached client options
        foreach ($this->_options['client'] as $name => $value) {
            $optId = null;
            if (is_int($name)) {
                $optId = $name;
            } else {
                $optConst = 'Memcached::OPT_' . strtoupper($name);
                if (defined($optConst)) {
                    $optId = constant($optConst);
                } else {
                    $this->_log("Unknown memcached client option '{$name}' ({$optConst})");
                }
            }
            if ($optId) {
                if (!$this->_memcache->setOption($optId, $value)) {
                    $this->_log("Setting memcached client option '{$optId}' failed");
                }
            }
        }

        // setup memcached servers
        if (!count($this->_memcache->getServerList())) {
        	$servers = array();
        	foreach ($this->_options['servers'] as $server) {
	        	if (!array_key_exists('port', $server)) {
	        		$server['port'] = self::DEFAULT_PORT;
	        	}
	        	if (!array_key_exists('weight', $server)) {
	        		$server['weight'] = self::DEFAULT_WEIGHT;
	        	}
	        	$servers[] = array($server['host'], $server['port'], $server['weight']);
        	}
        	$this->_memcache->addServers($servers);
        }
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $tmp = @$this->_memcache->get($id);
        if (isset($tmp[0])) {
            return $tmp[0];
        }
        return false;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id Cache id
     * @return int|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $tmp = @$this->_memcache->get($id);
        if (isset($tmp[0], $tmp[1])) {
            return (int)$tmp[1];
        }
        return false;
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data             Datas to cache
     * @param  string $id               Cache id
     * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
     * @param  int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean True if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $lifetime = $this->getLifetime($specificLifetime);

        // ZF-8856: using set because add needs a second request if item already exists
        $result = @@$this->_memcache->set($id, array($data, time(), $lifetime), $lifetime);
        if ($result === false) {
            $rsCode = $this->_memcache->getResultCode();
            $rsMsg  = $this->_memcache->getResultMessage();
            $this->_log("Memcached::set() failed: [{$rsCode}] {$rsMsg}");
            return $result;
        }
        foreach ($tags as $tag) {
        	$result = $this->_registerTag($id, $tag) && $result;
        }
        return $result;
    }
    /**
     * Register a cache id with the given tag
     *
     * @param  string $id  Cache id
     * @param  string $tag Tag
     * @return boolean True if no problem
     */
    protected function _registerTag($id, $tag)
    {
    	// load the available tags
    	if (is_array($cacheTags = $this->getTags()) === false) {
    		$cacheTags = array();
    	}
    	// check if the tag has already been available
    	if (array_key_exists($tag, $cacheTags)) {
    		$ids = $cacheTags[$tag];
    		$ids[] = $id;
    	}
    	else {
    		$ids = array($id);
    	}
    	// add the ID to the tag
    	$cacheTags[$tag] = $ids;
    	// store the tag back to the cache
    	return @$this->_memcache->set('internal-tags', $cacheTags);
    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id)
    {
        // load the available tags
        if (is_array($cacheTags = $this->getTags()) === false) {
        	$cacheTags = array();
        }
        // iterate over the tags and check if the passed ID is part of one of the tags
        foreach ($cacheTags as $name => $tag) {
        	// flip the tag array to get the ID's as keys
        	$flippedTag = array_flip($tag);
        	// check if the ID is in the tag
        	if (array_key_exists($id, $flippedTag)) {
        		// remove the ID from the tags
        		unset($flippedTag[$id]);
        		// reset the tags
        		$cacheTags[$name] = array_flip($flippedTag);
        	}
        }
        // store the tag back to the cache
        return @$this->_memcache->set('internal-tags', $cacheTags) && $this->_memcache->delete($id);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => unsupported
     * 'matchingTag'    => unsupported
     * 'notMatchingTag' => unsupported
     * 'matchingAnyTag' => unsupported
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                return $this->_memcache->flush();
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                $this->_log("Zend_Cache_Backend_Libmemcached::clean() : CLEANING_MODE_OLD is unsupported by the Libmemcached backend");
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
                $ids = $this->getIdsMatchingTags($tags);
                $result = true;
                foreach ($ids as $id) {
                    $result = $this->remove($id) && $result;
                }
                return $result;
                break;
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
                $ids = $this->getIdsNotMatchingTags($tags);
                $result = true;
                foreach ($ids as $id) {
                    $result = $this->remove($id) && $result;
                }
                return $result;
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $ids = $this->getIdsMatchingAnyTags($tags);
                $result = true;
                foreach ($ids as $id) {
                    $result = $this->remove($id) && $result;
                }
                return $result;
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }

    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return false;
    }

    /**
     * Set the frontend directives
     *
     * @param  array $directives Assoc of directives
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setDirectives($directives)
    {
        parent::setDirectives($directives);
        $lifetime = $this->getLifetime(false);
        if ($lifetime > 2592000) {
            // #ZF-3490 : For the memcached backend, there is a lifetime limit of 30 days (2592000 seconds)
            $this->_log('memcached backend has a limit of 30 days (2592000 seconds) for the lifetime');
        }
        if ($lifetime === null) {
            // #ZF-4614 : we tranform null to zero to get the maximal lifetime
            parent::setDirectives(array('lifetime' => 0));
        }
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        return $this->_memcache->getAllKeys();
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        return @$this->_memcache->get('internal-tags');
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        $first = true;
        $ids = array();
        foreach ($tags as $tag) {
            $ids2 = array();
            foreach ($this->getTags() as $name => $ids) {
                $ids2[] = $ids;
            }
            if ($first) {
                $ids = $ids2;
                $first = false;
            } else {
                $ids = array_intersect($ids, $ids2);
            }
        }
        $result = array();
        foreach ($ids as $id) {
            $result[] = $id;
        }
        return $result;
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        // initialize the array with the return result
        $result = array();
        // load the cached tags
        $cachedTags = $this->getTags();
        // load the cached ID's
        foreach ($this->getIds() as $id) {
			// initialize the flag if the ID matches a tag
        	$matching = false;
            // iterate over the passed tags
            foreach ($tags as $tag) {
            	// check if the ID is part of a tag
            	if (array_key_exists($name, $cachedTags) && in_array($cachedTags[$name])) {
            		$matching = true;
            	}
            }
            if (!$matching) {
                $result[] = $id;
            }
        }
        return $result;
    }

    /**
     * Return an array of stored cache ids which match any given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of any matching cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array())
    {
    	$first = true;
    	$ids = array();
    	foreach ($tags as $tag) {
    		$ids2 = array();
    		foreach ($this->getTags() as $name => $ids) {
    			$ids2[] = $ids;
    		}
    		if ($first) {
    			$ids = $ids2;
    			$first = false;
    		} else {
    			$ids = array_merge($ids, $ids2);
    		}
    	}
    	$result = array();
    	foreach ($ids as $id) {
    		$result[] = $id;
    	}
    	return $result;
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @throws Zend_Cache_Exception
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        $mems = $this->_memcache->getStats();
        if ($mems === false) {
            return 0;
        }

        $memSize = null;
        $memUsed = null;
        foreach ($mems as $key => $mem) {
            if ($mem === false) {
                $this->_log('can\'t get stat from ' . $key);
                continue;
            }

            $eachSize = $mem['limit_maxbytes'];
            $eachUsed = $mem['bytes'];
            if ($eachUsed > $eachSize) {
                $eachUsed = $eachSize;
            }

            $memSize += $eachSize;
            $memUsed += $eachUsed;
        }

        if ($memSize === null || $memUsed === null) {
            Zend_Cache::throwException('Can\'t get filling percentage');
        }

        return ((int) (100. * ($memUsed / $memSize)));
    }

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array must include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     *
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($id)
    {
        $tmp = @$this->_memcache->get($id);
        if (isset($tmp[0], $tmp[1], $tmp[2])) {
            $data     = $tmp[0];
            $mtime    = $tmp[1];
            $lifetime = $tmp[2];
            return array(
                'expire' => $mtime + $lifetime,
                'tags' => array(),
                'mtime' => $mtime
            );
        }

        return false;
    }

    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime)
    {
        $tmp = @$this->_memcache->get($id);
        if (isset($tmp[0], $tmp[1], $tmp[2])) {
            $data     = $tmp[0];
            $mtime    = $tmp[1];
            $lifetime = $tmp[2];
            $newLifetime = $lifetime - (time() - $mtime) + $extraLifetime;
            if ($newLifetime <=0) {
                return false;
            }
            // #ZF-5702 : we try replace() first becase set() seems to be slower
            if (!($result = $this->_memcache->replace($id, array($data, time(), $newLifetime), $newLifetime))) {
                $result = @$this->_memcache->set($id, array($data, time(), $newLifetime), $newLifetime);
                if ($result === false) {
                    $rsCode = $this->_memcache->getResultCode();
                    $rsMsg  = $this->_memcache->getResultMessage();
                    $this->_log("Memcached::set() failed: [{$rsCode}] {$rsMsg}");
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * Return an associative array of capabilities (booleans) of the backend
     *
     * The array must include these keys :
     * - automatic_cleaning (is automating cleaning necessary)
     * - tags (are tags supported)
     * - expired_read (is it possible to read expired cache records
     *                 (for doNotTestCacheValidity option for example))
     * - priority does the backend deal with priority when saving
     * - infinite_lifetime (is infinite lifetime can work with this backend)
     * - get_list (is it possible to get the list of cache ids and the complete list of tags)
     *
     * @return array associative of with capabilities
     */
    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => false,
            'tags' => true,
            'expired_read' => false,
            'priority' => false,
            'infinite_lifetime' => false,
            'get_list' => false
        );
    }

}
