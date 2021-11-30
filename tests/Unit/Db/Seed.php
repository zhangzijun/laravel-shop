<?php
/**
 * Copyright (c) 2020,2345
 * 摘    要：
 * 作    者：张子骏
 * 修改日期：2021-11-26 10:26
 */


namespace Tests\Unit\Db;


use Database\Seeders\UsersSeeder;
use PHPUnit\Framework\TestCase;

class Seed extends TestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testSeedUser()
    {
        app(UsersSeeder::class)->run();
    }
}
