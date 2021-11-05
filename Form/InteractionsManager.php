<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC\Form;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Language, NPC};
use pocketmine\Player;

class InteractionsManager
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
                    if (NPC::isset($NPC, 'pair_interactions_with_commands', 'Settings')) {
                        NPC::remove($NPC, 'pair_interactions_with_commands', 'Settings');
                        $player->sendMessage(NPC::PREFIX . "§aNPC §d{$NPC->getId()} §ahas §l§eUnPaired §r§awith commands successfully.");
                    } else {
                        NPC::add($NPC, 'pair_interactions_with_commands', 'Settings');
                        $player->sendMessage(NPC::PREFIX . "§aNPC §d{$NPC->getId()} §ahas §l§ePaired §r§awith commands successfully.");
                    }
                    break;
                case 3:
                    (new CustomizeMain())->send($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§3Interactions Manager");
        $form->setContent("§3+ §6NPC ID §b: §a" . $NPC->getId());
        $form->addButton('§8Interactions list');
        $form->addButton('§8Add Interaction');
        if (NPC::isset($NPC, 'pair_interactions_with_commands', 'Settings')) {
            $form->addButton("§8UnPair With Commands\n(Run interaction commands on interact)");
        } else {
            $form->addButton("§8Pair With Commands\n(Run commands on interact)");
        }
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function list(Player $player, CustomNPC $NPC)
    {
        $getInteractions = NPC::get($NPC, 'Interactions');
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC, $getInteractions) {
            if (is_null($data)) {
                $this->send($player, $NPC);
                return;
            }

            foreach (($getInteractions) as $result => $interaction) {
                if ($result === $data) {
                    $this->manage($player, $NPC, $interaction);
                }
            }
            if ($data === count($getInteractions)) {
                $this->send($player, $NPC);
            }
        });
        $form->setTitle("§3Interactions List");
        $form->setContent((($total = count($getInteractions)) <= 0) ? "§3+ §6There is no interactions for this npc!" : "§3+ §6Total interactions §b: §a" . $total . "\n§3+ §6Select an interaction to manage.");
        $num = 0;
        foreach ($getInteractions as $interaction) {
            $num++;
            $form->addButton("§9$num. §8$interaction");
        }
        $form->addButton('§cBack');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     * @param string $interaction
     */
    public function manage(Player $player, CustomNPC $NPC, string $interaction)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC, $interaction) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    NPC::remove($NPC, $interaction, 'Interactions');
                    $this->list($player, $NPC);
                    break;
                case 1:
                    $this->list($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§Interactions List");
        $form->setContent("§3+ §6NPC ID §b: §a" . $NPC->getId() . "\n§3+ §6NPC interaction §b: §a" . $interaction);
        $form->addButton('§8Delete Interaction');
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

            if (!empty($data[1]) and !NPC::isset($NPC, $data[1], 'Interactions')) {
                NPC::add($NPC, $data[1], 'Interactions');
                $this->list($player, $NPC);
            }
        });
        $form->setTitle('§3Add interaction');
        $form->addLabel('§3+ §6' . Language::translated(Language::COMMAND_BELOW));
        $form->addInput('', 'Type your new interaction here');
        $form->sendToPlayer($player);
    }
}
