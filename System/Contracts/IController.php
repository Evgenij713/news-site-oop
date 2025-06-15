<?php
declare(strict_types=1);

namespace System\Contracts;

interface IController{
	public function render() : string;
}