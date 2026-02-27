<?php

declare(strict_types=1);

use AdroSoftware\LaravelModelPresenter\Support\Json\Json;

describe('Json::encode', function () {
    it('encodes array to JSON string', function () {
        $data = ['name' => 'John', 'age' => 30];
        $json = Json::encode($data);

        expect($json)->toBe('{"name":"John","age":30}');
    });

    it('encodes with options', function () {
        $data = ['name' => 'John'];
        $json = Json::encode($data, JSON_PRETTY_PRINT);

        expect($json)->toContain("\n");
    });

    it('encodes nested arrays', function () {
        $data = ['user' => ['name' => 'John', 'roles' => ['admin', 'editor']]];
        $json = Json::encode($data);

        expect($json)->toBe('{"user":{"name":"John","roles":["admin","editor"]}}');
    });

    it('encodes empty array', function () {
        $json = Json::encode([]);

        expect($json)->toBe('[]');
    });

    it('encodes null value', function () {
        $json = Json::encode(null);

        expect($json)->toBe('null');
    });

    it('encodes scalar values', function () {
        expect(Json::encode('string'))->toBe('"string"');
        expect(Json::encode(123))->toBe('123');
        expect(Json::encode(true))->toBe('true');
        expect(Json::encode(false))->toBe('false');
    });

    it('throws exception on encoding error', function () {
        $resource = fopen('php://memory', 'r');
        Json::encode($resource);
        fclose($resource);
    })->throws(InvalidArgumentException::class, 'json_encode error');
});

describe('Json::decode', function () {
    it('decodes JSON string to object', function () {
        $json = '{"name":"John","age":30}';
        $data = Json::decode($json);

        expect($data)->toBeObject();
        expect($data->name)->toBe('John');
        expect($data->age)->toBe(30);
    });

    it('decodes JSON string to associative array', function () {
        $json = '{"name":"John","age":30}';
        $data = Json::decode($json, true);

        expect($data)->toBeArray();
        expect($data['name'])->toBe('John');
        expect($data['age'])->toBe(30);
    });

    it('decodes nested JSON', function () {
        $json = '{"user":{"name":"John","roles":["admin","editor"]}}';
        $data = Json::decode($json, true);

        expect($data['user']['name'])->toBe('John');
        expect($data['user']['roles'])->toBe(['admin', 'editor']);
    });

    it('decodes empty array', function () {
        $data = Json::decode('[]', true);

        expect($data)->toBe([]);
    });

    it('decodes null', function () {
        $data = Json::decode('null');

        expect($data)->toBeNull();
    });

    it('throws exception on invalid JSON', function () {
        Json::decode('invalid json');
    })->throws(InvalidArgumentException::class, 'json_decode error');

    it('throws exception on malformed JSON', function () {
        Json::decode('{"name": }');
    })->throws(InvalidArgumentException::class, 'json_decode error');
});
