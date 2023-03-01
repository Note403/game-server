<?php

namespace Auxilium\Support;

use Auxilium\Data\Request;
use Exception;

class Validator
{
    private array $ruleSet;
    private array $validRules = [
        'string',
        'number',
        'min',
        'max',
        'not',
        'float',
        'numeric',
        'alpha',
        'alphanumeric',
        'required',
    ];

    private Request $request;

    public function __invoke(Request $request, array $rules): void
    {
        if (empty($rules))
            throw new Exception('VALIDATION RULES EMPTY');

        $this->ruleSet = $this->createRuleSet($rules);
        $this->request = $request;

        if (!$this->validRules())
            throw new Exception('INVALID VALIDATION RULES');

        foreach ($this->ruleSet as $key => $rules) {
            foreach ($rules as $rule) {
                if (str_contains($rule, ':')) {
                    $rule_parts = explode(':', $rule);

                    $this->{$rule_parts[0]}($key, $this->request->input($key), $rule_parts[1]);
                } else {
                    if (in_array('required', $rules) && $this->request->input($key) == null) {
                        throw new Exception("{$key} is a required field");
                    } else if ($rule != 'required') {
                        $this->{$rule}($key, $this->request->input($key));
                    }
                }
            }
        }
    }

    private function createRuleSet(array $rules): array
    {
        $ruleSet = array();

        foreach ($rules as $key => $ruleSetStr) {
            $ruleSet[$key] = str_contains($ruleSetStr, '|')
                ? explode('|', $ruleSetStr)
                : [$ruleSetStr];
        }

        return $ruleSet;
    }

    private function validRules(): bool
    {
        foreach ($this->ruleSet as $key => $rules) {
            foreach ($rules as $rule) {
                if (str_contains($rule, ':'))
                    $rule = explode(':', $rule)[0];

                if (!in_array($rule, $this->validRules))
                    return false;
            }
        }

        return true;
    }

    private function string(string $key, $value)
    {
        if (!is_string($value))
            throw new Exception("{$key} must be a string");
    }

    private function number(string $key, $value)
    {
        if (!is_int($value))
            throw new Exception("{$key} must be an integer");
    }

    private function min(string $key, $value, int $min)
    {
        if (strlen($value) < $min)
            throw new Exception("{$key} must be at least {$min} characters long");
    }

    private function max(string $key, $value, int $max)
    {
        if (strlen($value) > $max)
            throw new Exception("{$key} cant be longer than {$max} characters");
    }

    private function not(string $key, $value, $not)
    {
        if ($value == $not)
            throw new Exception("{$key} can not be {$not}");
    }

    private function float(string $key, $value)
    {
        if (!is_float($value))
            throw new Exception("{$key} must be a float");
    }

    private function numeric(string $key, $value)
    {
        if (!is_numeric($value))
            throw new Exception("{$key} must be numeric");
    }

    private function alpha(string $key, $value)
    {
        if (!ctype_alpha($value))
            throw new Exception("{$key} can only contain letters");
    }

    private function alphanumeric(string $key, $value)
    {
        if (!ctype_alnum($value))
            throw new Exception("{$key} can only contain numbers and letters");
    }
}