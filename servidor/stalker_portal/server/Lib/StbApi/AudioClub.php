<?php

namespace Ministra\Lib\StbApi;

interface AudioClub
{
    public function createLink();
    public function getCategories();
    public function getOrderedList();
    public function getTrackList();
    public function getUserPlaylists();
    public function createPlaylist();
    public function addTrackToPlaylist();
    public function removeFromPlaylist();
    public function deletePlaylist();
    public function trackSearch();
    public function albumSearch();
}
