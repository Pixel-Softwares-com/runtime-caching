<?php

namespace RuntimeCaching\RuntimeCacheTypes;


use Exception;
use RuntimeCaching\Helpers\Helpers;

/**
 * This Abstract Class Present An Interface For Handling Runtime Caching Operations :
 * - Runtime Caching : is to save values in runtime only ... without saving them in database or redis or cache file (Only During The Request)
 * - It Applies The Fly Weight Design Pattern : To save values and reusing them later without needing to recreate them again.
 * - It Applies The Singleton Design Pattern : To Access The Same Cache From Anywhere
 */
abstract class RuntimeCache
{
    protected array $cache = [];

    protected function __construct() { }
    abstract static public function singleton() : RuntimeCache;

    protected function processValueToGet(mixed $value) : mixed
    {
        return $value;
    }
    protected function processValueToStore(mixed $value) : mixed
    {
        return $value;
    }

    protected function getKey(int | string $key) : string
    {
        return md5($key);
    }

    protected function getAllowedValueTypes() : array
    {
        return ["boolean",  "integer",  "double",  "string",  "array",  "object"];
    }

    protected function checkKeyValueType($value) : bool
    {
        return in_array(gettype($value) , $this->getAllowedValueTypes() );
    }

    /**
     * @param $value
     * @return void
     * @throws Exception
     */
    protected function ValidateKeyValue($value) : void
    {
        if(!$this->checkKeyValueType($value))
        {
            $exceptionClass = Helpers::getExceptionClass();
            throw new $exceptionClass("Failed To Add The Given Key's Value To Run Time Cache , Value Is Invalid");
        }
    }

    /**
     * @param string|int $key
     * @param $value
     * @return $this
     * @throws Exception
     */
    public function add(string | int $key , $value) : self
    {
        $this->ValidateKeyValue($value);

        /** If No Validation Exception Is Thrown .... The Value Will Be Added To Run Time Cache*/
        $this->cache[ $this->getKey($key) ] = $this->processValueToStore($value);
        return $this;
    }

    public function remove(string | int  $key) : bool
    {
        $key = $this->getKey($key);
        if(array_key_exists($key , $this->cache))
        {
            unset($this->cache[$key]);
            return true;
        }
        return false;
    }

    /**
     * @param string|int $key
     * @return bool
     * @throws Exception
     */
    public function removeOrFail(string | int  $key) : bool
    {
        if(!$this->remove($key))
        {
            $exceptionClass = Helpers::getExceptionClass();
            throw new $exceptionClass("Failed To Remove The Given Key's Cache Value , It Doesn't Have Any Value In The Run Time Cache");
        }
        return true;
    }

    /**
     * @param string|int $key
     * @return mixed
     */
    public function get(string | int $key) : mixed
    {
        $cachedValue = $this->cache[ $this->getKey($key) ] ?? null;
        return $this->processValueToGet($cachedValue);
    }

    /**
     * @param string|int $key
     * @return mixed
     * @throws Exception
     */
    public function getOrFail(string | int $key) :mixed
    {
        $value = $this->get($key);
        if($value){ return $value; }
        $exceptionClass = Helpers::getExceptionClass();
        throw new $exceptionClass("The Given Key Doesn't Have Any Value In The Run Time Cache");
    }

    /**
     * @return array
     */
    public function getCache(): array
    {
        return $this->cache;
    }
}
