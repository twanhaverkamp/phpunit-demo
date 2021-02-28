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
     *
     * This method allows you to prepare for your tests. Consider, for example, setting up a test database
     * and running migrations and fixtures. Afterwards you can clean up again with the {@see tearDown} method.
     *
     * Note: This method is called for every single test in this class.
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
     * As you may have noticed, this test requires a lot of effort, where most of the code consists of mocking various dependencies.
     * Note: Mocking an object manipulates the way that object behaves. It is useful if your class has many dependencies,
     * but this also increases the risk of actually testing "$this->assertTrue(true)".
     *
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
     * Testing your "best case" scenario makes sense and is usually the first thing you do.
     * But don't forget to test the other scenarios, because they will occur!
     *
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
     * Note: DataProviders are called before the class is initiated to calculate the number of tests present.
     * Because of this you do not have access to properties that are filled in the {@see setUp} method.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getValidCalculationData(): iterable
    {
        return [
            [Calculation::TYPE_MULTIPLY, 1, 1, 'multiply', 1, 1, 1],
            // This will perform the exact same test as above, but the output will be more comprehensible.
            '1 * 1 = 1' => [
                'calculationType' => Calculation::TYPE_MULTIPLY,
                'calculationVar1' => 1,
                'calculationVar2' => 1,
                'expectedCalculatorMethod' => 'multiply',
                'expectedCalculatorVar1' => 1,
                'expectedCalculatorVar2' => 1,
                'expectedCalculatorResult' => 1,
            ],
            '2 / 2 = 1' => [
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
