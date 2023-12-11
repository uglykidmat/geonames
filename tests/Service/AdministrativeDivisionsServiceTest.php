<?php

namespace App\Tests\Service;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AdministrativeDivisionsServiceTest extends ApiTestCase
{
    public function testJSONFileLevel0Has250Entries(): void
    {
        $jsonFile = json_decode(file_get_contents(__DIR__ . "/../../var/geonames_export_data/subdivisions_0_fr.json"));
        $this->assertArrayHasKey(249, $jsonFile, 'Hummmm no.');
        $this->assertArraySubset(['objectID' => '3576396'], $jsonFile[0]);
    }

    public function testJSONFileLevel1HasALotOfEntries(): void
    {
        $jsonFile = json_decode(file_get_contents(__DIR__ . "/../../var/geonames_export_data/subdivisions_1_fr.json"), true);
        $this->assertArrayHasKey(3765, $jsonFile, 'Hummmm no.');
        $this->assertArraySubset(['objectID' => '6269131'], $jsonFile[0]);
    }
    public function testJSONFileLevel2HasEvenMoreEntries(): void
    {
        $jsonFile = json_decode(file_get_contents(__DIR__ . "/../../var/geonames_export_data/subdivisions_2_fr.json"), true);
        $this->assertArrayHasKey(16684, $jsonFile, 'Hummmm no.');
        $this->assertArraySubset(['objectID' => '3333122'], $jsonFile[0]);
    }
    public function testJSONFileLevel3HasEntriesIThink(): void
    {
        $jsonFile = json_decode(file_get_contents(__DIR__ . "/../../var/geonames_export_data/subdivisions_3_fr.json"), true);
        $this->assertArrayHasKey(4266, $jsonFile, 'Hummmm no.');
        $this->assertArraySubset(['objectID' => '7299739'], $jsonFile[0]);
    }
}
