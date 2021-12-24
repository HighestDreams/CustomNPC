<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC\Form;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Language, NPC};
use pocketmine\Player;
use pocketmine\utils\TextFormat as COLOR;

class CustomizeMain
{

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send (Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    (new SettingsManager())->send($player, $NPC);
                    break;
                case 1:
                    $this->CommandsInteractionsSection($player, $NPC);
                    break;
                case 2:
                    NPC::$teleport[$player->getName()] = $NPC->getId();
                    $player->sendMessage(NPC::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_TELEPORT_TUTORIAL));
                    break;
                case 3:
                    $this->delete($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§4NPC §3Customize §5Menu");
        $form->setContent("§3+ §6ID §b: §a" . $NPC->getId() . "\n§3+ §6X§5,§6Y§5,§6Z§b: §a" . round($NPC->getX(), 1) . "§5,§a" . round($NPC->getY(), 1) . "§5,§a" . round($NPC->getZ(), 1));
        $form->addButton('§8§lSettings');
        $form->addButton('§8§lCommands/Interactions');
        $form->addButton('§8§lTeleport NPC');
        $form->addButton('§4Delete NPC');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function CommandsInteractionsSection (Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) return;

            switch ($data) {
                case 0:
                    (new CommandsManager())->send($player, $NPC);
                    break;
                case 1:
                    (new InteractionsManager())->send($player, $NPC);
                    break;
                case 2:
                    $this->send($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§5Commands§3/§5Interactions");
        $form->setContent("§3+ §6Commands :\n§3+ §6Interactions :");
        $form->addButton('§8§lManage commands');
        $form->addButton('§8§lManage interactions');
        $form->addButton('§l§4Back');
        $form->sendToPlayer($player);
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function delete (Player $player, CustomNPC $NPC)
    {
        $form = (new FormAPI())->createModalForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) return;

            switch ($data) {
                case true:
                    $NPC->flagForDespawn();
                    $player->sendMessage(NPC::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_DELETATION));
                    break;
                case false:
                    $this->send($player, $NPC);
                    break;
            }
        });
        $form->setTitle("§4DELETE NPC");
        $form->setContent("§3+ §6NPC ID §b: §a" . $NPC->getId() . " §6" . Language::translated(Language::NPC_DELETE_SURE));
        $form->setButton1('§l§aYes, Delete NPC');
        $form->setButton2('§l§cNo, Do not Delete NPC');
        $form->sendToPlayer($player);
    }
}