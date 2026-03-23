<?php

namespace App\Controller;

use App\Service\Form\JsonFormBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends AbstractController
{
    public function __construct(
        private readonly JsonFormBuilder $formBuilder,
    ) {}

    #[Route('/', name: 'app_form', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->formBuilder->buildFromSchema(
            $this->getJsonSchema(),
            $this->getUiSchema()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Aquí podemos guardar los datos en la base de datos o realizar otras acciones
            $this->addFlash('success', '¡Formulario enviado correctamente!');

            return $this->redirectToRoute('app_form');
        }

        return $this->render('form/dynamic.html.twig', [
            'form' => $form,
        ]);
    }

    private function getJsonSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'nombre' => [
                    'type'      => 'string',
                    'minLength' => 2,
                    'maxLength' => 100,
                ],
                'email' => [
                    'type'   => 'string',
                    'format' => 'email',
                ],
                'age' => [
                    'type'    => 'integer',
                    'minimum' => 18,
                    'maximum' => 99,
                ],
                'comentario' => [
                    'type'      => 'text',
                    'maxLength' => 500,
                ],
            ],
            'required' => ['nombre', 'email'],
        ];
    }

    private function getUiSchema(): array
    {
        return [
            'type' => 'VerticalLayout',
            'elements' => [
                [
                    'type'  => 'Control',
                    'scope' => '#/properties/nombre',
                    'label' => 'Nombre Completo',
                    'options' => [
                        'placeholder' => 'Ingresa tu nombre completo',
                        ]
                ],
                [
                    'type'  => 'Control',
                    'scope' => '#/properties/email',
                    'label' => 'Correo Electrónico',
                    'options' => [
                        'placeholder' => 'Ingresa tu correo electrónico',
                    ],
                ],
                [
                    'type'  => 'Control',
                    'scope' => '#/properties/age',
                    'label' => 'Edad',
                ],
                [
                    'type'  => 'Control',
                    'scope' => '#/properties/comentario',
                    'label' => 'Comentario',
                    'options' => [
                        'placeholder' => 'Escribe tu comentario...',
                    ],
                ],
            ],
        ];
    }
}
