<?php

final class CrudQaInspector
{
    private string $entity;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    public function report(): CrudQaReport
    {
        return new CrudQaReport($this->entity);
    }

    public function verifyCreate($createdId, callable $finder, array $expectedSubset = []): CrudQaCheck
    {
        if (!$this->isValidIdentifier($createdId)) {
            return new CrudQaCheck('create', false, '생성 식별자가 유효하지 않습니다.', [
                'created_id' => $createdId,
            ]);
        }

        $record = $finder($createdId);
        if (!$this->isRecord($record)) {
            return new CrudQaCheck('create', false, '생성 후 조회 결과가 없습니다.', [
                'created_id' => $createdId,
            ]);
        }

        $mismatch = $this->findSubsetMismatch($record, $expectedSubset);
        if (!empty($mismatch)) {
            return new CrudQaCheck('create', false, '생성 데이터가 기대값과 다릅니다.', [
                'created_id' => $createdId,
                'mismatch' => $mismatch,
            ]);
        }

        return new CrudQaCheck('create', true, '생성 후 조회까지 정상입니다.', [
            'created_id' => $createdId,
        ]);
    }

    public function verifyRead($id, callable $finder, ?callable $predicate = null): CrudQaCheck
    {
        if (!$this->isValidIdentifier($id)) {
            return new CrudQaCheck('read', false, '조회 식별자가 유효하지 않습니다.', [
                'id' => $id,
            ]);
        }

        $record = $finder($id);
        if (!$this->isRecord($record)) {
            return new CrudQaCheck('read', false, '조회 결과가 없습니다.', [
                'id' => $id,
            ]);
        }

        if ($predicate !== null && !$predicate($record)) {
            return new CrudQaCheck('read', false, '조회 결과가 사용자 정의 조건을 통과하지 못했습니다.', [
                'id' => $id,
                'record' => $record,
            ]);
        }

        return new CrudQaCheck('read', true, '조회가 정상입니다.', [
            'id' => $id,
        ]);
    }

    public function verifyUpdate($id, callable $finder, array $expectedSubset = []): CrudQaCheck
    {
        if (!$this->isValidIdentifier($id)) {
            return new CrudQaCheck('update', false, '수정 식별자가 유효하지 않습니다.', [
                'id' => $id,
            ]);
        }

        $record = $finder($id);
        if (!$this->isRecord($record)) {
            return new CrudQaCheck('update', false, '수정 후 조회 결과가 없습니다.', [
                'id' => $id,
            ]);
        }

        $mismatch = $this->findSubsetMismatch($record, $expectedSubset);
        if (!empty($mismatch)) {
            return new CrudQaCheck('update', false, '수정 데이터가 기대값과 다릅니다.', [
                'id' => $id,
                'mismatch' => $mismatch,
            ]);
        }

        return new CrudQaCheck('update', true, '수정 후 조회까지 정상입니다.', [
            'id' => $id,
        ]);
    }

    public function verifyDelete($id, callable $finder): CrudQaCheck
    {
        if (!$this->isValidIdentifier($id)) {
            return new CrudQaCheck('delete', false, '삭제 식별자가 유효하지 않습니다.', [
                'id' => $id,
            ]);
        }

        $record = $finder($id);
        if ($this->isRecord($record)) {
            return new CrudQaCheck('delete', false, '삭제 후에도 데이터가 남아 있습니다.', [
                'id' => $id,
                'record' => $record,
            ]);
        }

        return new CrudQaCheck('delete', true, '삭제가 정상 반영되었습니다.', [
            'id' => $id,
        ]);
    }

    public function verifyCrud(array $scenario): CrudQaReport
    {
        $report = $this->report();

        if (isset($scenario['create'])) {
            $report->addCheck($this->verifyCreate(
                $scenario['create']['id'] ?? null,
                $scenario['create']['finder'],
                $scenario['create']['expected'] ?? []
            ));
        }

        if (isset($scenario['read'])) {
            $report->addCheck($this->verifyRead(
                $scenario['read']['id'] ?? null,
                $scenario['read']['finder'],
                $scenario['read']['predicate'] ?? null
            ));
        }

        if (isset($scenario['update'])) {
            $report->addCheck($this->verifyUpdate(
                $scenario['update']['id'] ?? null,
                $scenario['update']['finder'],
                $scenario['update']['expected'] ?? []
            ));
        }

        if (isset($scenario['delete'])) {
            $report->addCheck($this->verifyDelete(
                $scenario['delete']['id'] ?? null,
                $scenario['delete']['finder']
            ));
        }

        return $report;
    }

    private function isValidIdentifier($id): bool
    {
        if (is_int($id)) {
            return $id > 0;
        }

        if (is_string($id) && ctype_digit($id)) {
            return (int) $id > 0;
        }

        return false;
    }

    private function isRecord($record): bool
    {
        return is_array($record) && !empty($record);
    }

    private function findSubsetMismatch(array $record, array $expectedSubset): array
    {
        $mismatch = [];
        foreach ($expectedSubset as $key => $expectedValue) {
            $actualValue = $record[$key] ?? null;
            if ((string) $actualValue !== (string) $expectedValue) {
                $mismatch[$key] = [
                    'expected' => $expectedValue,
                    'actual' => $actualValue,
                ];
            }
        }

        return $mismatch;
    }
}
