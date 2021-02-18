<?php
declare(strict_types=1);

namespace kim\present\plantsplaner;

use kim\present\plantsplaner\block\BambooPlants;
use kim\present\plantsplaner\block\BambooSaplingPlants;
use kim\present\plantsplaner\block\BeetrootPlants;
use kim\present\plantsplaner\block\CactusPlants;
use kim\present\plantsplaner\block\CarrotPlants;
use kim\present\plantsplaner\block\CocoaPlants;
use kim\present\plantsplaner\block\MelonStemPlants;
use kim\present\plantsplaner\block\PotatoPlants;
use kim\present\plantsplaner\block\PumpkinStemPlants;
use kim\present\plantsplaner\block\SaplingPlants;
use kim\present\plantsplaner\block\SugarcanePlants;
use kim\present\plantsplaner\block\WheatPlants;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\tile\TileFactory;
use pocketmine\block\utils\TreeType;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class Loader extends PluginBase{
    use SingletonTrait;

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

        $factory->register(new CocoaPlants(new BlockIdentifier(BlockLegacyIds::COCOA_BLOCK, 0, null, Plants::class), "Cocoa Beans"), true);
        $factory->register(new BambooSaplingPlants(new BlockIdentifier(BlockLegacyIds::BAMBOO_SAPLING, 0, ItemIds::BAMBOO, Plants::class), "Bamboo Sapling", BlockBreakInfo::instant()), true);

        $factory->register(new MelonStemPlants(new BlockIdentifier(BlockLegacyIds::MELON_STEM, 0, ItemIds::MELON_SEEDS, Plants::class), "Melon Stem"), true);
        $factory->register(new PumpkinStemPlants(new BlockIdentifier(BlockLegacyIds::PUMPKIN_STEM, 0, ItemIds::PUMPKIN_SEEDS, Plants::class), "Pumpkin Stem"), true);

        $factory->register(new SugarcanePlants(new BlockIdentifier(BlockLegacyIds::SUGARCANE_BLOCK, 0, ItemIds::SUGARCANE, Plants::class), "Sugarcane"), true);
        $factory->register(new CactusPlants(new BlockIdentifier(BlockLegacyIds::CACTUS, 0, ItemIds::CACTUS, Plants::class), "Cactus"), true);
        $factory->register(new BambooPlants(new BlockIdentifier(BlockLegacyIds::BAMBOO, 0, ItemIds::BAMBOO, Plants::class), "Bamboo", new BlockBreakInfo(2.0, BlockToolType::AXE)), true);

        foreach(TreeType::getAll() as $treeType){
            $sapling = new SaplingPlants(new BlockIdentifier(BlockLegacyIds::SAPLING, $treeType->getMagicNumber()), $treeType->getDisplayName() . " Sapling", $treeType);
            $factory->register(clone $sapling, true);
            $factory->register($sapling->setReady(true), true);
        }
    }
}