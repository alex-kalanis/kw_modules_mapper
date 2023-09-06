<?php

namespace ListsTests;


use CommonTestClass;
use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers\Storage\ATable;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_mapper\Records\ASimpleRecord;
use kalanis\kw_mapper\Storage\Shared\FormatFiles\SeparatedElements;
use kalanis\kw_mapper\Storage\Storage\StorageSingleton;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\ModulesLists\ParamsFormat;
use kalanis\kw_modules\ModulesLists\Record;
use kalanis\kw_modules_mapper\ModulesLists\Mapper;
use kalanis\kw_storage\Access as storage_access;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Storage\Target\Memory;
use kalanis\kw_storage\StorageException;


/**
 * Class MapperTest
 * @package ListsTests
 */
class MapperTest extends CommonTestClass
{
    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testListing(): void
    {
        $lib = $this->getLib(new XRecord());
        $list = $lib->listing();
        $this->assertNotEmpty($list);

        usort($list, [$this, 'sortListing']);

        /** @var Record $entry */
        $entry = reset($list);
        $this->assertEquals('Dashboard', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['display' => 'no'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Files', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['pos' => '4', 'image' => 'files/files.png'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Images', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['pos' => '5', 'image' => 'images/images.png'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Login', $entry->getModuleName());
        $this->assertFalse($entry->isEnabled());
        $this->assertEquals(['display' => 'no'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Logout', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['menu' => 'system', 'pos' => '1', 'image' => 'system/logout.png'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Menu', $entry->getModuleName());
        $this->assertFalse($entry->isEnabled());
        $this->assertEquals(['link' => 'menu/dashboard', 'pos' => '8', 'image' => 'menu.png'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Personal', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['menu' => 'system', 'pos' => '3', 'image' => 'system/personal.png'], $entry->getParams());

        $entry = next($list);
        $this->assertEquals('Texts', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['pos' => '1', 'image' => 'texts/texts.png', 'name' => 'Texty'], $entry->getParams());

        $entry = next($list);
        $this->assertFalse($entry);
    }

    public function sortListing(Record $a, Record $b): int
    {
        return $a->getModuleName() <=> $b->getModuleName();
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testProcess(): void
    {
        $lib = $this->getLib(new XRecord());

        $entry = $lib->get('Pedigree');
        $this->assertEmpty($entry);

        $this->assertTrue($lib->add('Pedigree', true, ['pos' => '8', 'image' => 'pedigree.png']));

        $entry = $lib->get('Pedigree');
        $this->assertNotEmpty($entry);
        $this->assertEquals('Pedigree', $entry->getModuleName());
        $this->assertTrue($entry->isEnabled());
        $this->assertEquals(['pos' => '8', 'image' => 'pedigree.png'], $entry->getParams());

        $entry->setEnabled(false);
        $this->assertTrue($lib->updateObject($entry));
        $entry = $lib->get('Pedigree');
        $this->assertNotEmpty($entry);
        $this->assertFalse($entry->isEnabled());

        $this->assertTrue($lib->updateBasic('Pedigree', null, ['pos' => '10']));
        $entry = $lib->get('Pedigree');
        $this->assertNotEmpty($entry);
        $this->assertEquals(['pos' => '10'], $entry->getParams());

        $this->assertTrue($lib->updateBasic('Pedigree', true, null));
        $entry = $lib->get('Pedigree');
        $this->assertNotEmpty($entry);
        $this->assertTrue($entry->isEnabled());

        $this->assertTrue($lib->remove('Pedigree'));
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testProcessNone(): void
    {
        $lib = $this->getLib(new XRecord());
        $this->assertFalse($lib->add('Logout', false));
        $this->assertFalse($lib->updateBasic('Logout', null, null));
        $this->assertFalse($lib->updateBasic('This one does not exists', true, []));
        $unknown = new Record();
        $unknown->setModuleName('This one does not exists');
        $this->assertFalse($lib->updateObject($unknown));
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testFailList(): void
    {
        $lib = $this->getLib(new XFailReadRecord());
        $this->expectException(ModuleException::class);
        $lib->listing();
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testFailRead(): void
    {
        $lib = $this->getLib(new XFailReadRecord());
        $this->expectException(ModuleException::class);
        $lib->get('Logout');
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testFailAdd(): void
    {
        $lib = $this->getLib(new XFailWriteRecord());
        $this->expectException(ModuleException::class);
        $lib->add('Pedigree');
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testFailUpdateBasic(): void
    {
        $lib = $this->getLib(new XFailWriteRecord());
        $this->expectException(ModuleException::class);
        $lib->updateBasic('Logout', null, []);
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testFailUpdateObject(): void
    {
        $lib = $this->getLib(new XFailWriteRecord());
        $this->expectException(ModuleException::class);
        $unknown = new Record();
        $unknown->setModuleName('Logout');
        $lib->updateObject($unknown);
    }

    /**
     * @throws MapperException
     * @throws ModuleException
     * @throws StorageException
     */
    public function testFailRemove(): void
    {
        $lib = $this->getLib(new XFailWriteRecord());
        $this->expectException(ModuleException::class);
        $lib->remove('Pedigree');
    }

    /**
     * @param XRecord $record
     * @throws StorageException
     * @return Mapper
     * This thing reads data from memory as its storage and uses it again for saving
     */
    protected function getLib(XRecord $record): Mapper
    {
        $storage = new Memory();
        $storage->save('modules.conf',
            'Dashboard|1|display=no|53|' . "\r\n"
            . 'Login|0|display=no|53|' . "\r\n"
            . 'Logout|1|menu=system&pos=1&image=system/logout.png|53|' . "\r\n"
            . 'Personal|1|menu=system&pos=3&image=system/personal.png|53|' . "\r\n"
            . 'Texts|1|pos=1&image=texts/texts.png&name=Texty|53|' . "\r\n"
            . 'Files|1|pos=4&image=files/files.png|53|' . "\r\n"
            . 'Images|1|pos=5&image=images/images.png|53|' . "\r\n"
            . 'Menu|0|link=menu/dashboard&pos=8&image=menu.png|53|' . "\r\n"
            . 'Menu|1|link=menu/dashboard&pos=8&image=menu.png|72|' . "\r\n"
            . '');
//        OwnStorageSingleton::getInstance()->clearStorage();
//        OwnStorageSingleton::getInstance()->setStorage((new storage_access\Factory())->getStorage($storage));
        StorageSingleton::getInstance()->clearStorage();
        StorageSingleton::getInstance()->getStorage($storage);
        $file = new Mapper($record, new Mapper\Translate(), new ParamsFormat\Http());
        $file->setModuleLevel(53);
        return $file;
    }
}


class XRecord extends ASimpleRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('name', IEntryType::TYPE_STRING, 32);
        $this->addEntry('level', IEntryType::TYPE_INTEGER, 8);
        $this->addEntry('params', IEntryType::TYPE_STRING, PHP_INT_MAX);
        $this->addEntry('enabled', IEntryType::TYPE_BOOLEAN);
        $this->setMapper(XMapper::class);
    }
}


class XMapper extends ATable
{
    protected function setMap(): void
    {
        $this->setSource('modules.conf');
        $this->setFormat(SeparatedElements::class);
        $this->setRelation('name', 0);
        $this->setRelation('enabled', 1);
        $this->setRelation('params', 2);
        $this->setRelation('level', 3);
    }

//    protected function getStorage($storageParams = 'volume'): IStorage
//    {
//        return OwnStorageSingleton::getInstance()->getStorage($storageParams);
//    }
//
//    protected function clearStorage(): void
//    {
//        OwnStorageSingleton::getInstance()->clearStorage();
//    }
}


class XFailReadRecord extends XRecord
{
    protected function addEntries(): void
    {
        parent::addEntries();
        $this->setMapper(XFailReadMapper::class);
    }
}


class XFailWriteRecord extends XRecord
{
    protected function addEntries(): void
    {
        parent::addEntries();
        $this->setMapper(XFailWriteMapper::class);
    }
}


class XFailReadMapper extends XMapper
{
    protected function beforeLoad(ARecord $record): bool
    {
        throw new MapperException('mock');
    }

    public function loadMultiple(ARecord $record): array
    {
        throw new MapperException('mock');
    }
}


class XFailWriteMapper extends XMapper
{
    protected function beforeSave(ARecord $record): bool
    {
        throw new MapperException('mock');
    }

    protected function beforeDelete(ARecord $record): bool
    {
        throw new MapperException('mock');
    }
}


class OwnStorageSingleton
{
    /** @var self|null */
    protected static $instance = null;
    /** @var IStorage|null */
    private $storage = null;

    public static function getInstance(): self
    {
        if (empty(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    protected function __construct()
    {
    }

    /**
     * @codeCoverageIgnore why someone would run that?!
     */
    private function __clone()
    {
    }

    public function setStorage(IStorage $storageParams): void
    {
        $this->storage = $storageParams;
    }

    /**
     * @param object|array<string, string|object>|string $storageParams
     * @throws StorageException
     * @return IStorage
     */
    public function getStorage($storageParams): IStorage
    {
        if (empty($this->storage)) {
            throw new StorageException('Storage cannot be empty!');
        }
        return $this->storage;
    }

    public function clearStorage(): void
    {
        $this->storage = null;
    }
}
