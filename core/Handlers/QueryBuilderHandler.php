<?php

namespace Core\Handlers;

use App\Interfaces\BuilderInterface;
use App\Traits\BuilderTrait;
use App\Traits\CrudTrait;

class QueryBuilderHandler implements BuilderInterface
{
    use BuilderTrait;
    use CrudTrait;
}