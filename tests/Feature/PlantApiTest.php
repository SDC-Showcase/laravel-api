<?php

namespace Tests\Feature;

use Tests\TestCase;

class PlantApiTest extends TestCase
{
    protected $baseUrl = '/api/v1';
    protected $plant_id = 13597;

    /**
     * Test that the endpoint returns a successful response.
     *
     * @return void
     */
    public function testPlantEndpointReturnsSuccessfulResponse()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function testMultipleEndpoints()
    {
        $endpoints = [
            '/plants',
            '/plants/13597',
            '/fields',
            '/references',
            '/references/1',
            '/plants?distribution_text=*North*',
            '/plants?author=*Mill*&species=latifolia',
            '/plants?author=*Mill*&glands=unknown&distribution_text=*Tropical America*',
            '/plants?date=2023-10-03',
            '/plants?updated_at=2024-01-25',
            '/plants?date=2023-10-03,2024-12-23',
            '/plants?updated_at=2024-01-01,2025-09-05',
            '/plants?sort=genus',
        ];

       foreach ($endpoints as $endpoint) {
            $response = $this->get($this->baseUrl . $endpoint);

            // Check if 'data' exists in the JSON response
            $json = $response->json();
            if (!array_key_exists('data', $json)) {
                $this->fail("Failed asserting that endpoint {$endpoint} returned JSON with a 'data' attribute. Response content: " . json_encode($json));
            }

            // Assert the response status is 200
            $response->assertStatus(200);

        }
    }


    /**
     * Test the structure of the response.
     *
     * @return void
     */
    public function testPlantResponseHasCorrectStructure()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $response->assertJson([
            'data' => [
                'id' => $this->plant_id,
            ]
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'date',
                'updated_at',
                'fields',
                'images'
            ]
        ]);
    }

    /**
     * Test the specific plant data.
     *
     * @return void
     */
    public function testPlantDataMatchesExpectedValues()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);
        $plant = $responseData['data'];

        // Find the taxonomy fields
        $family = $this->findFieldByName($plant['fields'], 'family');
        $genus = $this->findFieldByName($plant['fields'], 'genus');
        $species = $this->findFieldByName($plant['fields'], 'species');

        // Assert the taxonomy values
        $this->assertEquals('Amaranthaceae', $family['value']);
        $this->assertEquals('Atriplex', $genus['value']);
        $this->assertEquals('isatidea', $species['value']);
    }

    /**
     * Test that the response includes field references when applicable.
     *
     * @return void
     */
    public function testFieldReferencesAreIncludedWhenAvailable()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        // Find max_salinity fields (which should have references)
        $maxSalinityFields = array_filter($responseData['data']['fields'], function($field) {
            return $field['name'] === 'max_salinity';
        });

        // We should have max_salinity fields with references
        $this->assertNotEmpty($maxSalinityFields);

        // Check each max_salinity field has references
        foreach ($maxSalinityFields as $field) {
            $this->assertArrayHasKey('references', $field);
            $this->assertIsArray($field['references']);
            $this->assertNotEmpty($field['references']);

            // Check structure of the first reference
            $this->assertArrayHasKey('id', $field['references'][0]);
            $this->assertArrayHasKey('type', $field['references'][0]);
            $this->assertArrayHasKey('Authors', $field['references'][0]);
            $this->assertArrayHasKey('Title', $field['references'][0]);
        }
    }

    /**
     * Test the plant images data.
     *
     * @return void
     */
    public function testPlantImagesDataIsCorrectlyStructured()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        // Check that images array is not empty
        $this->assertNotEmpty($responseData['data']['images']);

        // Check structure of the first image
        $this->assertArrayHasKey('id', $responseData['data']['images'][0]);
        $this->assertArrayHasKey('fileName', $responseData['data']['images'][0]);
    }

    /**
     * Test for expected economic uses.
     *
     * @return void
     */
    public function testPlantHasExpectedEconomicUses()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        // Find all economic_Use fields
        $economicUses = array_filter($responseData['data']['fields'], function($field) {
            return $field['name'] === 'economic_use';
        });

        // Extract just the values
        $useValues = array_map(function($field) {
            return $field['value'];
        }, $economicUses);

        // Check for expected uses
        $this->assertContains('3100.0 Grazing', $useValues);
    }

    /**
     * Test for secondary metabolites.
     *
     * @return void
     */
    public function testPlantHasExpectedSecondaryMetabolites()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        // Find all secondary_metabolites fields
        $metabolites = array_filter($responseData['data']['fields'], function($field) {
            return $field['name'] === 'secondary_metabolites';
        });

        // Extract just the values
        $metaboliteValues = array_map(function($field) {
            return $field['value'];
        }, $metabolites);

        // Check for expected metabolites
        $this->assertContains('sctest', $metaboliteValues);
    }

    /**
     * Test error handling for non-existent plant ID.
     *
     * @return void
     */
    public function testReturnsAppropriateErrorForNonExistentPlantID()
    {
        $nonExistentId = 99999999;
        $response = $this->get($this->baseUrl . "/plants/{$nonExistentId}");

        // Expect 404 Not Found or similar error code
        $this->assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    /**
     * Helper function to find a field by name.
     *
     * @param array $fields
     * @param string $name
     * @return array|null
     */
    protected function findFieldByName(array $fields, string $name)
    {
        foreach ($fields as $field) {
            if ($field['name'] === $name) {
                return $field;
            }
        }
        return null;
    }
}