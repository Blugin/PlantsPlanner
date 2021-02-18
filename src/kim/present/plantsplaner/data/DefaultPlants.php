<?php
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