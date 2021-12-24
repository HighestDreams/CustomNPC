<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC\Form\Settings;

use HighestDreams\CustomNPC\{Entity\CustomNPC, Form\formapi\FormAPI, Form\SettingsManager, NPC};
use pocketmine\{Player, Server, utils\TextFormat as COLOR, entity\Skin};

class SkinAndCape
{

    public $players = ['Choose a player'];
    public $capes = ['Choose a cape'];

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     */
    public function send (Player $player, CustomNPC $NPC)
    {
        # Players
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->players[] = $player->getName();
        }
        # Capes
        foreach (array_slice(scandir(NPC::getInstance()->getDataFolder() . 'Capes'), 2) as $cape) {
            if (!in_array($cape, NPC::get($NPC, 'Settings'))) {
                $this->capes[] = $cape;
            } else {
                $this->capes[0] = $cape;
                $this->capes[] = 'Remove cape';
            }
        }

        $form = (new FormAPI())->createCustomForm(function (Player $player, $data = null) use ($NPC) {
            if (is_null($data)) {
                (new SettingsManager())->send($player, $NPC);
                return;
            }
            # Skin
            if ($this->players[$data[1]] !== $this->players[0]) {
                if (!is_null($target = Server::getInstance()->getPlayer($this->players[$data[1]]))) {
                    $NPC->setSkin(new Skin($target->getSkin()->getSkinId(), $target->getSkin()->getSkinData(), $target->getSkin()->getCapeData(), $target->getSkin()->getGeometryName(), $target->getSkin()->getGeometryData()));
                    $NPC->sendSkin();
                } else {
                    $player->sendMessage(NPC::PREFIX . COLOR::RED . "Player §b{$this->players[$data[1]]} §cis not online!");
                }
            }
            # Cape
            if ($this->capes[$data[3]] !== $this->capes[0]) {
                if ($this->capes[$data[3]] !== 'Remove cape') {
                    $this->removeCapeTag($NPC);
                    NPC::add($NPC, $this->capes[$data[3]], 'Settings');

                    $NPC->setSkin($this->getSkin($NPC, $this->capes[$data[3]]));
                } else {
                    $this->removeCapeTag($NPC);
                    $NPC->setSkin($this->getSkin($NPC));
                }
                $NPC->sendSkin();
            }
        });
        $form->setTitle("§l§3Skin & Cape");
        $form->addLabel("§3+ §6Select an online player from below to change NPC skin to their skin.");
        $form->addDropdown('Skins :', $this->players);
        $form->addLabel("§3+ §6Select a new cape for this NPC (If you want to remove NPC's cape, select \"Remove cape\".");
        $form->addDropdown('Capes :', preg_replace('/Remove cape/i', COLOR::RED . 'Remove cape', preg_replace('/\.png/i', '', $this->capes)));
        $form->sendToPlayer($player);
    }

    /**
     * @param string $cape
     * @return string
     */
    public function createCapeFromPNG (string $cape): string
    {
        $path = NPC::getInstance()->getDataFolder() . "Capes\\$cape";
        $img = @imagecreatefrompng($path);
        $skinBytes = "";
        $s = (int)@getimagesize($path)[1];
        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < 64; $x++) {
                $colorAt = @imagecolorat($img, $x, $y);
                $a = ((~($colorAt >> 24)) << 1) & 0xff;
                $r = ($colorAt >> 16) & 0xff;
                $g = ($colorAt >> 8) & 0xff;
                $b = $colorAt & 0xff;
                $skinBytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);

        return $skinBytes;
    }

    /**
     * @param CustomNPC $NPC
     * @return void
     */
    public function removeCapeTag (CustomNPC $NPC) {
        foreach (preg_grep('/\.png/i', NPC::get($NPC, 'Settings')) as $cape) {
            NPC::remove($NPC, $cape, 'Settings');
        }
    }

    /**
     * @param CustomNPC $NPC
     * @param string|null $cape
     * @return Skin
     */
    public function getSkin (CustomNPC $NPC, string $cape = null): Skin {
        return new Skin(($Skin = $NPC->getSkin())->getSkinId(), $Skin->getSkinData(), is_null($cape) ? '' : $this->createCapeFromPNG($cape), $Skin->getGeometryName(), $Skin->getGeometryData());
    }
}
