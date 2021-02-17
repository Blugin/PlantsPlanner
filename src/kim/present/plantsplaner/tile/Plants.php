<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\tile;

use kim\present\plantsplaner\block\IPlants;
use pocketmine\block\Block;
use pocketmine\block\tile\Tile;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\Server;
use pocketmine\world\World;

/**
 * Tile class created to grow in the precise time and save data in the worlds,
 * Save the last time check time, so that the plant will grow even if the chunk is not loaded.
 */
class Plants extends Tile{
    public const TAG_LAST_TIME = "LastTime";

    /** Delay to check whether the plants is growing */
    public static int $updateDelay = 60 * 20;

    /** Last time to checked plant growth */
    protected float $lastTime;

    /** @override to register scehduling */
    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);

        $this->lastTime = microtime(true);
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Plants::$updateDelay);
    }

    /** @override to read last-time from nbt and run check-growth */
    public function readSaveData(CompoundTag $nbt) : void{
        $this->lastTime = $nbt->getFloat(self::TAG_LAST_TIME, microtime(true));
        $this->checkGrowth();
    }

    /** @override to write last-time to nbt */
    protected function writeSaveData(CompoundTag $nbt) : void{
        $nbt->setFloat(self::TAG_LAST_TIME, $this->lastTime);
    }

    /**
     * Check whether the plants is growing.
     * If returns false, Scheduling ends.
     */
    public function checkGrowth() : bool{
        if($this->closed)
            return false;

        $block = $this->getBlock();
        if(!$block instanceof IPlants || $block->canGrow()) //
            return false;

        $this->timings->startTiming();
        $diffSeconds = microtime(true) - $this->lastTime;
        $growSeconds = $block->getGrowSeconds();
        while(!$block->canGrow() && $diffSeconds > $growSeconds){
            $diffSeconds -= $growSeconds;
            $block->grow();

            $block = $this->getBlock();
            if(!$block instanceof IPlants)
                return false;
        }
        $this->lastTime = microtime(true) - $diffSeconds;
        $this->timings->stopTiming();

        return !$block->canGrow();
    }

    public function getLastTime() : float{
        return $this->lastTime;
    }

    public function setLastTime(float $lastTime) : void{
        $this->lastTime = $lastTime;
    }

    /** Convenience method for call BlockGrowEvent and spawn particle */
    public static function growPlants(Block $block, Block $newState) : void{
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