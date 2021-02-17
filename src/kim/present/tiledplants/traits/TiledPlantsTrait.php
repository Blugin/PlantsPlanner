<?php
declare(strict_types=1);

namespace kim\present\tiledplants\traits;

use kim\present\tiledplants\block\ITiledPlant;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\tile\Plants;
use pocketmine\block\Block;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\Server;

/**
 * This trait provides a implementation for `ITiledPlant` to reduce boilerplate.
 *
 * @see ITiledPlant
 */
trait TiledPlantsTrait{
    public function onScheduledUpdate() : void{
        /** @var Block|ITiledPlant $this */
        $plantsTile = $this->pos->getWorld()->getTile($this->pos);
        if($plantsTile instanceof Plants and $plantsTile->onUpdate()){
            $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Loader::$updateDelay);
        }
    }

    public function onPostPlace() : void{
        /** @var Block|ITiledPlant $this */
        $plantsTile = $this->pos->getWorld()->getTile($this->pos);
        if(!$plantsTile instanceof Plants){
            $this->pos->getWorld()->addTile(new Plants($this->pos->getWorld(), $this->pos));
        }
    }

    public function ticksRandomly() : bool{
        return false;
    }

    public function onRandomTick() : void{
    }

    public function onGrow() : void{
        /** @var Block|ITiledPlant $this */
        $pk = new SpawnParticleEffectPacket();
        $pk->position = $this->pos;
        $pk->particleName = "minecraft:crop_growth_emitter";
        Server::getInstance()->broadcastPackets($this->pos->getWorld()->getViewersForPosition($this->pos->add(0.5, 0.5, 0.5)), [$pk]);
    }

    public function getGrowSeconds() : float{
        /** @var Block|ITiledPlant $this */
        return $this->getPlantData()->getGrowSeconds();
    }
}