<?php

namespace Ministra\Lib\StbApi;

interface TvArchive
{
    public function createLink();
    public function getLinkForChannel();
    public function getNextPartUrl();
    public function setPlayed();
    public function setPlayedTimeshift();
    public function updatePlayedEndTime();
    public function updatePlayedTimeshiftEndTime();
}
