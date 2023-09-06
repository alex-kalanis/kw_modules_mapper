<?php

namespace kalanis\kw_modules_mapper\ModulesLists\Mapper;


/**
 * Class Translate
 * @package kalanis\kw_modules_mapper\Lists\Mapper
 * Which column in mapper is mapped on which record key
 */
class Translate
{
    public function getName(): string
    {
        return 'name';
    }

    public function getLevel(): string
    {
        return 'level';
    }

    public function getParams(): string
    {
        return 'params';
    }

    public function getEnabled(): string
    {
        return 'enabled';
    }
}
