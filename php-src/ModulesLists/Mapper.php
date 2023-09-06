<?php

namespace kalanis\kw_modules_mapper\ModulesLists;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\Interfaces\Lists;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\ModulesLists\Record;


/**
 * Class Mapper
 * @package kalanis\kw_modules\ModulesLists
 *
 * Because kw_mapper does not know json as storage format, it needs to set the way to pack params
 */
class Mapper implements Lists\IModulesList
{
    /** @var int */
    protected $level = Lists\ISitePart::SITE_NOWHERE;
    /** @var Mapper\Translate */
    protected $translate = null;
    /** @var Lists\File\IParamFormat */
    protected $format = null;
    /** @var ARecord */
    protected $record = null;

    public function __construct(ARecord $record, Mapper\Translate $tr, Lists\File\IParamFormat $format)
    {
        $this->record = $record;
        $this->translate = $tr;
        $this->format = $format;
    }

    public function setModuleLevel(int $level): void
    {
        $this->level = $level;
    }

    public function add(string $moduleName, bool $enabled = false, array $params = []): bool
    {
        try {
            if ($this->get($moduleName)) {
                return false;
            }

            $record = new Record();
            $record->setModuleName($moduleName);
            $record->setEnabled($enabled);
            $record->setParams($params);

            $rec = $this->fillDbRecord($record);
            return $rec->save();

        } catch (MapperException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(string $moduleName): ?Record
    {
        try {
            $rec = clone $this->record;
            $rec->offsetSet($this->translate->getName(), $moduleName);
            $rec->offsetSet($this->translate->getLevel(), $this->level);

            if (!$rec->load()) {
                return null;
            }
            return $this->fillModuleRecord($rec);

        } catch (MapperException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function listing(): array
    {
        try {
            $rec = clone $this->record;
            $rec->offsetSet($this->translate->getLevel(), $this->level);
            $records = array_map([$this, 'fillModuleRecord'], $rec->loadMultiple());
            $recs = array_combine(array_map([$this, 'getRecordName'], $records), $records);
            return (false === $recs) ? [] : $recs;

        } catch (MapperException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function updateBasic(string $moduleName, ?bool $enabled, ?array $params): bool
    {
        try {
            if (!is_null($enabled) || !is_null($params)) {
                $rec = clone $this->record;
                // known things
                $rec->getEntry($this->translate->getName())->setData($moduleName, true);
                $rec->getEntry($this->translate->getLevel())->setData($this->level, true);

                // changed things
                $rec->offsetSet($this->translate->getParams(), is_null($params) ? false : $this->format->pack($params));
                $rec->offsetSet($this->translate->getEnabled(), $enabled);
                return $rec->save();
            }

            // not exists or nothing to change
            return false;

        } catch (MapperException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function updateObject(Record $record): bool
    {
        try {
            $rec = clone $this->record;
            // known things
            $rec->getEntry($this->translate->getName())->setData($record->getModuleName(), true);
            $rec->getEntry($this->translate->getLevel())->setData($this->level, true);

            // changed things
            $rec->offsetSet($this->translate->getParams(), $this->format->pack($record->getParams()));
            $rec->offsetSet($this->translate->getEnabled(), $record->isEnabled());
            return $rec->save();

        } catch (MapperException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(string $moduleName): bool
    {
        try {
            $rec = clone $this->record;
            $rec->offsetSet($this->translate->getName(), $moduleName);
            $rec->offsetSet($this->translate->getLevel(), $this->level);
            return $rec->delete();

        } catch (MapperException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param ARecord $rec
     * @throws MapperException
     * @return Record
     */
    public function fillModuleRecord(ARecord $rec): Record
    {
        $record = new Record();
        $record->setModuleName(strval($rec->offsetGet($this->translate->getName())));
        $record->setEnabled(boolval($rec->offsetGet($this->translate->getEnabled())));
        $record->setParams($this->format->unpack(strval($rec->offsetGet($this->translate->getParams()))));
        return $record;
    }

    /**
     * @param Record $record
     * @return ARecord
     */
    public function fillDbRecord(Record $record): ARecord
    {
        $rec = clone $this->record;
        $rec->offsetSet($this->translate->getName(), $record->getModuleName());
        $rec->offsetSet($this->translate->getLevel(), $this->level);
        $rec->offsetSet($this->translate->getParams(), $this->format->pack($record->getParams()));
        $rec->offsetSet($this->translate->getEnabled(), $record->isEnabled());
        return $rec;
    }

    public function getRecordName(Record $record): string
    {
        return $record->getModuleName();
    }
}
