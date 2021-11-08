<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC;

use HighestDreams\CustomNPC\Entity\CustomNPC;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ColorfulTimer extends Task {

    public $colors = ['§1', '§2', '§3', '§4', '§5', '§6', '§9', '§a', '§e', '§d', '§f', '§c', '§b'];
    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getLevels() as $level) {
            foreach ($level->getEntities() as $NPC) {
                if ($NPC instanceof CustomNPC) {
                    foreach (['shuffle'] as $type) {
                        if (NPC::isset($NPC, $type, 'Settings')) {
                            $Name = preg_replace('/§./i', '', $NPC->getNameTag());
                            if (!empty($Name)) {
                                $newName = '';
                                for ($i = 0; $i <= strlen($Name) - 1; $i++) {
                                    $newName .= $this->colors[rand(0, count($this->colors) - 1)] . $Name[$i];
                                }
                                $NPC->setNameTag($newName);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param CustomNPC $NPC
     * @return string
     */
    public function getRgbName (CustomNPC $NPC): string
    {
        $color = "";
        $Name = preg_replace('/(§[^lromn])/i', '', $NPC->getNameTag());
        shuffle($this->colors);
        for ($numberPos = 0; $numberPos < strlen($Name) - 1; $numberPos++) {
            $color .= $Name[$numberPos] . $this->colors[$numberPos % count($this->colors)];
        }
        return $color . $Name[$numberPos];
    }
    /*
     public function getColorize (string $type, string $NameTag): string {
        switch ($type) {
            case 'Shuffle':
                $NameTag = preg_replace('/(§[^lr])/i', '', $NameTag);
                break;
            case 'Blink':
                $NameTag = preg_replace('/§./i', '', $NameTag);
                break;
        }
        return $NameTag;
    }
     */
}