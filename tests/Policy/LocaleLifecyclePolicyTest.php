<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Policy;

use App\Localizing\Policy\LocaleLifecyclePolicy;
use PHPUnit\Framework\TestCase;

final class LocaleLifecyclePolicyTest extends TestCase
{
    public function testAllowsDocumentedTransitionsAndNormalizesInput(): void
    {
        $policy = new LocaleLifecyclePolicy();

        self::assertTrue($policy->canTransition(' Draft ', 'ENABLED'));
        self::assertTrue($policy->canTransition('enabled', 'enabled'));
        self::assertFalse($policy->canTransition('unknown', 'enabled'));
        $policy->assertCanTransition('draft', 'enabled');
        $this->addToAssertionCount(1);
        self::assertSame(['fallback_only', 'disabled', 'deprecated'], $policy->allowedNextStatuses(' ENABLED '));
        self::assertSame([], $policy->allowedNextStatuses('unknown'));
    }

    public function testArchivedStatusIsTerminal(): void
    {
        $policy = new LocaleLifecyclePolicy();

        self::assertSame([], $policy->allowedNextStatuses('archived'));
        self::assertFalse($policy->canTransition('archived', 'enabled'));
    }

    public function testRejectsInvalidTransition(): void
    {
        $policy = new LocaleLifecyclePolicy();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invalid locale lifecycle transition');
        $policy->assertCanTransition('draft', 'archived');
    }
}
