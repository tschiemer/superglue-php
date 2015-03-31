<?php

namespace Superglue\Interfaces;

interface Command {
    static public function run($argc,$argv);
}