<?php

declare(strict_types=1);

namespace brokiem\JoinFireworks;

use BlockHorizons\Fireworks\entity\FireworksRocket;
use BlockHorizons\Fireworks\item\Fireworks;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase implements Listener {

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function getFireworksColor(): string {
        $colors = [
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

        return $colors[array_rand($colors)];
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        if ($player->hasPermission("joinfireworks.use")) {
            $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player): void {
                $fw = ItemFactory::getInstance()->get(ItemIds::FIREWORKS);
                if ($fw instanceof Fireworks) {
                    $fw->addExplosion(random_int(0, 4), $this->getFireworksColor(), "", true, true);
                    $fw->setFlightDuration($this->getConfig()->get("flight-duration", 2));

                    $entity = new FireworksRocket(Location::fromObject($player->getPosition(), $player->getWorld(), lcg_value() * 360, 90), $fw);
                    $entity->spawnToAll();
                }
            }), $this->getConfig()->get("spawn-delay", 2) * 20);
        }
    }
}