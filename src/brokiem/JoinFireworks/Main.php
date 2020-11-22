<?php

namespace brokiem\JoinFireworks;

use BlockHorizons\Fireworks\item\Fireworks;
use BlockHorizons\Fireworks\entity\FireworksRocket;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

	public function onEnable() {
		ItemFactory::registerItem(new Fireworks());
		Item::initCreativeItems();
		if(!Entity::registerEntity(FireworksRocket::class, false, ["FireworksRocket"])) {
			$this->getLogger()->error("Failed to register FireworksRocket entity with savename 'FireworksRocket'");
		}
  	$this->getServer()->getPluginManager()->registerEvents($this, $this);
  	}

	public function getFireworksColor() {
		$color_array = [
			Fireworks::COLOR_BLACK,
			Fireworks::COLOR_RED,
			Fireworks::COLOR_DARK_GREEN,
			Fireworks::COLOR_BROWN,
			Fireworks::COLOR_BLUE,
			Fireworks::COLOR_DARK_PURPLE,
			Fireworks::COLOR_DARK_AQUA,
			Fireworks::COLOR_GRAY,
			Fireworks::COLOR_DARK_GRAY,
			Fireworks::COLOR_PINK,
			Fireworks::COLOR_GREEN,
			Fireworks::COLOR_YELLOW,
			Fireworks::COLOR_LIGHT_AQUA,
			Fireworks::COLOR_DARK_PINK,
			Fireworks::COLOR_GOLD,
			Fireworks::COLOR_WHITE
		];

		return $color_array[array_rand($color_array)];
	}

  	public function onJoin(PlayerJoinEvent $event) {
  		$fw = ItemFactory::get(Item::FIREWORKS);
   		$fw->addExplosion(mt_rand(0, 4), $this->getFireworksColor(), "", true, true);
    		$fw->setFlightDuration(mt_rand(1, 2));
		$level = $this->getServer()->getDefaultLevel();
   		$vector3 = $level->getSpawnLocation()->add(0, 1, 0);
		$nbt = FireworksRocket::createBaseNBT($vector3, new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
   		$entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
   		if ($entity instanceof FireworksRocket) {
			if($event->getPlayer()->hasPermission("brokiem.join.firework")) {
   				$entity->spawnTo($event->getPlayer());
			}
    		}
	}
}
