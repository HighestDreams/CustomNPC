<?php
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
declare(strict_types=1);

namespace HighestDreams\CustomNPC;

use HighestDreams\CustomNPC\Entity\CustomNPC;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as COLOR;

class NPC extends PluginBase
{

    public const PREFIX = COLOR::BOLD . COLOR::WHITE . "Custom" . COLOR::RED . "NPC " . COLOR::RESET . COLOR::DARK_RED . "> ";
    public static $editor = [];
    public static $teleport = [];
    public static $timer = [];
    public static $Instance;
    public static $session;
    public static $settings;

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$Instance;
    }

    public function onEnable()
    {
        $this->saveResource('steve.png');
        $this->saveResource('Settings.yml');
        self::$Instance = $this;
        self::$settings = new Config($this->getDataFolder() . 'Settings.yml', Config::YAML);
        self::$session = new Session($this);
        Entity::registerEntity(CustomNPC::class, true);
        $this->getServer()->getPluginManager()->registerEvents(new EventsHandler($this), $this);
    }

    /**
     * @param CommandSender $player
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args): bool
    {
        if (strtolower($cmd->getName()) === 'rca') {
            if (is_null($this->getServer()->getPluginManager()->getPlugin('Slapper'))) {
                if (count($args) >= 2) {
                    $target = $this->getServer()->getPlayer(array_shift($args));
                    if ($target instanceof Player) {
                        $this->getServer()->dispatchCommand($target, trim(implode(" ", $args)));
                    } else {
                        $player->sendMessage(self::PREFIX . "Player not found.");
                    }
                } else {
                    $player->sendMessage(self::PREFIX . "Usage: /rca <player-name> <command>");
                }
                return true;
            }
        }
        if (!$player instanceof Player) return true;

        if (strtolower($cmd->getName()) === "npc") {
            if (isset($args[0])) {
                if ($args[0] === "edit") {
                    if (!self::$session::isEditor($player)) {
                        self::$session::setEditorMode($player, true);
                        $player->sendMessage(self::PREFIX . COLOR::GREEN . 'NPC editing mode enabled for you! Tap any NPC you want to edit, use (/npc edit) command to disable NPC edit mode');
                    } else {
                        self::$session::setEditorMode($player, false);
                        $player->sendMessage(self::PREFIX . COLOR::GREEN . 'NPC editing mode disabled your you.');
                    }
                } else {
                    $player->sendMessage(self::PREFIX . COLOR::RED . "For enable NPC editing mode use : /npc edit");
                }
                return true;
            }
            $this->spawn($player);
            $player->sendMessage(self::PREFIX . COLOR::GREEN . "NPC created successfully! For customizing you need first enable npc editor mode! for this, use command /npc edit");
        }
        return true;
    }

    /**
     * @param Player $player
     */
    public function spawn(Player $player)
    {
        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $player->getX()),
                new DoubleTag("", $player->getY()),
                new DoubleTag("", $player->getZ())
            ]),
            new ListTag("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", 0),
                new DoubleTag("", 0)
            ]),
            new ListTag("Rotation", [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ])
        ]);
        $nbt->setTag($player->namedtag->getTag("Skin"));
        $npc = new CustomNPC ($player->getLevel(), $nbt);
        $npc->yaw = $player->getYaw();
        $npc->pitch = $player->getPitch();
        $npc->updateSkin();
        $npc->setNameTag('Custom NPC');
        $npc->spawnToAll();
    }
}
