<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC\Form\Settings;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Form\SettingsManager, NPC};
use pocketmine\{Player};

class Cooldown
{

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send (Player $player, CustomNPC $NPC)
    {
        $getCooldown = function () use ($NPC) {
            foreach (NPC::get($NPC, 'Settings') as $setting) {
                if (is_numeric($setting)) {
                    return (float)$setting;
                }
            }
        };
        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($getCooldown, $NPC) {
            if (is_null($data)) {
                (new SettingsManager())->send($player, $NPC);
                return;
            }

            if (is_numeric($data[1])) {
                foreach (NPC::get($NPC, 'Settings') as $setting) {
                    if (is_numeric($setting)) {
                        NPC::remove($NPC, $setting, 'Settings');
                    }
                }
                NPC::add($NPC, $data[1], 'Settings');
            }
        });
        $form->setTitle("§l§3Cooldown");
        $form->addLabel("§3+ §6Write a number for Cooldown (Per seconds), (To disable cooldown for this NPC write 0).");
        $form->addInput('§l§fCoolDown :', 'Write a new cooldown number...', !is_null($getCooldown()) ? (string)$getCooldown() : '0');
        $form->sendToPlayer($player);
    }
}
