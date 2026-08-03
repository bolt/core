<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Backend;

use Bolt\Tests\DbAwareTestCase;

/**
 * Simple smoke test to verify password reset page loads successfully.
 *
 * This test was created after fixing a bug where navigating to the reset password
 * page caused a TypeError due to strict_types=1 enforcement when config->get()
 * returned DeepCollection instead of array
 *
 * The bug was in Canonical::setRequest() which didn't handle test environment properly,
 * causing parse_url() to return false/incomplete array, leading to TypeError when
 * trying to access array keys.
 */
class ResetPasswordControllerTest extends DbAwareTestCase
{
    /**
     * Test that the reset password page loads successfully.
     * This is the main regression test - the bug prevented this page from loading.
     */
    public function testResetPasswordPageLoads(): void
    {
        // Navigate directly to reset password page (this is what the "Forgotten password" link does)
        $crawler = $this->client->request('GET', '/bolt/reset-password');

        // Verify page loads successfully (this would fail with TypeError before the fix)
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('bolt_forgot_password_request');

        // Verify the form exists with email input
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="reset_password_request_form[email]"]');
    }
}
