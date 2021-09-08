<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC\Form;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, NPC, Session};
use pocketmine\Player;

class CommandsManager
{

    public $npc;
    public $session;

    public function __construct(NPC $npc)
    {
        $this->npc = $npc;
        $this->session = new Session(NPC::getInstance());
    }

    public function send(Player $player, CustomNPC $npc)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($npc) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    $this->list($player, $npc);
                    break;
                case 1:
                    $this->add($player, $npc);
                    break;
                case 2:
                    (new CustomizeMain(NPC::getInstance()))->send($player, $npc);
                    break;
            }
        });
        $form->setTitle("§3Commands Manager");
        $form->setContent("§3+ §6NPC ID §b: §a" . $npc->getId());
        $form->addButton('§8Commands list');
        $form->addButton('§8Add command');
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $npc
     */
    public function list(Player $player, CustomNPC $npc)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($npc) {
            if (is_null($data)) {
                $this->send($player, $npc);
                return;
            }

            foreach (($commands = $this->session::getCommands($npc)) as $result => $command) {
                if ($result === $data) {
                    $this->manage($player, $npc, $command);
                }
            }
            if ($data === count($commands)) {
                $this->send($player, $npc);
            }
        });
        $form->setTitle("§3Commands List");
        $form->setContent((($total = count($this->session::getCommands($npc))) <= 0) ? "§3+ §6There is no commands for this npc!" : "§3+ §6Total commands §b: §a" . $total . "\n§3+ §6Select a command to manage.");
        $num = 0;
        foreach ($this->session::getCommands($npc) as $command) {
            $num++;
            $form->addButton("§9$num. §8$command");
        }
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $npc
     * @param string $command
     */
    public function manage(Player $player, CustomNPC $npc, string $command)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($npc, $command) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    $this->session::removeCommand($npc, $command);
                    $this->list($player, $npc);
                    break;
                case 1:
                    $this->list($player, $npc);
                    break;
            }
        });
        $form->setTitle("§3Commands List");
        $form->setContent("§3+ §6NPC ID §b: §a" . $npc->getId() . "\n§3+ §6NPC command §b: §a" . $command);
        $form->addButton('§8Delete Command');
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $npc
     */
    public function add(Player $player, CustomNPC $npc)
    {
        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($npc) {
            if (is_null($data)) {
                $this->send($player, $npc);
                return;
            }

            if (!empty($data[1]) and !$this->session::isCommandExists($npc, $data[1])) {
                $this->session::addCommand($npc, $data[1]);
                $this->list($player, $npc);
            }
        });
        $form->setTitle('§3Add command');
        $form->addLabel('§3+ §6Write your command in the input below.');
        $form->addInput('', 'Type your command here');
        $form->sendToPlayer($player);
    }
}