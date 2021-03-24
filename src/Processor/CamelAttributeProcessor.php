<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\CodeGenerator\Model\ArgumentModel;
use Krlove\CodeGenerator\Model\MethodModel;
use Krlove\CodeGenerator\Model\DocBlockModel;
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
     * @var EmgHelper
     */
    protected $helper;

    /**
     * CamelAttributeProcessor constructor.
     * @param EmgHelper $helper
     */
    public function __construct(EmgHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        $method = new MethodModel('getAttribute');
        $method->addArgument(new ArgumentModel('key'));
        $method->setBody('return parent::getAttribute(\Illuminate\Support\Str::snake($key));');
        $model->addMethod($method);

        $method = new MethodModel('setAttribute');
        $method->addArgument(new ArgumentModel('key'));
        $method->addArgument(new ArgumentModel('value'));
        $method->setBody('return parent::setAttribute(\Illuminate\Support\Str::snake($key), $value);');
        $model->addMethod($method);

        $method = new MethodModel('toArray');
        $method
            ->setBody('$parent = parent::toArray(); $ret = []; foreach($parent as $key => $value) { $ret[\Illuminate\Support\Str::camel($key)] = $value; }; return $ret;')
            ->setDocBlock(new DocBlockModel('@return array<mixed>'));
        $model->addMethod($method);
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 11;
    }
}
