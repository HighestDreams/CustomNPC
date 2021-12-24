<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC\Form\Settings;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Form\SettingsManager, NPC};
use pocketmine\{Player, Server, utils\TextFormat as COLOR, entity\Skin};

class Rotation
{

    public $distances = ['5', '6', '7', '8', '9', '11', '13', '15', '17', '20', '22', '25'];

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send (Player $player, CustomNPC $NPC)
    {
        $rotation = !empty(preg_grep('/rotation/i', NPC::get($NPC, 'Settings')));
        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC, $rotation) {
            if (is_null($data)) {
                (new SettingsManager())->send($player, $NPC);
                return;
            }
            # Rotation
            if ($data[1] === false and $rotation === true) {
                NPC::remove($NPC, 'rotation', 'Settings');
            } elseif ($data[1] === true and $rotation === false) {
                NPC::add($NPC, 'rotation', 'Settings');
            }
            # Maximum distance
            if (($distance = (int)$this->distances[$data[3]]) !== NPC::$settings->get('rotation-distance')) {
                NPC::$settings->set('rotation-distance', $distance);
                NPC::$settings->save();
            }
        });
        $form->setTitle("§l§3Rotation");
        $form->addLabel("§3+ §6Enable or Disable rotation for this NPC.");
        $form->addToggle('§l§fRotation', $rotation);
        $form->addLabel('§3+ §6Maximum distance (Per block) that npcs looking at nearest player (This is for all NPCs!)');
        $form->addStepSlider('Maximum distance', $this->distances, array_search(is_bool($distance = NPC::$settings->get('rotation-distance')) ? 5 : $distance, $this->distances));
        $form->sendToPlayer($player);
    }

    /**
     * @param CustomNPC $NPC
     * @return void
     */
    public function removeRotationTag (CustomNPC $NPC) {
        foreach (preg_grep('/rotation_\d+/i', NPC::get($NPC, 'Settings')) as $rotation) {
            NPC::remove($NPC, $rotation, 'Settings');
        }
    }
}
