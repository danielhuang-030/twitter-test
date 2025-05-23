<?php

namespace Tests\Unit\Requests\Api\v1\Post;

use App\Http\Requests\Api\v1\Post\StoreRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PostStoreRequestTest extends TestCase
{
    public function testAuthorization(): void
    {
        $request = new StoreRequest();
        $this->assertTrue($request->authorize());
    }

    public function testRules(): void
    {
        $request = new StoreRequest();
        $rules = $request->rules();

        // Scenario 1: Provide valid content
        $validData = ['content' => 'This is a valid post content.'];
        $validatorValid = Validator::make($validData, $rules);
        $this->assertFalse($validatorValid->fails(), 'Validator should pass with valid content.');

        // Scenario 2: Content is empty (validation fails)
        $emptyContentData = ['content' => ''];
        $validatorEmptyContent = Validator::make($emptyContentData, $rules);
        $this->assertTrue($validatorEmptyContent->fails(), 'Validator should fail when content is empty.');
        $this->assertArrayHasKey('content', $validatorEmptyContent->errors()->toArray(), "Error messages should contain 'content' key for empty content.");

        // Scenario 3: Content not provided (validation fails)
        $noContentData = []; // Or provide other irrelevant fields
        $validatorNoContent = Validator::make($noContentData, $rules);
        $this->assertTrue($validatorNoContent->fails(), 'Validator should fail when content is not provided.');
        $this->assertArrayHasKey('content', $validatorNoContent->errors()->toArray(), "Error messages should contain 'content' key when content is missing.");

        // Scenario 4: Content is not a string (e.g., array)
        $nonStringContentData = ['content' => ['array instead of string']];
        $validatorNonStringContent = Validator::make($nonStringContentData, $rules);
        $this->assertTrue($validatorNonStringContent->fails(), 'Validator should fail when content is not a string.');
        $this->assertArrayHasKey('content', $validatorNonStringContent->errors()->toArray(), "Error messages should contain 'content' key for non-string content.");

        // Scenario 5: Content is a number (should fail due to 'string' rule)
        $numericContentData = ['content' => 12345];
        $validatorNumericContent = Validator::make($numericContentData, $rules);
        $this->assertTrue($validatorNumericContent->fails(), 'Validator should fail when content is numeric but rule expects string.');
        $this->assertArrayHasKey('content', $validatorNumericContent->errors()->toArray(), "Error messages should contain 'content' key for numeric content when string is expected.");
    }

    // Optional: testMessages if StoreRequest customizes messages
    // public function testMessages(): void
    // {
    //     $request = new StoreRequest();
    //     $messages = $request->messages();
    //     // Add assertions for custom messages if any
    //     // e.g., $this->assertEquals('Your custom message for content.required', $messages['content.required']);
    // }
}
