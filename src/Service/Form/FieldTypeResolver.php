<?php

namespace App\Service\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FieldTypeResolver
{
    private const TYPE_MAP = [
        'string'   => TextType::class,
        'text'     => TextareaType::class,
        'integer'  => IntegerType::class,
        'number'   => NumberType::class,
        'boolean'  => CheckboxType::class,
        'enum'     => ChoiceType::class,
    ];

    public function resolve(string $jsonType): string
    {
        if (!isset(self::TYPE_MAP[$jsonType])) {
            throw new \InvalidArgumentException(
                sprintf('No existe un FormType para el tipo JSON "%s".', $jsonType)
            );
        }

        return self::TYPE_MAP[$jsonType];
    }

    public function supports(string $type): bool
    {
        return isset(self::TYPE_MAP[$type]);
    }
    public function getSupportedTypes(): array
    {
        return array_keys(self::TYPE_MAP);
    }
}
