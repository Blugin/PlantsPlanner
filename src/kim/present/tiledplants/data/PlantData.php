<?php
declare(strict_types=1);

namespace kim\present\tiledplants\data;

use pocketmine\utils\EnumTrait;

/**
 * @method static self WHEAT()
 * @method static self POTATO()
 * @method static self CARROT()
 * @method static self BEETROOT()
 * @method static self MELON_STEM()
 * @method static self PUMPKIN_STEM()
 */
final class PlantData{
    use EnumTrait {
        __construct as Enum___construct;
    }

    public static function register(string $name, float $growDelay) : void{
        $name = mb_strtoupper($name);
        self::$members[$name] = new self($name, $growDelay);
    }

    protected static function setup() : void{ }

    private float $growSeconds;

    private function __construct(string $name, float $growSeconds = 300.0){
        $this->Enum___construct($name);
        $this->growSeconds = $growSeconds;
    }

    public function getGrowSeconds() : float{
        return $this->growSeconds;
    }
}