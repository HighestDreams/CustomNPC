<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC\Form\Settings;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Form\SettingsManager};
use pocketmine\Player;

class NameTag
{

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send (Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) {
                (new SettingsManager())->send($player, $NPC);
                return;
            }
            if ($data[1] !== $NPC->getNameTag()) {
                if (empty($data[1])) {
                    $NPC->setNameTagAlwaysVisible(false);
                } else {
                    $NPC->setNameTagAlwaysVisible(true);
                }
                $NPC->setNameTag(str_replace('{line}', "\n", $data[1]));
            }
        });
        $form->setTitle("§l§3NameTag");
        $form->addLabel("§3+ §6Write a new name for npc in the below input or leave it empty to hide npc name (Use tag {line} to go in next line).");
        $form->addInput("§lNew Name :", 'Type new name here...', $NPC->getNameTag());
        $form->sendToPlayer($player);
    }
}
