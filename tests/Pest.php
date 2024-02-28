<?php

use Henzeb\Prompts\Inputs;
use Henzeb\Prompts\Prompt;

uses()->afterEach(
    function () {
        Prompt::resetOutput();
        Inputs::resetInput();
    }
)->in(__DIR__);
