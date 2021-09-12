<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC\Form;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Language, NPC};
use pocketmine\Player;

class CommandsManager
{
    public function send(Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    $this->list($player, $NPC);
                    break;
                case 1:
                    $this->add($player, $NPC);
                    break;
                case 2:
                    (new CustomizeMain())->send($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§3Commands Manager");
        $form->setContent("§3+ §6NPC ID §b: §a" . $NPC->getId());
        $form->addButton('§8Commands list');
        $form->addButton('§8Add command');
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function list(Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) {
                $this->send($player, $NPC);
                return;
            }

            foreach (($commands = NPC::get($NPC, 'Commands')) as $result => $command) {
                if ($result === $data) {
                    $this->manage($player, $NPC, $command);
                }
            }
            if ($data === count($commands)) {
                $this->send($player, $NPC);
            }
        });
        $form->setTitle("§3Commands List");
        $form->setContent((($total = count(NPC::get($NPC, 'Commands'))) <= 0) ? "§3+ §6There is no commands for this npc!" : "§3+ §6Total commands §b: §a" . $total . "\n§3+ §6Select a command to manage.");
        $num = 0;
        foreach (NPC::get($NPC, 'Commands') as $command) {
            $num++;
            $form->addButton("§9$num. §8$command");
        }
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     * @param string $command
     */
    public function manage(Player $player, CustomNPC $NPC, string $command)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC, $command) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    NPC::remove($NPC, $command);
                    $this->list($player, $NPC);
                    break;
                case 1:
                    $this->list($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§3Commands List");
        $form->setContent("§3+ §6NPC ID §b: §a" . $NPC->getId() . "\n§3+ §6NPC command §b: §a" . $command);
        $form->addButton('§8Delete Command');
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function add(Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) {
                $this->send($player, $NPC);
                return;
            }

            if (!empty($data[1]) and !NPC::isset($NPC, $data[1])) {
                NPC::add($NPC, $data[1]);
                $this->list($player, $NPC);
            }
        });
        $form->setTitle('§3Add command');
        $form->addLabel('§3+ §6' . Language::translated(Language::COMMAND_BELOW));
        $form->addInput('', 'Type your command here');
        $form->sendToPlayer($player);
    }
}