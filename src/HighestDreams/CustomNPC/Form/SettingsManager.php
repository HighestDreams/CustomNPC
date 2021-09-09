<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC\Form;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, lang, NPC, Session};
use pocketmine\entity\Skin;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as COLOR;

class SettingsManager
{

    public $npc;
    public $players = ['Choose a player'];
    public $session;
    public $lang;

    public function __construct(NPC $npc)
    {
        $this->npc = $npc;
        $this->session = new Session(NPC::getInstance());
        $this->lang = new lang(NPC::getInstance());
    }

    /**
     * @param Player $player
     * @param CustomNPC $npc
     */
    public function send(Player $player, CustomNPC $npc)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($npc) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    $this->General($player, $npc);
                    break;
                case 1:
                    $this->Other($player, $npc);
                    break;
                case 2:
                    (new CustomizeMain(NPC::getInstance()))->send($player, $npc);
                    break;
            }
        });
        $form->setTitle("§3Settings Manager");
        $form->setContent("§3+ §6NPC ID §b: §a" . $npc->getId());
        $form->addButton('§8General');
        $form->addButton('§8Other');
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $npc
     */
    public function General(Player $player, CustomNPC $npc)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            $this->players[] = $p->getName();
        }

        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($npc) {
            if (is_null($data)) {
                $this->send($player, $npc);
                return;
            }

            if (!empty($data[1]) and $data[1] !== $npc->getName()) {
                $npc->setNameTag(str_replace('{line}', "\n", $data[1]));
            }
            if (!empty($data[3]) and $data[3] != $npc->getScale()) {
                $npc->setScale((float)$data[3]);
            }
            if ($this->players[$data[5]] !== $this->players[0]) {
                if (!is_null($target = Server::getInstance()->getPlayer($this->players[$data[5]]))) {
                    $npc->setSkin(new Skin($target->getSkin()->getSkinId(), $target->getSkin()->getSkinData(), $target->getSkin()->getCapeData(), $target->getSkin()->getGeometryName(), $target->getSkin()->getGeometryData()));
                    $npc->sendSkin();
                } else {
                    $player->sendMessage(NPC::PREFIX . COLOR::RED . "Player §b{$this->players[$data[5]]} §cis not online!");
                }
            }
            $player->sendMessage(NPC::PREFIX . COLOR::GREEN . $this->lang::get($this->lang::CHANGES));
        });
        $form->setTitle("§3General Settings");
        $form->addLabel('§3+ §6Change the name of NPC');
        $form->addInput('§fName : ', 'Type a new name for NPC', $npc->getName());
        $form->addLabel('§3+ §6Change the size of NPC');
        $form->addInput('§fSize : ', 'Type a new size for NPC', (string)$npc->getScale());
        $form->addLabel("§3+ §6" . $this->lang::get($this->lang::SKIN));
        $form->addDropdown('Skin', $this->players);
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $npc
     */
    public function Other(Player $player, CustomNPC $npc)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            $this->players[] = $p->getName();
        }
        /**
         * @return float|void
         */
        $getCooldown = function () use ($npc) {
            $total = $this->session::getSettings($npc);
            foreach ($total as $setting) {
                if ($setting !== 'rotation') {
                    return (float)$setting;
                }
            }
        };
        $getRotation = $this->session::isSettingExists($npc, 'rotation');

        $emotesList = ['Choose an emote'];
        foreach (NPC::$emotes as $emote => $id) {
            $emotesList[] = $emote;
        }

        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($npc, $getRotation, $getCooldown, $emotesList) {
            if (is_null($data)) {
                $this->send($player, $npc);
                return;
            }
            if ($data[1] === true and !$this->session::isSettingExists($npc, 'rotation')) {
                $this->session::addSetting($npc, 'rotation');
            } elseif ($data[1] === false and $this->session::isSettingExists($npc, 'rotation') === true) {
                $this->session::removeSetting($npc, 'rotation');
            }

            if (is_numeric($data[3])) {
                foreach ($this->session::getSettings($npc) as $setting) {
                    if (preg_match('/[0-9]/i', $setting)) {
                        $this->session::removeSetting($npc, $setting);
                    }
                }
                $this->session::addSetting($npc, $data[3]);
            }

            if (!$this->session::isSettingExists($npc, $emotesList[$data[5]])) {
                foreach ($this->session::getSettings($npc) as $setting) {
                    if (in_array($setting, $emotesList)) {
                        $this->session::removeSetting($npc, $setting);
                    }
                }
                $this->session::addSetting($npc, $emotesList[$data[5]]);
            }
        });
        $form->setTitle("§3Other Settings");
        $form->addLabel('§3+ §6' . $this->lang::get($this->lang::ROTATION));
        $form->addToggle('Rotation', $getRotation);
        $form->addLabel('§3+ §6' . $this->lang::get($this->lang::COOLDOWN));
        $form->addInput('§fCooldown : ', 'For example: 0.5', !is_null($getCooldown()) ? (string)$getCooldown() : '0');
        $form->addLabel('§3+ §6' . $this->lang::get($this->lang::EMOTE));
        $form->addDropdown('Emotes :', $emotesList);
        $form->sendToPlayer($player);
    }
}