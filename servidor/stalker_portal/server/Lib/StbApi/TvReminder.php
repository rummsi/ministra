<?php

namespace Ministra\Lib\StbApi;

interface TvReminder
{
    public function getAllActive();
    public function add();
    public function del();
}
