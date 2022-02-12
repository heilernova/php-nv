<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Config;

use Phpnv\Api\Config\ConfigInfo;
use Phpnv\Api\Config\Developers\Developer;

class Developers
{
    public function __construct(private $data){}

    public function add(string $name, string $email)
    {
        $this->data->developers[] = new Developer($name, $email);
    }

    public function list():array
    {
        return $this->data->developers;
    }
}