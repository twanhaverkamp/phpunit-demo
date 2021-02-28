<?php

namespace Tests\Functional\Controller;

use App\Controller\CalculationController;
use App\Model\Calculation;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 * @covers CalculationController
 */
class CalculationControllerTest extends WebTestCase
{
    /**
     * @covers CalculationController::__invoke
     */
    public function testInvokeReturnsFormWithoutResult(): void {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/calculate');

        $returnValue = $crawler
            ->filterXPath('//span[contains(@id, "calculation_result")]')
            ->text();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('-', $returnValue);
    }

    /**
     * Unlike integration tests, these tests act more like an actual user. You visit the page via your browser,
     * fill in the form and then click the submit button. Because you act like a real user and because you're not
     * manipulate any part of the integration makes tests like these considerably more valuable.
     *
     * @covers       CalculationController::__invoke
     * @dataProvider getValidFormData
     *
     * @param array<string, mixed> $formData
     */
    public function testInvokeWithValidFormDataWillReturnExpectedValue(
        string $url,
        string $buttonValue,
        array $formData,
        float $expectedReturnValue
    ): void {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, $url);

        $form = $crawler->selectButton($buttonValue)
            ->form($formData);

        $crawler = $client->submit($form);
        $returnValue = $crawler
            ->filterXPath('//span[contains(@id, "calculation_result")]')
            ->text();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals($expectedReturnValue, $returnValue);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getValidFormData(): iterable
    {
        return [
            '1 * 1 = 1' => [
                'url' => '/calculate',
                'buttonValue' => 'Calculate',
                'formData' => [
                    'calculation[type]' => Calculation::TYPE_MULTIPLY,
                    'calculation[var1]' => 1,
                    'calculation[var2]' => 1,
                ],
                'expectedResult' => 1,
            ],
            '2 / 2 = 1' => [
                'url' => '/calculate',
                'buttonValue' => 'Calculate',
                'formData' => [
                    'calculation[type]' => Calculation::TYPE_DIVIDE,
                    'calculation[var1]' => 2,
                    'calculation[var2]' => 2,
                ],
                'expectedResult' => 1,
            ],
        ];
    }
}
