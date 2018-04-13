<?php

use Invisnik\LaravelSteamAuth\SteamInfo;

class SteamInfoTest extends TestCase
{
    public function test_construct()
    {
        $data = $this->getSteamInfoData();
        $steamInfo = new SteamInfo($data);

        $this->assertInstanceOf(SteamInfo::class, $steamInfo);
    }

    public function test_steamid()
    {
        $data = $this->getSteamInfoData();
        $steamInfo = new SteamInfo($data);

        $this->assertArrayNotHasKey('steamid', $steamInfo);
        $this->assertArrayHasKey('steamID64', $steamInfo->toArray());
        $this->assertEquals($data['steamid'], $steamInfo['steamID64']);
    }
}
