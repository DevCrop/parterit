<?php

final class CrudQaFactory
{
    public static function forEntity(string $entity): CrudQaInspector
    {
        return new CrudQaInspector($entity);
    }
}
