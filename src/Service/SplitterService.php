<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Serializer;

class SplitterService
{
    public function __construct(
        Filesystem $filesystem,
        ParameterBagInterface $params
    ) {
        $this->filesystem = $filesystem;
        $this->params = $params;
    }

    public function process(string $file, string $mapping, string $from, string $to)
    {
        try {
            $outputDir   = $this->params->get("kernel.project_dir").'/files/output/'.$to;
            // specify which mapping should be used defined in mapping.yaml
            $mappingInfo = $this->params->get('mapping')[$mapping];

            // find a specific serializer to convert $from (json, xml...) to $to (csv, yaml...)
            $serializer  = $this->getSerializer($from, $to, $mappingInfo['property_type_extractor']??[]);

            // open and read the file
            //@todo: the checks and validation are made in the command but should be done here.
            $jsonContent = file_get_contents($file);
            $json = json_decode($jsonContent, true);

            // use the mapping definition to get desired parts of the object
            $extracts = $this->extractFromMapping($json[$mappingInfo['root']], $serializer, $mappingInfo, $from);

            // write to output files the selection extracted
            foreach($extracts as $filename => $extract) {
                $writeFile = sprintf("%s/%s-%s.%s", $outputDir, $filename, time(), $to);
                $this->filesystem->appendToFile($writeFile, $serializer->serialize($extract, $to));
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $extracts??[];
    }

    private function extractFromMapping(array $heap, Serializer $serializer, array $mappingInfo, string $from): array {

        $datas = [];
        $datas[$mappingInfo['filename']] = [];
        foreach($heap as $objectParam) {
            // deserialize team according to class mapping and $from (input file json, xml, csv...)
            $object = $serializer->deserialize(json_encode($objectParam), $mappingInfo['class'], $from);

            // use the interfaced method getSummary() from SplitterInterface in App\Entity\Interface\
            $datas[$mappingInfo['filename']][] = $object->getSummary();

            //@todo: should be a recursive access property to permit large relation object mapping
            if(isset($mappingInfo['children'])) {

                // accessing children of the relation oneToMany defined in the mapping.yaml
                //@todo: should be able to deal with all the types of relationships ?
                foreach($mappingInfo['children'] as $attribute => $child) {
                    if(!isset($datas[$child['filename']])) {
                        $datas[$child['filename']] = [];
                    }
                    $getterMethod = sprintf("get%s", ucfirst($attribute));
                    foreach($object->$getterMethod() as $childObject) {

                        // use the interfaced method getSummary() from SplitterInterface in App\Entity\Interface\
                        $datas[$child['filename']][] = $childObject->getSummary();
                    }
                }
            }
        }

        return $datas;
    }

    public function getSerializer(string $from, string $to, ?string $propertyTypeExtrator=null): Serializer
    {
        $encoders = [];
        // retrive the needed encodes depended of from and to (xml, json, csv...)
        foreach([$from, $to] as $encoder) {
            $class = sprintf("\Symfony\Component\Serializer\Encoder\%sEncoder", ucfirst($encoder));
            if(! class_exists($class)) {
                throw new \InvalidArgumentException("class $class does not exist.");
            }
            $encoders[] = new $class();
        }

        // inject the $propertyTypeExtrator to deal with oneToMany Relation
        //@todo: can't see how to do another way.
        if(! class_exists($propertyTypeExtrator)) {
            throw new \InvalidArgumentException("class $propertyTypeExtrator does not exist.");
        }
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, $propertyTypeExtrator?new $propertyTypeExtrator():null)
        ];

        return new Serializer($normalizers, $encoders);
    }
}
