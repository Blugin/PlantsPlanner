<?php
declare(strict_types=1);

namespace kim\present\plantsplaner;

use kim\present\plantsplaner\block\BeetrootPlants;
use kim\present\plantsplaner\block\CactusPlants;
use kim\present\plantsplaner\block\CarrotPlants;
use kim\present\plantsplaner\block\MelonStemPlants;
use kim\present\plantsplaner\block\PotatoPlants;
use kim\present\plantsplaner\block\PumpkinStemPlants;
use kim\present\plantsplaner\block\SugarcanePlants;
use kim\present\plantsplaner\block\WheatPlants;
use kim\present\plantsplaner\data\BearablePlantsData;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\data\StackablePlantsData;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\TileFactory;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\RegistryTrait;
use pocketmine\utils\SingletonTrait;

/**
 * @method static PlantsData WHEAT()
 * @method static PlantsData POTATO()
 * @method static PlantsData CARROT()
 * @method static PlantsData BEETROOT()
 *
 * @method static BearablePlantsData MELON_STEM()
 * @method static BearablePlantsData PUMPKIN_STEM()
 *
 * @method static StackablePlantsData SUGARCANE()
 * @method static StackablePlantsData CACTUS()
 */
final class Loader extends PluginBase{
    use SingletonTrait, RegistryTrait;

    public static int $updateDelay = 60 * 20;

    protected function onLoad() : void{
        self::$instance = $this;

        //Register Plants tile
        TileFactory::getInstance()->register(Plants::class, ["Plants", "plantsplanner:plants"]);

        //Resiter plants blocks
        $factory = BlockFactory::getInstance();
        $factory->register(new WheatPlants(new BlockIdentifier(BlockLegacyIds::WHEAT_BLOCK, 0, ItemIds::WHEAT_SEEDS, Plants::class), "Wheat Block"), true);
        $factory->register(new PotatoPlants(new BlockIdentifier(BlockLegacyIds::POTATO_BLOCK, 0, ItemIds::POTATO, Plants::class), "Potato Block"), true);
        $factory->register(new CarrotPlants(new BlockIdentifier(BlockLegacyIds::CARROT_BLOCK, 0, ItemIds::CARROT, Plants::class), "Carrot Block"), true);
        $factory->register(new BeetrootPlants(new BlockIdentifier(BlockLegacyIds::BEETROOT_BLOCK, 0, ItemIds::BEETROOT, Plants::class), "Beetroot Block"), true);
        $factory->register(new MelonStemPlants(new BlockIdentifier(BlockLegacyIds::MELON_STEM, 0, ItemIds::MELON_SEEDS, Plants::class), "Melon Stem"), true);
        $factory->register(new PumpkinStemPlants(new BlockIdentifier(BlockLegacyIds::PUMPKIN_STEM, 0, ItemIds::PUMPKIN_SEEDS, Plants::class), "Pumpkin Stem"), true);
        $factory->register(new SugarcanePlants(new BlockIdentifier(BlockLegacyIds::SUGARCANE_BLOCK, 0, ItemIds::SUGARCANE, Plants::class), "Sugarcane"), true);
        $factory->register(new CactusPlants(new BlockIdentifier(BlockLegacyIds::CACTUS, 0, ItemIds::CACTUS, Plants::class), "Cactus"), true);
    }

    private function getConfigFloat(string $k, float $default) : float{
        return (float) $this->getConfig()->getNested($k, $default);
    }

    protected static function setup() : void{
        $config = self::getInstance();
        self::$updateDelay = max(1, (int) ($config->getConfigFloat("global.update-delay", 60) * 20));
        self::_registryRegister("wheat", new PlantsData($config->getConfigFloat("wheat.grow-seconds", 60.0)));
        self::_registryRegister("potato", new PlantsData($config->getConfigFloat("potato.grow-seconds", 60.0)));
        self::_registryRegister("carrot", new PlantsData($config->getConfigFloat("carrot.grow-seconds", 60.0)));
        self::_registryRegister("beetroot", new PlantsData($config->getConfigFloat("beetroot.grow-seconds", 60.0)));
        self::_registryRegister("melon_stem", new BearablePlantsData(
            $config->getConfigFloat("mellon_stem.grow-seconds", 30.0),
            $config->getConfigFloat("mellon_stem.bear-seconds", 300.0)
        ));
        self::_registryRegister("pumpkin_stem", new BearablePlantsData(
            $config->getConfigFloat("pumpkin_stem.grow-seconds", 30.0),
            $config->getConfigFloat("pumpkin_stem.bear-seconds", 300.0)
        ));
        self::_registryRegister("sugarcane", new StackablePlantsData(
            $config->getConfigFloat("sugarcane.grow-seconds", 60.0),
            (int) $config->getConfigFloat("sugarcane.max-height", 3)
        ));
        self::_registryRegister("cactus", new StackablePlantsData(
            $config->getConfigFloat("cactus.grow-seconds", 60.0),
            (int) $config->getConfigFloat("cactus.max-height", 3)
        ));
    }
}