<?php

namespace App\Controller;

use App\Form\CalculationType;
use App\Model\Calculation;
use App\Tool\Calculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 *
 * @Route("/calculate", name="calculation", methods={"GET", "POST"})
 */
class CalculationController extends AbstractController
{
    public function __invoke(
        Request $request,
        Calculator $calculator,
        FormFactoryInterface $formFactory
    ): Response {
        $form = $formFactory->create(CalculationType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Calculate',
            ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            /** @var Calculation $calculation */
            $calculation = $form->getData();

            $result = $calculator->{$calculation->type}($calculation->var1, $calculation->var2);
        }

        return $this->render('calculation.html.twig', [
            'form' => $form->createView(),
            'result' => $result ?? null,
        ]);
    }
}
