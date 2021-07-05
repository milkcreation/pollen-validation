<?php

declare(strict_types=1);

namespace Pollen\Validation\Rules;

use Pollen\Validation\ValidationRuleInterface;
use Respect\Validation\Rules\Length;
use Respect\Validation\Rules\Regex;
use Respect\Validation\Exceptions\ComponentException;

class PasswordRule extends AbstractRule
{
    /**
     * Nombre de chiffres requis.
     * @var int
     */
    protected $digit = 1;

    /**
     * Nombre de minuscules requises.
     * @var int
     */
    protected $lower = 1;

    /**
     * Longueur maximum.
     * @var int
     */
    protected $max = 0;

    /**
     * Longueur minimum.
     * @var int
     */
    protected $min = 8;

    /**
     * Nombre de caractères spéciaux requis.
     * @var int
     */
    protected $special = 0;

    /**
     * Nombre de majuscules requises.
     * @var int
     */
    protected $upper = 1;

    /**
     * @inheritDoc
     */
    public function setArgs(...$args): ValidationRuleInterface
    {
        $args = array_merge(
            $defaults = [
                'digit'   => 1,
                'lower'   => 1,
                'max'     => 16,
                'min'     => 8,
                'special' => 0,
                'upper'   => 1,
            ],
            $args[0] ?? []
        );

        foreach ($args as $k => $v) {
            if (array_key_exists($k, $defaults)) {
                $this->{$k} = (int)$v;
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        try {
            if ($this->min > 0 && !(new Length($this->min))->validate($input)) {
                return false;
            }

            if ($this->max > 0 && !(new Length(null, $this->max))->validate($input)) {
                return false;
            }

            $regex = "";

            if ($this->digit) {
                $regex .= "(?=(?:.*\d){" . $this->digit . ",})";
            }

            if ($this->lower) {
                $regex .= "(?=(?:.*[a-z]){" . $this->lower . ",})";
            }

            if ($this->upper) {
                $regex .= "(?=(?:.*[A-Z]){" . $this->upper . ",})";
            }

            if ($this->special) {
                $regex .= "(?=(?:.*[!@#$%^&*()\[\]\-_=+{};:,<.>]){" . $this->special . ",})";
            }

            return (new Regex('/' . $regex . '/'))->validate($input);
        } catch (ComponentException $e) {
            return false;
        }
    }
}