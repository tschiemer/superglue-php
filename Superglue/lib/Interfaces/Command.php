<?php

namespace Superglue\Interfaces;
use Superglue\Server as SG;

interface Command {
    static public function run($argc,$argv);
}