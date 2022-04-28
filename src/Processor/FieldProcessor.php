<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Support\Str;
use Illuminate\Database\DatabaseManager;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\VirtualPropertyModel;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\TypeRegistry;

class FieldProcessor implements ProcessorInterface
{
    public function __construct(private DatabaseManager $databaseManager, private TypeRegistry $typeRegistry) {}
    
    public function process(EloquentModel $model, Config $config): void
    {
        $schemaManager = $this->databaseManager->connection($config->getConnection())->getDoctrineSchemaManager();

        $tableDetails = $schemaManager->listTableDetails(Prefix::add($model->getTableName()));
        $primaryColumnNames = $tableDetails->getPrimaryKey() ? $tableDetails->getPrimaryKey()->getColumns() : [];

        $columnNames = [];
        $dateColumnNames = [];
        foreach ($tableDetails->getColumns() as $column) {
            // $model->addProperty(new VirtualPropertyModel(
            //     Str::camel($column->getName()),
            //     $this->typeRegistry->resolveType($column->getType()->getName())
            // ));

            if (!in_array($column->getName(), $primaryColumnNames)) {
                $columnNames[] = Str::camel($column->getName());
            }

            if (in_array($column->getType()->getName(), ['datetime', 'datetimetz'])) {
                $dateColumnNames[] = $column->getName();
            }
        }

        $fillableProperty = new PropertyModel('fillable');
        $fillableProperty->setAccess('protected')
            ->setValue($columnNames)
            ->setDocBlock(new DocBlockModel('@var string[]'));
        $model->addProperty($fillableProperty);

        $fillableProperty = new PropertyModel('dates');
        $fillableProperty->setAccess('protected')
            ->setValue($dateColumnNames)
            ->setDocBlock(new DocBlockModel('@var string[]'));
        $model->addProperty($fillableProperty);
    }

    public function getPriority(): int
    {
        return 5;
    }
}
