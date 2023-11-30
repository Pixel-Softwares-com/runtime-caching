<?php

namespace RuntimeCaching\RuntimeCacheTypes;

use Exception;
use RuntimeCaching\Helpers\Helpers;
use RuntimeCaching\Interfaces\ParentModelRuntimeCacheInterfaces\NeededFromChildes;
use RuntimeCaching\Interfaces\ParentModelRuntimeCacheInterfaces\NeedToAccessParentRelationships;
use Illuminate\Database\Eloquent\Model;

class ParentModelRuntimeCache extends RuntimeCache
{
    static protected ?ParentModelRuntimeCache $instance = null;

    static public function singleton() : RuntimeCache
    {
        if(!static::$instance)
        {
            static::$instance = new static();
        }
        return static::$instance;
    }

    static public function NeededFromChildes(Model $model) : bool
    {
        return $model instanceof NeededFromChildes;
    }

    static public function NeedToAccessParentRelationships(Model $model) : bool
    {
        return $model instanceof NeedToAccessParentRelationships;
    }

    /**
     * @param $model
     * @return void
     * @throws Exception
     */
    protected function checkModelValue($model) : void
    {
        if(!$model instanceof Model)
        {
            $exceptionClass = Helpers::getExceptionClass();
            throw new $exceptionClass("Can't Save non Model Instance In The Run Time Cache");
        }

        if(!$this::NeededFromChildes($model))
        {
            $exceptionClass = Helpers::getExceptionClass();
            throw new $exceptionClass("Can't Save The Given Model Instance In The Run Time Cache , It Is Not Needed To Access By Its Child");
        }
    }

    protected function getAllowedValueTypes() : array
    {
        return ["object"];
    }

    protected function ValidateKeyValue($value): void
    {
        parent::ValidateKeyValue($value);
        $this->checkModelValue($value);
    }

}
