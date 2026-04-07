<?php

use App\Imports\CustomersImport;

beforeEach(function () {
    $this->import = new CustomersImport;
});

test('normalizeIsActive returns 1 for null value', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, null))->toBe(1);
});

test('normalizeIsActive returns 1 for active string', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'Active'))->toBe(1);
    expect($method->invoke($this->import, 'active'))->toBe(1);
    expect($method->invoke($this->import, 'ACTIVE'))->toBe(1);
});

test('normalizeIsActive returns 0 for inactive string', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'Inactive'))->toBe(0);
    expect($method->invoke($this->import, 'inactive'))->toBe(0);
    expect($method->invoke($this->import, 'INACTIVE'))->toBe(0);
});

test('normalizeIsActive handles yes/no values', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'Yes'))->toBe(1);
    expect($method->invoke($this->import, 'yes'))->toBe(1);
    expect($method->invoke($this->import, 'No'))->toBe(0);
    expect($method->invoke($this->import, 'no'))->toBe(0);
});

test('normalizeIsActive handles integer values', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 1))->toBe(1);
    expect($method->invoke($this->import, 0))->toBe(0);
    expect($method->invoke($this->import, '1'))->toBe(1);
    expect($method->invoke($this->import, '0'))->toBe(0);
});

test('normalizeIsActive handles Indonesian values', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'Aktif'))->toBe(1);
    expect($method->invoke($this->import, 'aktif'))->toBe(1);
    expect($method->invoke($this->import, 'Tidak'))->toBe(0);
    expect($method->invoke($this->import, 'tidak'))->toBe(0);
});

test('normalizeIsActive handles boolean string values', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'true'))->toBe(1);
    expect($method->invoke($this->import, 'false'))->toBe(0);
    expect($method->invoke($this->import, 'True'))->toBe(1);
    expect($method->invoke($this->import, 'False'))->toBe(0);
});

test('normalizeIsActive returns integer for integer input', function () {
    $method = new ReflectionMethod($this->import, 'normalizeIsActive');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 1))->toBe(1);
    expect($method->invoke($this->import, 0))->toBe(0);
    expect($method->invoke($this->import, 5))->toBe(1);
});

test('normalizeCdNcdType returns null for null value', function () {
    $method = new ReflectionMethod($this->import, 'normalizeCdNcdType');
    $method->setAccessible(true);

    expect($method->invoke($this->import, null))->toBeNull();
});

test('normalizeCdNcdType handles direct CD and NCD values', function () {
    $method = new ReflectionMethod($this->import, 'normalizeCdNcdType');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'CD'))->toBe('CD');
    expect($method->invoke($this->import, 'cd'))->toBe('CD');
    expect($method->invoke($this->import, 'NCD'))->toBe('NCD');
    expect($method->invoke($this->import, 'ncd'))->toBe('NCD');
});

test('normalizeCdNcdType extracts type from parentheses', function () {
    $method = new ReflectionMethod($this->import, 'normalizeCdNcdType');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'LS_LIFE SCIENCE (N-CD)'))->toBe('NCD');
    expect($method->invoke($this->import, 'Some Label (CD)'))->toBe('CD');
    expect($method->invoke($this->import, 'Example (NCD)'))->toBe('NCD');
});

test('normalizeCdNcdType handles contains check', function () {
    $method = new ReflectionMethod($this->import, 'normalizeCdNcdType');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'NCD Customer'))->toBe('NCD');
    expect($method->invoke($this->import, 'CD Distributor'))->toBe('CD');
});

test('normalizeCdNcdType returns null for unrecognized values', function () {
    $method = new ReflectionMethod($this->import, 'normalizeCdNcdType');
    $method->setAccessible(true);

    expect($method->invoke($this->import, 'Unknown'))->toBeNull();
    expect($method->invoke($this->import, 'Some Random Value'))->toBeNull();
});
