<?php

declare(strict_types=1);

/*
 *       _       _       ______ _                             _
 *      | |     (_)     |  ____(_)                           | |
 *      | | ___  _ _ __ | |__   _ _ __ _____      _____  _ __| | _____
 *  _   | |/ _ \| | '_ \|  __| | | '__/ _ \ \ /\ / / _ \| '__| |/ / __|
 * | |__| | (_) | | | | | |    | | | |  __/\ V  V / (_) | |  |   <\__ \
 *  \____/ \___/|_|_| |_|_|    |_|_|  \___| \_/\_/ \___/|_|  |_|\_\___/
 *
 * Copyright (C) 2020 brokiem
 *
 * This software is distributed under "GNU General Public License v3.0".
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 *
 */

namespace brokiem\JoinFireworks;

use BlockHorizons\Fireworks\item\Fireworks;
use BlockHorizons\Fireworks\entity\FireworksRocket;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function getFireworksColor(): string
    {
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

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();

        if ($player->hasPermission("join.fireworks.use")) {
            $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($player): void {
                $fw = ItemFactory::get(Item::FIREWORKS);
                if ($fw instanceof Fireworks) {
                    $fw->addExplosion(mt_rand(0, 4), $this->getFireworksColor(), "", true, true);
                    $fw->setFlightDuration($this->getConfig()->get("flight-duration", 2));

                    $level = $player->getLevelNonNull();
                    $vector3 = $level->getSpawnLocation()->add(0.5, 1, 0.5);
                    $nbt = FireworksRocket::createBaseNBT($vector3, new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
                    $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);

                    if ($entity instanceof FireworksRocket) {
                        $entity->spawnToAll();

                    }
                }
            }), $this->getConfig()->get("spawn-delay", 2) * 20);
        }
    }
}
