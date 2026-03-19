<?php

namespace App\Service\Form;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class ConstraintBuilder
{
    public function build(string $fieldName, array $fieldConfig, array $requiredFields, string $label): array
    {
        $constraints = [];

        if ($this->isRequired($fieldName, $requiredFields)) {
            $constraints[] = new NotBlank(
                message: sprintf('El campo "%s" es obligatorio.', $label)
            );
        }

        if (isset($fieldConfig['minLength']) || isset($fieldConfig['maxLength'])) {
            $constraints[] = new Length(
                min: $fieldConfig['minLength'] ?? null,
                max: $fieldConfig['maxLength'] ?? null,
                minMessage: sprintf('El campo "%s" debe tener al menos {{ limit }} caracteres.', $label),
                maxMessage: sprintf('El campo "%s" no puede tener más de {{ limit }} caracteres.', $label),
            );
        }

        if (isset($fieldConfig['minimum']) || isset($fieldConfig['maximum'])) {
            $constraints[] = new Range(
                min: $fieldConfig['minimum'] ?? null,
                max: $fieldConfig['maximum'] ?? null,
                notInRangeMessage: sprintf(
                    'El campo "%s" debe estar entre {{ min }} y {{ max }}.',
                    $label
                ),
            );
        }

        if (isset($fieldConfig['pattern'])) {
            $constraints[] = new Regex(
                pattern: $fieldConfig['pattern'],
                message: sprintf('El campo "%s" tiene un formato incorrecto.', $label)
            );
        }

        if (isset($fieldConfig['format']) && $fieldConfig['format'] === 'email') {
            $constraints[] = new Email(
                message: sprintf('El campo "%s" debe ser un email válido.', $label)
            );
        }

        return $constraints;
    }

    private function isRequired(string $fieldName, array $requiredFields): bool
    {
        return in_array($fieldName, $requiredFields, true);
    }
}
