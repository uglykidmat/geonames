<?php

namespace App\Tests\Service;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiLevelsTest extends ApiTestCase
{
    public function testShouldExportLevelOneOnlyForCountryCodeAG(): void
    {
        $response = static::createClient()->request('GET', '/administrativedivisions/api/fr/AG');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'level1' => [
                0 => [
                    'code_up' => 'AG',
                    'code' => '01',
                    'name' => 'Barbuda',
                    'geonameId' => '3576390',
                ]
            ],
            'level2' => [],
            'level3' => [],
        ]);
    }

    public function testShouldExportLevelsOneAndTwoForCountryCodeMZ(): void
    {
        $response = static::createClient()->request('GET', '/administrativedivisions/api/fr/MZ');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'level1' => [
                0 => [
                    'code_up' => 'MZ',
                    'code' => '01',
                    'name' => 'Cabo Delgado Province',
                    'geonameId' => '1051823',
                ]
            ],

            'level2' => [
                0 => [
                    'code_up' => '01',
                    'code' => '7909876',
                    'name' => 'Ancuabe District',
                    'geonameId' => '7909876',
                ]
            ],
            'level3' => []
        ]);
    }

    public function testShouldExportLevelsOneTwoAndThreeForCountryCodeDE(): void
    {
        $response = static::createClient()->request('GET', '/administrativedivisions/api/fr/DE');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'level1' => [
                0 => [
                    'code_up' => 'DE',
                ]
            ],
            'level2' => [
                0 => [
                    'code_up' => '01',
                ]
            ],
            'level3' => [
                0 => [
                    'code_up' => '083',
                ]
            ]
        ]);
    }
}
