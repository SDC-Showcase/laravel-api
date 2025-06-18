<?php

namespace Tests\Feature;

use Tests\TestCase;
use DateTime;

class PlantApiAdvancedTest extends TestCase
{
    protected $baseUrl = '/api/v1';
    protected $plant_id = 13597;

    /**
     * Test for validation of datetime format.
     *
     * @return void
     */
    public function testDateFieldsHaveValidIsoFormat()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);
        $plant = $responseData['data'];

        // Check date formats
        $this->assertTrue($this->isValidISODateTime($plant['date']));
        $this->assertTrue($this->isValidISODateTime($plant['updated_at']));
    }

    /**
     * Test performance - response time should be acceptable.
     *
     * @return void
     */
    public function testEndpointRespondsWithinAcceptableTime()
    {
        $startTime = microtime(true);
        $this->get($this->baseUrl . "/plants/{$this->plant_id}");
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        // Response should be under 1 second (adjust as needed)
        $this->assertLessThan(0.5, $responseTime, "API response time too slow: {$responseTime}s");
    }

    /**
     * Test for consistent field properties across all fields.
     *
     * @return void
     */
    public function testAllFieldsHaveConsistentPropertyStructure()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        foreach ($responseData['data']['fields'] as $field) {

            $this->assertArrayHasKey('name', $field);
            $this->assertArrayHasKey('question', $field);
            $this->assertArrayHasKey('value', $field);


            // Validate name and question are non-empty strings
            $this->assertIsString($field['name']);
            $this->assertGreaterThan(0, strlen($field['name']));

            $this->assertIsString($field['question']);
            $this->assertGreaterThan(0, strlen($field['question']));

        }
    }

    /**
     * Test for salinity-related data consistency.
     *
     * @return void
     */
    public function skip_testSalinityDataIsConsistentAcrossFields()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        // Find max salinity fields
        $maxSalinityFields = array_filter($responseData['data']['fields'], function($field) {
            return $field['name'] === 'max_salinity';
        });

        // Find optimal salinity fields
        $optimalSalinityFields = array_filter($responseData['data']['fields'], function($field) {
            return $field['name'] === 'optimal_salinity';
        });

        // Extract numeric values
        $maxValues = array_map(function($field) {
            return floatval($field['value']);
        }, $maxSalinityFields);

        $optimalValues = array_map(function($field) {
            return floatval($field['value']);
        }, $optimalSalinityFields);

        // Check consistency: optimal values should be less than or equal to max values
        $maxOfOptimal = max($optimalValues);
        $maxOfMax = max($maxValues);

        $this->assertLessThanOrEqual($maxOfMax, $maxOfOptimal);
    }

    /**
     * Test for consistent references structure.
     *
     * @return void
     */
    public function testReferencesHaveConsistentStructure()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        $fieldsWithReferences = array_filter($responseData['data']['fields'], function($field) {
            return isset($field['references']) && !empty($field['references']);
        });

        foreach ($fieldsWithReferences as $field) {
            foreach ($field['references'] as $reference) {
                // All references should have these base fields
                $this->assertArrayHasKey('id', $reference);
                $this->assertArrayHasKey('type', $reference);

                // Type should be one of the known types
                $this->assertContains($reference['type'], ['J', 'B', 'CH']);

                // ID should be a positive integer
                $this->assertIsInt($reference['id']);
                $this->assertGreaterThan(0, $reference['id']);

                // Authors should be present
                $this->assertArrayHasKey('Authors', $reference);

                // If it's a journal article (type J), it should have these fields
                if ($reference['type'] === 'J') {
                    $this->assertArrayHasKey('Title', $reference);
                    $this->assertArrayHasKey('Source', $reference);
                }
            }
        }
    }

    /**
     * Test for additional fields presence.
     *
     * @return void
     */
    public function testPlantHasRequiredTaxonomyFields()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        $fields = $responseData['data']['fields'];

        // Helper to check if a field exists with a specific name
        $hasField = function($name) use ($fields) {
            return !empty(array_filter($fields, fn($f) => $f['name'] === $name));
        };

        // Check for required taxonomy fields
        $this->assertTrue($hasField('family'));
        $this->assertTrue($hasField('genus'));
        $this->assertTrue($hasField('species'));
        $this->assertTrue($hasField('author'));
    }

    /**
     * Test that image filenames match expected format.
     *
     * @return void
     */
    public function skip_testImageFilenamesMatchExpectedFormat()
    {
        $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");

        $responseData = json_decode($response->getContent(), true);

        foreach ($responseData['data']['images'] as $image) {
            // Check that filename matches expected format (may need adjustment based on actual format)
            $this->assertMatchesRegularExpression('/^\d+_\d{4}-\d{2}-\d{2}_.*\.jpg$/', $image['fileName']);
        }
    }

    /**
     * Helper function to validate ISO8601 datetime.
     *
     * @param string $dateString
     * @return bool
     */
    protected function isValidISODateTime($dateString)
    {
        $d = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $dateString);
        return $d && $d->format('Y-m-d\TH:i:s.u\Z') === $dateString;
    }

}
