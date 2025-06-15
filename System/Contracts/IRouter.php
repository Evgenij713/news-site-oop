<?php
declare(strict_types=1);

namespace System\Contracts;

interface IRouter{
    public function parseUrl(string $url): array;
}