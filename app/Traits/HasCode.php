<?php

namespace App\Traits;

use App\Services\ResourceCodeGenerator;

trait HasCode
{
    public static function bootHasCode(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getCodeColumnName()})) {
                $model->{$model->getCodeColumnName()} = $model->generateCode();
            }
        });
    }

    public function generateCode(): string
    {
        $generator = app(ResourceCodeGenerator::class);

        return $generator->generate(
            $this->getCodePrefix(),
            $this->getTable(),
            $this->getCodeColumnName()
        );
    }

    public function getCodeColumnName(): string
    {
        return $this->codeColumn ?? $this->getTable().'_code';
    }

    public function getCodePrefix(): string
    {
        return $this->codePrefix ?? str(class_basename(static::class))->upper()->limit(3, '')->toString();
    }
}
