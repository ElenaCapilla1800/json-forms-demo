<?php

namespace App\Service\Form;

class JsonSchemaParser
{
    public function __construct(
        private readonly FieldTypeResolver $typeResolver,
    ) {}

    public function parse(array $schema): array
    {
        $this->validateHasProperties($schema);
        $this->validateFields($schema['properties']);

        return $schema;
    }

    private function validateHasProperties(array $schema): void
    {
        if (empty($schema['properties'])) {
            throw new \InvalidArgumentException(
                'El schema debe tener al menos un campo en "properties".'
            );
        }
    }

    private function validateFields(array $properties): void
    {
        foreach ($properties as $fieldName => $fieldConfig) {
            $this->validateFieldHasType($fieldName, $fieldConfig);
            $this->validateFieldTypeIsSupported($fieldName, $fieldConfig['type']);
            $this->validateEnumHasOptions($fieldName, $fieldConfig);
        }
    }

    private function validateFieldHasType(string $fieldName, array $fieldConfig): void
    {
        if (empty($fieldConfig['type'])) {
            throw new \InvalidArgumentException(
                sprintf('El campo "%s" debe tener un "type".', $fieldName)
            );
        }
    }

    private function validateFieldTypeIsSupported(string $fieldName, string $type):void
    {
        if (!$this->typeResolver->supports($type)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'El campo "%s" tiene un tipo "%s" no soportado. Tipos válidos: %s.',
                    $fieldName,
                    $type,
                    implode(', ', $this->typeResolver->getSupportedTypes())
                )
            );
        }
    }

    private function validateEnumHasOptions(string $fieldName, array $fieldConfig): void
    {
        if ($fieldConfig['type'] === 'enum' && empty($fieldConfig['options'])) {
            throw new \InvalidArgumentException(
                sprintf('El campo "%s" es de tipo "enum" y debe tener "options".', $fieldName)
            );
        }
    }
}
