<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Support\Str;
use Illuminate\Database\DatabaseManager;
use Krlove\CodeGenerator\Model\ArgumentModel;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\MethodModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\UseClassModel;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

/**
 * Class CamelAttributeProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class CamelAttributeProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var EmgHelper
     */
    protected $helper;

    /**
     * CamelAttributeProcessor constructor.
     * @param DatabaseManager $databaseManager
     * @param EmgHelper $helper
     */
    public function __construct(DatabaseManager $databaseManager, EmgHelper $helper)
    {
        $this->databaseManager = $databaseManager;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix        = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();
        $tableDetails       = $schemaManager->listTableDetails($prefix . $model->getTableName());

        $columnNames = [];
        foreach ($tableDetails->getColumns() as $column) {
            $columnNames[] = Str::camel($column->getName());
        }
        // dd($columnNames);
        // exit();

        $model->addUses(new UseClassModel('Illuminate\Support\Str'));

        $columnsProperty = new PropertyModel('columns');
        $columnsProperty->setAccess('private')
            ->setValue($columnNames)
            ->setDocBlock(new DocBlockModel('Laravel非標準のカラムリスト', '@var string[]'));
        $model->addProperty($columnsProperty);

        $method = new MethodModel('getAttribute');
        $method->addArgument(new ArgumentModel('key'));
        $method->setBody('return parent::getAttribute(in_array($key, $this->columns) ? Str::snake($key) : $key);');
        $model->addMethod($method);

        $method = new MethodModel('setAttribute');
        $method->addArgument(new ArgumentModel('key'));
        $method->addArgument(new ArgumentModel('value'));
        $method->setBody('return parent::setAttribute(in_array($key, $this->columns) ? Str::snake($key) : $key, $value);');
        $model->addMethod($method);

        $method = new MethodModel('toArray');
        $method
            ->setBody('$parent = parent::toArray(); $ret = []; foreach($parent as $key => $value) { $ret[Str::camel((string)$key)] = $value; }; return $ret;')
            ->setDocBlock(new DocBlockModel('@return array<mixed>'));
        $model->addMethod($method);
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 5;
    }
}
