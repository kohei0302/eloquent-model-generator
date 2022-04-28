<?php

namespace Krlove\EloquentModelGenerator;

use Illuminate\Database\DatabaseManager;

class TypeRegistry
{
    protected array $types = [
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
        'enum'         => 'string',
    ];

    public function __construct(private DatabaseManager $databaseManager) {}

    public function registerType(string $type, string $value, string $connection = null): void
    {
        $this->types[$type] = $value;

        $manager = $this->databaseManager->connection($connection)->getDoctrineSchemaManager();
        $manager->getDatabasePlatform()->registerDoctrineTypeMapping($type, $value);
    }

    public function resolveType(string $type): string
    {
        return array_key_exists($type, $this->types) ? $this->types[$type] : 'mixed';
    }
}
