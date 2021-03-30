<?php

namespace Krlove\EloquentModelGenerator;

use Illuminate\Database\DatabaseManager;

/**
 * Class TypeRegistry
 * @package Krlove\EloquentModelGenerator
 */
class TypeRegistry
{
    /**
     * @var array
     */
    protected $types = [
        'array'        => 'array',
        'simple_array' => 'array',
        'json_array'   => 'string',
        'bigint'       => 'int|null',
        'boolean'      => 'bool|null',
        'datetime'     => '\Illuminate\Support\Carbon|null',
        'datetimetz'   => '\Illuminate\Support\Carbon|null',
        'date'         => 'string|null',
        'time'         => 'string|null',
        'decimal'      => 'float|null',
        'int'          => 'int|null',
        'integer'      => 'int|null',
        'object'       => 'object',
        'smallint'     => 'int|null',
        'string'       => 'string|null',
        'text'         => 'string|null',
        'binary'       => 'string|null',
        'blob'         => 'string|null',
        'float'        => 'float|null',
        'guid'         => 'string',
    ];

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * TypeRegistry constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @param string $type
     * @param string $value
     * @param string|null $connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public function registerType($type, $value, $connection = null)
    {
        $this->types[$type] = $value;

        $manager = $this->databaseManager->connection($connection)->getDoctrineSchemaManager();
        $manager->getDatabasePlatform()->registerDoctrineTypeMapping($type, $value);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function resolveType($type)
    {
        return array_key_exists($type, $this->types) ? $this->types[$type] : 'mixed';
    }
}
