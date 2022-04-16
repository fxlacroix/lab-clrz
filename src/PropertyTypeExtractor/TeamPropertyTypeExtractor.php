<?php

namespace App\PropertyTypeExtractor;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use App\Entity\Member;
use App\Entity\Power;
use App\Entity\Team;

class TeamPropertyTypeExtractor implements PropertyTypeExtractorInterface
{
    private $reflectionExtractor;

    public function __construct()
    {
        $this->reflectionExtractor = new ReflectionExtractor();
    }

    public function getTypes($class, $property, array $context = array()): ?array
    {
        if (is_a($class, Team::class, true) && 'members' === $property) {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, true, Member::class . "[]")
            ];
        }
        if (is_a($class, Member::class, true) && 'powers' === $property) {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, true, Power::class . "[]")
            ];
        }
        return $this->reflectionExtractor->getTypes($class, $property, $context);
    }
}
