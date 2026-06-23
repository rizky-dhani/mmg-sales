<?php

use App\Services\ResourceCodeGenerator;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::table('code_sequences')->truncate();
});

describe('ResourceCodeGenerator', function () {
    it('generates code with correct format for lead', function () {
        $generator = app(ResourceCodeGenerator::class);
        $code = $generator->generate('LEA');

        expect($code)->toMatch('/^MMG-LEA-\d{6}$/');
    });

    it('generates sequential codes', function () {
        $generator = app(ResourceCodeGenerator::class);

        $code1 = $generator->generate('LEA');
        $code2 = $generator->generate('LEA');
        $code3 = $generator->generate('LEA');

        expect($code1)->toBe('MMG-LEA-000001');
        expect($code2)->toBe('MMG-LEA-000002');
        expect($code3)->toBe('MMG-LEA-000003');
    });

    it('generates different codes for different prefixes', function () {
        $generator = app(ResourceCodeGenerator::class);

        $leadCode = $generator->generate('LEA');
        $activityCode = $generator->generate('ACT');
        $customerCode = $generator->generate('CST');

        expect($leadCode)->toBe('MMG-LEA-000001');
        expect($activityCode)->toBe('MMG-ACT-000001');
        expect($customerCode)->toBe('MMG-CST-000001');
    });

    it('generates order number with year', function () {
        $generator = app(ResourceCodeGenerator::class);
        $code = $generator->generateForOrder(2025);

        expect($code)->toMatch('/^MMG-ORD-2025-\d{6}$/');
        expect($code)->toBe('MMG-ORD-2025-000001');
    });

    it('generates sequential order numbers within same year', function () {
        $generator = app(ResourceCodeGenerator::class);

        $code1 = $generator->generateForOrder(2025);
        $code2 = $generator->generateForOrder(2025);
        $code3 = $generator->generateForOrder(2025);

        expect($code1)->toBe('MMG-ORD-2025-000001');
        expect($code2)->toBe('MMG-ORD-2025-000002');
        expect($code3)->toBe('MMG-ORD-2025-000003');
    });

    it('resets order sequence for new year', function () {
        $generator = app(ResourceCodeGenerator::class);

        $generator->generateForOrder(2025);
        $generator->generateForOrder(2025);
        $code2026 = $generator->generateForOrder(2026);

        expect($code2026)->toBe('MMG-ORD-2026-000001');
    });

    it('generates codes case insensitive prefix', function () {
        $generator = app(ResourceCodeGenerator::class);

        $code = $generator->generate('lea');

        expect($code)->toMatch('/^MMG-LEA-\d{6}$/');
    });

    it('generates unique codes under concurrent-like load', function () {
        $generator = app(ResourceCodeGenerator::class);

        $codes = collect(range(1, 10))->map(fn () => $generator->generate('LEA'))->toArray();

        expect(array_unique($codes))->toHaveCount(10);
    });
});
