<?php

final class CrudQaCheck
{
    private string $operation;
    private bool $passed;
    private string $message;
    private array $context;

    public function __construct(string $operation, bool $passed, string $message, array $context = [])
    {
        $this->operation = $operation;
        $this->passed = $passed;
        $this->message = $message;
        $this->context = $context;
    }

    public function operation(): string
    {
        return $this->operation;
    }

    public function passed(): bool
    {
        return $this->passed;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function context(): array
    {
        return $this->context;
    }

    public function toArray(): array
    {
        return [
            'operation' => $this->operation,
            'passed' => $this->passed,
            'message' => $this->message,
            'context' => $this->context,
        ];
    }
}
