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
    public static $lang;
    public static $emotes = [
        "wave" => "4c8ae710-df2e-47cd-814d-cc7bf21a3d67",
        "simple_clap" => "9a469a61-c83b-4ba9-b507-bdbe64430582",
        "over_there" => "ce5c0300-7f03-455d-aaf1-352e4927b54d",
        "diamonds_to_you" => "86b34976-8f41-475b-a386-385080dc6e83",
        "the_pickaxe" => "d7519b5a-45ec-4d27-997c-89d402c6b57f",
        "over_here" => "71721c51-b7d1-46b1-b7ea-eb4c4126c3db",
        "breakdance" => "1dbaa006-0ec6-42c3-9440-a3bfa0c6fdbe",
        "chatting" => "59d9e78c-f0bb-4f14-9e9b-7ab4f58ffbf5",
        "the_hammer" => "7cec98d8-55cc-44fe-b0ae-2672b0b2bd37",
        "golf_clap" => "434489fd-ed42-4814-961a-df14161d67e0",
        "disappointed" => "a98ea25e-4e6a-477f-8fc2-9e8a18ab7004",
        "victory_cheer" => "d0c60245-538e-4ea2-bdd4-33477db5aa89",
        "foot_stomp" => "13334afa-bd66-4285-b3d9-d974046db479",
        "the_woodpunch" => "42fde774-37d4-4422-b374-89ff13a6535a",
        "sad_sigh" => "98a68056-e025-4c0f-a959-d6e330ccb5f5",
        "the_elytra" => "7393aa53-9145-4e66-b23b-ec86def6c6f2",
        "giddy" => "738497ce-539f-4e06-9a03-dc528506a468",
        "hooray" => "c4b5b251-24d3-43eb-9c05-46be246aeefb"
    ];

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
        self::$lang = new lang($this);
        self::$settings = new Config($this->getDataFolder() . 'Settings.yml', Config::YAML);
        self::$session = new Session($this);
        Entity::registerEntity(CustomNPC::class, true);
        $this->getServer()->getPluginManager()->registerEvents(new EventsHandler($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new EmoteTimer ($this), (is_bool(($seconds = self::$settings->get('emote-timer'))) ? 10 : $seconds) * 20);
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
                        $player->sendMessage(self::PREFIX . self::$lang::get(self::$lang::get(self::$lang::RCA_NOTFOUND)));
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
                        $player->sendMessage(self::PREFIX . COLOR::GREEN . self::$lang::get(self::$lang::NPC_EDITMODE_TUTORIAL));
                    } else {
                        self::$session::setEditorMode($player, false);
                        $player->sendMessage(self::PREFIX . COLOR::GREEN . self::$lang::get(self::$lang::NPC_EDITMODE_DISABLE));
                    }
                } else {
                    $player->sendMessage(self::PREFIX . COLOR::RED . self::$lang::get(self::$lang::NPC_EDITGMODE_TUTORIAL2));
                }
                return true;
            }
            $this->spawn($player);
            $player->sendMessage(self::PREFIX . COLOR::GREEN . self::$lang::get(self::$lang::NPC_CREATION_MSG));
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
