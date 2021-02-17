<?php
declare(strict_types=1);

namespace kim\present\tiledplants\tile;

use kim\present\tiledplants\block\ITiledPlant;
use kim\present\tiledplants\Loader;
use pocketmine\block\Block;
use pocketmine\block\tile\Tile;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\Server;
use pocketmine\world\World;

class Plants extends Tile{
    public const TAG_LAST_TIME = "LastTime";

    protected float $lastTime;

    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);

        $this->lastTime = microtime(true);
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Loader::$updateDelay);
    }

    public function readSaveData(CompoundTag $nbt) : void{
        $this->lastTime = $nbt->getFloat(self::TAG_LAST_TIME, microtime(true));
        $this->onUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt) : void{
        $nbt->setFloat(self::TAG_LAST_TIME, $this->lastTime);
    }

    /** @override for grow up plant */
    public function onUpdate() : bool{
        if($this->closed)
            return false;

        $block = $this->getBlock();
        if(!$block instanceof ITiledPlant || $block->isRipe())
            return false;

        $this->timings->startTiming();
        $diffSeconds = microtime(true) - $this->lastTime;
        $growSeconds = $block->getGrowSeconds();
        while(!$block->isRipe() && $diffSeconds > $growSeconds){
            $diffSeconds -= $growSeconds;
            $block->grow();

            $block = $this->getBlock();
            if(!$block instanceof ITiledPlant)
                return false;
        }
        $this->lastTime = microtime(true) - $diffSeconds;
        $this->timings->stopTiming();

        return !$block->isRipe();
    }

    public function getLastTime() : float{
        return $this->lastTime;
    }

    public function setLastTime(float $lastTime) : void{
        $this->lastTime = $lastTime;
    }

    public static function growPlant(Block $block, Block $newState) : void{
        $ev = new BlockGrowEvent($block, $newState);
        $ev->call();
        if(!$ev->isCancelled()){
            $pos = $block->getPos();
            $world = $pos->getWorld();
            $world->setBlock($pos, $ev->getNewState());

            $pk = new SpawnParticleEffectPacket();
            $pk->position = $pos->add(0.5, 0, 0.5);
            $pk->particleName = "minecraft:crop_growth_emitter";
            Server::getInstance()->broadcastPackets($world->getViewersForPosition($pos), [$pk]);
        }
    }
}