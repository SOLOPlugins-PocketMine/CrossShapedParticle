<?php

namespace solo\crossshapedparticle;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\level\particle\CriticalParticle;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function handlePlayerInteract(PlayerInteractEvent $event){
		if(true or $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			$entity = new class($event->getPlayer()->getLevel(), Entity::createBaseNBT($event->getPlayer()->add(0, $event->getPlayer()->getEyeHeight(), 0), null, $event->getPlayer()->getYaw(), $event->getPlayer()->getPitch())) extends Entity{

				public function attack(EntityDamageEvent $event){

				}

				public function onUpdate(int $currentTick) : bool{
					if($this->closed){
						return false;
					}

					$tickDiff = $currentTick - $this->lastUpdate;
					if($tickDiff <= 0){
						return true;
					}

					$this->lastUpdate = $currentTick;

					$this->ticksLived += $tickDiff;

					// 객체의 속도를 조절합니다.
					$speed = $this->ticksLived * 0.6;

					// 처음에 스폰될 때 플레이어와의 거리를 설정합니다.
					$spawnOffset = 5;

					// 파티클의 갯수를 설정합니다. 실제 스폰되는 파티클의 갯수는 약 $amount * 4입니다.
					$amount = 15;

					// 파티클이 퍼지는 정도를 설정합니다.
					$spread = 8;

					// 객체의 수명을 설정합니다.
					$maxAge = 50;

					$y = -sin(deg2rad($this->pitch));
					$xz = cos(deg2rad($this->pitch));
					for($i = $amount; $i >= -$amount; --$i){
						$x = -$xz * sin(deg2rad($this->yaw + $i * $spread));
					    $z = $xz * cos(deg2rad($this->yaw + $i * $spread));
					    $vector = new Vector3($x, $y, $z);
					    $this->level->addParticle(new CriticalParticle($vector->multiply($speed + $spawnOffset)->add($this)));
					}

					$x = -$xz * sin(deg2rad($this->yaw));
					$z = $xz * cos(deg2rad($this->yaw));
					for($i = $amount; $i >= -$amount; --$i){
						$y = -sin(deg2rad($this->pitch + $i * $spread));
					    $vector = new Vector3($x, $y, $z);
						$this->level->addParticle(new CriticalParticle($vector->multiply($speed + $spawnOffset)->add($this)));
					}

					if($this->ticksLived > $maxAge){
						$this->close();
						return false;
					}
					return true;
				}
			};
		}
	}
}