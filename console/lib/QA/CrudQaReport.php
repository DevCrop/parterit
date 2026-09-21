<?php

final class CrudQaReport
{
    private string $entity;
    /** @var CrudQaCheck[] */
    private array $checks = [];

    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function addCheck(CrudQaCheck $check): self
    {
        $this->checks[] = $check;
        return $this;
    }

    public function passed(): bool
    {
        foreach ($this->checks as $check) {
            if (!$check->passed()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return CrudQaCheck[]
     */
    public function checks(): array
    {
        return $this->checks;
    }

    public function summary(): array
    {
        return [
            'entity' => $this->entity,
            'passed' => $this->passed(),
            'checks' => array_map(static function (CrudQaCheck $check): array {
                return $check->toArray();
            }, $this->checks),
        ];
    }
}
