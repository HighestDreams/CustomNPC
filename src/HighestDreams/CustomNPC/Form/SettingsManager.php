<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC\Form;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, lang, Language, NPC, Session};
use pocketmine\entity\Skin;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as COLOR;

class SettingsManager
{
    public $players = ['Choose a player'];

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send(Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    $this->General($player, $NPC);
                    break;
                case 1:
                    $this->Other($player, $NPC);
                    break;
                case 2:
                    (new CustomizeMain())->send($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§3Settings Manager");
        $form->setContent("§3+ §6NPC ID §b: §a" . $NPC->getId());
        $form->addButton('§8General');
        $form->addButton('§8Other');
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function General(Player $player, CustomNPC $NPC)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            $this->players[] = $p->getName();
        }

        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) {
                $this->send($player, $NPC);
                return;
            }

            if (!empty($data[1]) and $data[1] !== $NPC->getName()) {
                $NPC->setNameTag(str_replace('{line}', "\n", $data[1]));
            }
            if (!empty($data[3]) and $data[3] != $NPC->getScale()) {
                $NPC->setScale((float)$data[3]);
            }
            if ($this->players[$data[5]] !== $this->players[0]) {
                if (!is_null($target = Server::getInstance()->getPlayer($this->players[$data[5]]))) {
                    $NPC->setSkin(new Skin($target->getSkin()->getSkinId(), $target->getSkin()->getSkinData(), $target->getSkin()->getCapeData(), $target->getSkin()->getGeometryName(), $target->getSkin()->getGeometryData()));
                    $NPC->sendSkin();
                } else {
                    // Mybe target leave the game, so that's wht its here :p
                    $player->sendMessage(NPC::PREFIX . COLOR::RED . "Player §b{$this->players[$data[5]]} §cis not online!");
                }
            }
            $player->sendMessage(NPC::PREFIX . COLOR::GREEN . Language::translated(Language::CHANGES));
        });
        $form->setTitle("§3General Settings");
        $form->addLabel('§3+ §6Change the name of NPC');
        $form->addInput('§fName : ', 'Type a new name for NPC', $NPC->getName());
        $form->addLabel('§3+ §6Change the size of NPC');
        $form->addInput('§fSize : ', 'Type a new size for NPC', (string)$NPC->getScale());
        $form->addLabel("§3+ §6" . Language::translated(Language::SKIN));
        $form->addDropdown('Skin', $this->players);
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function Other(Player $player, CustomNPC $NPC)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            $this->players[] = $p->getName();
        }

        $getSettings = NPC::get($NPC, 'Settings');
        
        /**
         * @return float|void
         */
        $getCooldown = function () use ($NPC, $getSettings) {
            foreach ($getSettings as $setting) {
                if (is_numeric($setting)) {
                    return (float)$setting;
                }
            }
        };
        $getRotation = NPC::isset($NPC, 'rotation', 'Settings');

        $emotesList = ['Choose an emote'];
        foreach (NPC::$emotes as $emote => $id) {
            $emotesList[] = $emote;
        }

        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC, $getRotation, $getCooldown, $emotesList, $getSettings) {
            if (is_null($data)) {
                $this->send($player, $NPC);
                return;
            }
            if ($data[1] === true and $getRotation === false) {
                NPC::add($NPC, 'rotation', 'Settings');
            } elseif ($data[1] === false and $getRotation === true) {
                NPC::remove($NPC, 'rotation', 'Settings');
            }

            if (is_numeric($data[3])) {
                foreach ($getSettings as $setting) {
                    if (is_numeric($setting)) {
                        NPC::remove($NPC, $setting, 'Settings');
                    }
                }
                NPC::add($NPC, $data[3], 'Settings');
            }

            if (!NPC::isset($NPC, $emotesList[$data[5]], 'Settings')) {
                foreach ($getSettings as $setting) {
                    if (in_array($setting, $emotesList)) {
                        NPC::remove($NPC, $setting, 'Settings');
                    }
                }
                NPC::add($NPC, $emotesList[$data[5]], 'Settings');
            }
        });
        $form->setTitle("§3Other Settings");
        $form->addLabel('§3+ §6' . Language::translated(Language::ROTATION));
        $form->addToggle('Rotation', $getRotation);
        $form->addLabel('§3+ §6' . Language::translated(Language::COOLDOWN));
        $form->addInput('§fCooldown : ', 'For example: 0.5', !is_null($getCooldown()) ? (string)$getCooldown() : '0');
        $form->addLabel('§3+ §6' . Language::translated(Language::EMOTE));
        $form->addDropdown('Emotes :', $emotesList);
        $form->sendToPlayer($player);
    }
}
