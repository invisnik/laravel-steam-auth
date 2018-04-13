<?php

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function getSteamInfoData($steamId = '76561198061912622')
    {
        $filepath = realpath(__DIR__.'/data').'/'.$steamId.'.json';

        if (! file_exists($filepath)) {
            throw new InvalidArgumentException('there is no steaminfo data file for given steamid');
        }

        return array_get(json_decode(file_get_contents($filepath), true), 'response.players.0');
    }
}
