<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 */
class Calculation
{
    /**
     * @var string
     */
    public const TYPE_MULTIPLY = 'multiply';

    /**
     * @var string
     */
    public const TYPE_DIVIDE = 'divide';

    /**
     * @Assert\Choice(choices={
     *     Calculation::TYPE_MULTIPLY,
     *     Calculation::TYPE_DIVIDE
     * })
     * @Assert\NotNull
     */
    public string $type;

    /**
     * @Assert\NotNull
     */
    public float $var1;

    /**
     * @Assert\NotNull
     */
    public float $var2;

    public ?float $result = null;
}
