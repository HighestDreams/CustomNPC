<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC\Form\Settings;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Form\SettingsManager, NPC};
use pocketmine\{entity\Entity, Player, Server, utils\TextFormat as COLOR, entity\Skin};

class SizeAndEmotes
{

    public $emotes = ['Choose an emote'];

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send (Player $player, CustomNPC $NPC)
    {
        # Emotes
        foreach (NPC::$emotes as $emote => $ID) {
            if (!in_array($emote, NPC::get($NPC, 'Settings'))) {
                $this->emotes[] = $emote;
            } else {
                $this->emotes[0] = $emote;
                $this->emotes[] = 'Remove emote';
            }
        }

        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) {
                (new SettingsManager())->send($player, $NPC);
                return;
            }
            # Size
            if ($data[1] !== "" and $data[1] != $NPC->getDataPropertyManager()->getFloat(Entity::DATA_SCALE)) {
                $NPC->getDataPropertyManager()->setFloat(Entity::DATA_SCALE, (float)$data[1]);
                $NPC->sendData($NPC->getViewers());
            }
            # Emotes
            if ($this->emotes[$data[3]] !== $this->emotes[0]) {
                if ($this->emotes[$data[3]] !== 'Remove emote') {
                    $this->removeEmoteTag($NPC);
                    NPC::add($NPC, $this->emotes[$data[3]], 'Settings');
                } else {
                    $this->removeEmoteTag($NPC);
                }
            }
        });
        $form->setTitle("§l§3Size & Emotes");
        $form->addLabel("§3+ §6Write a new size for this NPC (You can also use float numbers, Write 0 to disappear NPC's skin).");
        $form->addInput('New Size :', 'Type new npc size here...', (string)$NPC->getScale());
        $form->addLabel("§3+ §6Choose an emote for this NPC to do it (If you want to remove NPC's cape, select \"Remove cape\").");
        $form->addDropdown('Emotes :', preg_replace('/Remove emote/i', COLOR::RED . 'Remove emote', $this->emotes));
        $form->sendToPlayer($player);
    }

    /**
     * @param CustomNPC $NPC
     * @return void
     */
    public function removeEmoteTag (CustomNPC $NPC) {
        foreach (NPC::get($NPC, 'Settings') as $tag) {
            if (isset(NPC::$emotes[$tag])) {
                NPC::remove($NPC, $tag, 'Settings');
            }
        }
    }
}
