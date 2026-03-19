<?php

namespace App\Service\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class JsonFormBuilder
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly JsonSchemaParser $parser,
        private readonly FieldTypeResolver $typeResolver,
        private readonly ConstraintBuilder $constraintBuilder,
    ) {}

    public function buildFromSchema(array $jsonSchema, array $uiSchema): FormInterface
    {
        $parsedSchema = $this->parser->parse($jsonSchema);

        $properties   = $parsedSchema['properties'];
        $requiredFields = $parsedSchema['required'] ?? [];
        $elements       = $uiSchema['elements'] ?? [];

        $formBuilder = $this->formFactory->createBuilder(FormType::class);

        foreach ($elements as $element) {
            $fieldName   = $this->resolveFieldName($element['scope']);
            $fieldConfig = $properties[$fieldName] ?? [];
            $label       = $element['label'] ?? $fieldName;
            $placeholder = $element['options']['placeholder'] ?? '';
            $attr        = $element['options']['attr'] ?? [];

            $formBuilder->add(
                $fieldName,
                $this->typeResolver->resolve($fieldConfig['type']),
                $this->buildFieldOptions($fieldName, $fieldConfig, $requiredFields, $label, $placeholder, $attr)
            );
        }

        return $formBuilder->getForm();
    }

    private function resolveFieldName(string $scope): string
    {
        // "#/properties/nombre" → "nombre"
        return basename(str_replace('#/properties/', '', $scope));
    }

    private function buildFieldOptions(
        string $fieldName,
        array $fieldConfig,
        array $requiredFields,
        string $label,
        string $placeholder,
        array  $attr
    ): array {
        $constraints = $this->constraintBuilder->build(
            $fieldName,
            $fieldConfig,
            $requiredFields,
            $label
        );

        return [
            'label'       => $label,
            'required'    => in_array($fieldName, $requiredFields, true),
            'constraints' => $constraints,
            'attr'        => array_merge(
            ['placeholder' => $placeholder],
            $attr,
            )
        ];
    }
}
