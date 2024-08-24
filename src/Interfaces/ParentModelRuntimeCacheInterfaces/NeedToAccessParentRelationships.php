<?php

namespace RuntimeCaching\Interfaces\ParentModelRuntimeCacheInterfaces;

interface NeedToAccessParentRelationships
{
    /**
     * @return array
     * Returned array must be like :
     * [  "client" => Client::class ] ... where :
     * client : is the parent relationships name
     */
    public function getParentRelationshipsDetails() : array ;
}
