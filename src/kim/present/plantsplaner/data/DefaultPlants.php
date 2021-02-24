<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection SpellCheckingInspection
 */

declare(strict_types=1);

namespace kim\present\plantsplaner\data;

use kim\present\plantsplaner\Loader;
use pocketmine\utils\RegistryTrait;

/**
 * @method static PlantsData WHEAT()
 * @method static PlantsData POTATO()
 * @method static PlantsData CARROT()
 * @method static PlantsData BEETROOT()
 * @method static PlantsData SAPLING()
 * @method static PlantsData COCOA()
 * @method static PlantsData BAMBOO_SAPLING()
 *
 * @method static BearablePlantsData MELON_STEM()
 * @method static BearablePlantsData PUMPKIN_STEM()
 *
 * @method static StackablePlantsData SUGARCANE()
 * @method static StackablePlantsData CACTUS()
 * @method static StackablePlantsData BAMBOO()
 */
final class DefaultPlants{
    use RegistryTrait;

    protected static function setup() : void{
        $config = Loader::getInstance()->getConfig();

        //Register normal plants
        foreach(["wheat", "potato", "carrot", "beetroot", "sapling", "cocoa", "bamboo_sapling"] as $name){
            self::_registryRegister($name, PlantsData::fromArray($config->get($name, [])));
        }

        //Register bearable plants
        foreach(["melon_stem", "pumpkin_stem"] as $name){
            self::_registryRegister($name, BearablePlantsData::fromArray($config->get($name, [])));
        }

        //Register stackable plants
        foreach(["sugarcane", "cactus", "bamboo"] as $name){
            self::_registryRegister($name, StackablePlantsData::fromArray($config->get($name, [])));
        }
    }
}