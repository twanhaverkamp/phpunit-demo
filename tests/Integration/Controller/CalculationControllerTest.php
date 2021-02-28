<?php

namespace Tests\Integration\Controller;

use App\Controller\CalculationController;
use App\Form\CalculationType;
use App\Model\Calculation;
use App\Tool\Calculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 * @covers CalculationController
 */
class CalculationControllerTest extends TestCase
{
    /**
     * @var Calculator|MockObject
     */
    private $calculatorMock;

    /**
     * @var ContainerInterface|MockObject
     */
    private $containerMock;

    /**
     * @var FormFactoryInterface|MockObject
     */
    private $formFactoryMock;

    /**
     * @var FormInterface|MockObject
     */
    private $formMock;

    /**
     * @var Environment|MockObject
     */
    private $twigEnvironmentMock;

    /**
     * @var FormView
     */
    private $formViewStub;

    /**
     * @var Request
     */
    private $requestStub;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->calculatorMock = $this->createMock(Calculator::class);

        $this->formViewStub = new FormView();
        $this->requestStub = new Request();

        $this->formMock = $this->createMock(FormInterface::class);
        $this->formMock
            ->expects($this->once())
            ->method('add')
            ->with('submit', SubmitType::class, ['label' => 'Calculate'])
            ->willReturn($this->formMock);
        $this->formMock
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->requestStub);
        $this->formMock
            ->expects($this->once())
            ->method('createView')
            ->willReturn($this->formViewStub);

        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(CalculationType::class)
            ->willReturn($this->formMock);

        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->containerMock
            ->expects($this->once())
            ->method('has')
            ->with('twig')
            ->willReturn(true);
    }

    /**
     * @covers       CalculationController::__invoke
     * @dataProvider getValidCalculationData
     */
    public function testInvokeWithValidSubmittedFormWillCallExpectedCalculatorMethod(
        string $calculationType,
        int $calculationVar1,
        int $calculationVar2,
        string $expectedCalculatorMethod,
        int $expectedCalculatorVar1,
        int $expectedCalculatorVar2,
        float $expectedCalculatorResult
    ): void {
        $this->formMock
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);
        $this->formMock
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $calculationStub = new Calculation();
        $calculationStub->type = $calculationType;
        $calculationStub->var1 = $calculationVar1;
        $calculationStub->var2 = $calculationVar2;

        $this->formMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn($calculationStub);

        $this->calculatorMock
            ->expects($this->once())
            ->method($expectedCalculatorMethod)
            ->with($expectedCalculatorVar1, $expectedCalculatorVar2)
            ->willReturn($expectedCalculatorResult);

        $this->twigEnvironmentMock = $this->createMock(Environment::class);
        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with('calculation/multiply.html.twig', [
                'form' => $this->formViewStub,
                'result' => $expectedCalculatorResult,
            ]);

        $this->containerMock
            ->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twigEnvironmentMock);

        $calculationController = new CalculationController();
        $calculationController->setContainer($this->containerMock);
        $calculationController->__invoke($this->requestStub, $this->calculatorMock, $this->formFactoryMock);
    }

    /**
     * @covers CalculationController::__invoke
     */
    public function testInvokeWithNonSubmittedFormWontCallCalculator(): void
    {
        $this->formMock
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false);
        $this->formMock
            ->expects($this->never())
            ->method('isValid');
        $this->formMock
            ->expects($this->never())
            ->method('getData');

        $this->calculatorMock
            ->expects($this->never())
            ->method('multiply');
        $this->calculatorMock
            ->expects($this->never())
            ->method('divide');

        $this->twigEnvironmentMock = $this->createMock(Environment::class);
        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with('calculation/multiply.html.twig', [
                'form' => $this->formViewStub,
                'result' => '-',
            ]);

        $this->containerMock
            ->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twigEnvironmentMock);

        $calculationController = new CalculationController();
        $calculationController->setContainer($this->containerMock);
        $calculationController->__invoke($this->requestStub, $this->calculatorMock, $this->formFactoryMock);
    }

    /**
     * @covers CalculationController::__invoke
     */
    public function testInvokeWithInvalidSubmittedFormWontCallCalculator(): void
    {
        $this->formMock
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);
        $this->formMock
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $this->formMock
            ->expects($this->never())
            ->method('getData');

        $this->calculatorMock
            ->expects($this->never())
            ->method('multiply');
        $this->calculatorMock
            ->expects($this->never())
            ->method('divide');

        $this->twigEnvironmentMock = $this->createMock(Environment::class);
        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with('calculation/multiply.html.twig', [
                'form' => $this->formViewStub,
                'result' => '-',
            ]);

        $this->containerMock
            ->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twigEnvironmentMock);

        $calculationController = new CalculationController();
        $calculationController->setContainer($this->containerMock);
        $calculationController->__invoke($this->requestStub, $this->calculatorMock, $this->formFactoryMock);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getValidCalculationData(): iterable
    {
        return [
            'Multiply 1 by 1' => [
                'calculationType' => Calculation::TYPE_MULTIPLY,
                'calculationVar1' => 1,
                'calculationVar2' => 1,
                'expectedCalculatorMethod' => 'multiply',
                'expectedCalculatorVar1' => 1,
                'expectedCalculatorVar2' => 1,
                'expectedCalculatorResult' => 1,
            ],
            'Divide 2 by 2' => [
                'calculationType' => Calculation::TYPE_DIVIDE,
                'calculationVar1' => 2,
                'calculationVar2' => 2,
                'expectedCalculatorMethod' => 'divide',
                'expectedCalculatorVar1' => 2,
                'expectedCalculatorVar2' => 2,
                'expectedCalculatorResult' => 1,
            ],
        ];
    }
}
