<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC;

use HighestDreams\CustomNPC\Entity\CustomNPC;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class EmoteTimer extends Task {

    public $npc;
    public $session;

    public function __construct(NPC $npc)
    {
        $this->npc = $npc;
        $this->session = new Session(NPC::getInstance());
    }

    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getLevels() as $level) {
            foreach ($level->getEntities() as $npc) {
                if ($npc instanceof CustomNPC) {
                    foreach ($this->session::getSettings($npc) as $setting) {
                        foreach (NPC::$emotes as $emote => $id) {
                            if ($setting === $emote) {
                                Server::getInstance()->broadcastPacket($npc->getViewers(), EmotePacket::create($npc->getId(), $id, 1 << 0));
                            }
                        }
                    }
                }
            }
        }
    }
}