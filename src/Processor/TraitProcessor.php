<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\CodeGenerator\Model\UseClassModel;
use Krlove\CodeGenerator\Model\UseTraitModel;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

/**
 * Class TraitProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class TraitProcessor implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config): void
    {
        $model->addUses(new UseClassModel('Illuminate\Database\Eloquent\Factories\HasFactory'));
        $model->addTrait(new UseTraitModel('HasFactory'));
    }

    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        return 6;
    }
}
