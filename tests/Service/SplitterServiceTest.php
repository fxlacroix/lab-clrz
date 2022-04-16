<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Serializer;

class SplitterServiceTest extends KernelTestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::bootKernel();
        $this->container = static::getContainer();
        $this->splitterService = $this->container->get("App\Service\SplitterService");
    }

    /**
     *  @dataProvider provideSerializerData
     */
    public function testGetSerializer(
        string  $from,
        string  $to,
        ?string $propertyTypeExtrator,
        string  $data,
        string  $class,
        bool    $status=true): void
    {
        try {
            $serializer = $this->splitterService->getSerializer($from, $to, $propertyTypeExtrator);
            $this->assertInstanceOf('Symfony\Component\Serializer\Serializer', $serializer);
            $object = $serializer->deserialize($data, $class, $from);
            $this->assertInstanceOf($class, $object);
            $this->assertTrue($status);
        }catch(\Exception $e) {
            $this->assertNotTrue($status);
        }
    }

    public function provideSerializerData()
    {
        return [
            ['json', 'csv', null, '{}', '', false],
            ['ukn', 'csv', null, '{}', 'App\Entity\Team' , false],
            ['json', 'ukn', null, '{}', 'App\Entity\Team' , false],
            ['json', 'csv', 'App\PropertyTypeExtractor\TeamPropertyTypeExtractor', '{}', 'App\Entity\Team'],
        ];
    }

    /**
     *  @dataProvider provideExtractorData
     */
    public function extractTeamFromMappingTest(string $heap, string $mappingInfo, ?string $propertyTypeExtrator, string $from, string $to)
    {
        $serializer = $this->splitterService->getSerializer($fro, $to, 'App\PropertyTypeExtractor\TeamPropertyTypeExtractor');
        $datas = $this->splitterService(json_decode($heap, true), $mappingInfo, $from);
        $this->assertIsArray($datas);
        $this->assertNotEmpty($datas);
    }

    public function provideTeamExtractorData()
    {
        return [
            '[{"squadName":"Super hero squad","homeTown":"Metro City","formed":2016,"secretBase":"Super tower","active":true,"members":[{"name":"Molecule Man","age":29,"secretIdentity":"Dan Jukes","powers":[{"power_code":"RR","strengh":1},{"power_code":"TT","strengh":5},{"power_code":"RB","strengh":10}]},{"name":"Madame Uppercut","age":39,"secretIdentity":"Jane Wilson","powers":[{"power_code":"MTP","strengh":6},{"power_code":"DR","strengh":5},{"power_code":"SR","strengh":10}]},{"name":"Eternal Flame","age":1000000,"secretIdentity":"Unknown","powers":[{"power_code":"IM","strengh":3},{"power_code":"HI","strengh":10},{"power_code":"IF","strengh":2},{"power_code":"TEL","strengh":6},{"power_code":"IT","strengh":5}]}]}]',
            '{"property_type_extractor":"App\\PropertyTypeExtractor\\TeamPropertyTypeExtractor","filename":"teams","root":"teams","class":"App\\Entity\\Team","children":{"members":{"filename":"team_members"}}}',
            'App\PropertyTypeExtractor\TeamPropertyTypeExtractor',
            'json',
            'csv'
        ];
    }

}
