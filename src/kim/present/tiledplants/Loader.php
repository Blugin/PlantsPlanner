<?php
declare(strict_types=1);

namespace kim\present\tiledplants;

use kim\present\tiledplants\block\TiledBeetroot;
use kim\present\tiledplants\block\TiledCarrot;
use kim\present\tiledplants\block\TiledMelonStem;
use kim\present\tiledplants\block\TiledPotato;
use kim\present\tiledplants\block\TiledPumpkinStem;
use kim\present\tiledplants\block\TiledWheat;
use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\tile\Plants;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\TileFactory;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase{
    protected function onLoad() : void{
        //Load config
        Plants::$updateDelay = (int) ($this->getConfigFloat("delay", 60) * 20);
        PlantData::register("wheat", $this->getConfigFloat("growtimes.wheat", 300));
        PlantData::register("potato", $this->getConfigFloat("growtimes.potato", 300));
        PlantData::register("carrot", $this->getConfigFloat("growtimes.carrot", 300));
        PlantData::register("beetroot", $this->getConfigFloat("growtimes.beetroot", 300));
        PlantData::register("melon_stem", $this->getConfigFloat("growtimes.mellon_stem", 300));
        PlantData::register("pumpkin_stem", $this->getConfigFloat("growtimes.pumpkin_stem", 300));

        //Register Plants tile
        TileFactory::getInstance()->register(Plants::class, ["Plants", "presentkim:plants"]);

        //Resiter plant blocks
        $factory = BlockFactory::getInstance();
        $factory->register(new TiledWheat(new BlockIdentifier(BlockLegacyIds::WHEAT_BLOCK, 0, ItemIds::WHEAT_SEEDS, Plants::class), "Wheat Block"), true);
        $factory->register(new TiledPotato(new BlockIdentifier(BlockLegacyIds::POTATO_BLOCK, 0, ItemIds::POTATO, Plants::class), "Potato Block"), true);
        $factory->register(new TiledCarrot(new BlockIdentifier(BlockLegacyIds::CARROT_BLOCK, 0, ItemIds::CARROT, Plants::class), "Carrot Block"), true);
        $factory->register(new TiledBeetroot(new BlockIdentifier(BlockLegacyIds::BEETROOT_BLOCK, 0, ItemIds::BEETROOT, Plants::class), "Beetroot Block"), true);
        $factory->register(new TiledMelonStem(new BlockIdentifier(BlockLegacyIds::MELON_STEM, 0, ItemIds::MELON_SEEDS, Plants::class), "Melon Stem"), true);
        $factory->register(new TiledPumpkinStem(new BlockIdentifier(BlockLegacyIds::PUMPKIN_STEM, 0, ItemIds::PUMPKIN_SEEDS, Plants::class), "Pumpkin Stem"), true);
    }

    private function getConfigFloat(string $k, float $default) : float{
        return (float) $this->getConfig()->getNested($k, $default);
    }
}