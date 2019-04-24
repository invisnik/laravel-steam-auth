<?php

namespace Invisnik\LaravelSteamAuth\Tests;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get data for tests.
     *
     * @param string $steamId
     *
     * @return mixed
     */
    protected function getSteamInfoData($steamId = '76561198061912622')
    {
        $filepath = realpath(__DIR__.'/data').'/'.$steamId.'.json';

        if (! file_exists($filepath)) {
            throw new InvalidArgumentException('there is no steaminfo data file for given steamid');
        }

        return Arr::get(
            json_decode(file_get_contents($filepath), true),
            'response.players.0'
        );
    }
}
