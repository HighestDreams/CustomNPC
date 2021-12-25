<?php
declare(strict_types=1);

namespace HighestDreams\CustomNPC;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\nbt\tag\CompoundTag;
use HighestDreams\CustomNPC\Entity\CustomNPC;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\{Config, TextFormat as COLOR};

class NPC extends PluginBase
{

    public const PREFIX = COLOR::BOLD . COLOR::WHITE . "Custom" . COLOR::RED . "NPC " . COLOR::RESET . COLOR::DARK_RED . "> ";
    public static $editor = [];
    public static $teleport = [];
    public static $timer = [];
    public static $emotes = ["wave" => "4c8ae710-df2e-47cd-814d-cc7bf21a3d67", "simple_clap" => "9a469a61-c83b-4ba9-b507-bdbe64430582", "over_there" => "ce5c0300-7f03-455d-aaf1-352e4927b54d", "diamonds_to_you" => "86b34976-8f41-475b-a386-385080dc6e83", "the_pickaxe" => "d7519b5a-45ec-4d27-997c-89d402c6b57f", "over_here" => "71721c51-b7d1-46b1-b7ea-eb4c4126c3db", "breakdance" => "1dbaa006-0ec6-42c3-9440-a3bfa0c6fdbe", "chatting" => "59d9e78c-f0bb-4f14-9e9b-7ab4f58ffbf5", "the_hammer" => "7cec98d8-55cc-44fe-b0ae-2672b0b2bd37", "golf_clap" => "434489fd-ed42-4814-961a-df14161d67e0", "disappointed" => "a98ea25e-4e6a-477f-8fc2-9e8a18ab7004", "victory_cheer" => "d0c60245-538e-4ea2-bdd4-33477db5aa89", "foot_stomp" => "13334afa-bd66-4285-b3d9-d974046db479", "the_woodpunch" => "42fde774-37d4-4422-b374-89ff13a6535a", "sad_sigh" => "98a68056-e025-4c0f-a959-d6e330ccb5f5", "the_elytra" => "7393aa53-9145-4e66-b23b-ec86def6c6f2", "giddy" => "738497ce-539f-4e06-9a03-dc528506a468", "hooray" => "c4b5b251-24d3-43eb-9c05-46be246aeefb"];
    public static $settings;
    public static $Instance;

    public function onEnable()
    {
        self::$Instance = $this;
        $this->saveResource('Settings.yml');
        self::$settings = new Config("{$this->getDataFolder()}Settings.yml", Config::YAML);

        Entity::registerEntity(CustomNPC::class, true);
        $this->getServer()->getPluginManager()->registerEvents(new EventsHandler($this), $this);

        $seconds = self::$settings->get('emote-timer');
        $this->getScheduler()->scheduleRepeatingTask(new EmoteTimer(), (is_bool($seconds) ? 10 : $seconds) * 20);

        foreach (['Capes', 'Skins'] as $dir) {
            if (!is_dir($path = "{$this->getDataFolder()}$dir")) {
                @mkdir($path);
            }
        }

        foreach ($this->getResources() as $name => $info) {
            if (preg_match('/.+\.png/i', $name) and !is_file($newPath = "{$this->getDataFolder()}Capes\\$name")) {
                $capes = fopen($newPath, 'w+');
                $resource = $this->getResource($name);
                stream_copy_to_stream($resource, $capes);
                fclose($resource);
                fclose($capes);
            }
        }
    }

    /**
     * @param CustomNPC $NPC
     * @param string $value
     * @param null $Tag
     * @return bool
     */
    public static function isset(CustomNPC $NPC, string $value, $Tag = null): bool
    {
        return in_array(strtolower($value), self::get($NPC, $Tag ?? "Commands"));
    }

    /**
     * To get NPC commands and other stuff.
     * @param CustomNPC $NPC
     * @param string $Tag
     * @return array
     */
    public static function get(CustomNPC $NPC, string $Tag): array
    {
        $result = [];
        if (!is_null($Tags = $NPC->namedtag->getCompoundTag($Tag))) {
            foreach ($Tags as $Tag) {
                $result[] = $Tag->getValue();
            }
        }
        return $result;
    }

    /**
     * @param CustomNPC $NPC
     * @param string $value
     * @param null $Tag
     */
    public static function remove(CustomNPC $NPC, string $value, $Tag = null)
    {
        if (!is_null($Tags = $NPC->namedtag->getCompoundTag($Tag ?? 'Commands'))) {
            $Tags->removeTag($value);
            $NPC->namedtag->setTag($Tags);
        }
    }

    /**
     * @param CustomNPC $NPC
     * @param string $value
     * @param null $Tag
     */
    public static function add(CustomNPC $NPC, string $value, $Tag = null)
    {
        $TheTag = $Tag ?? 'Commands';
        $Tags = $NPC->namedtag->getCompoundTag($TheTag) ?? new CompoundTag($TheTag);
        $Tags->setString($value, $value);
        $NPC->namedtag->setTag($Tags);
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$Instance;
    }

    /**
     * @param CommandSender $player
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool
    {
        switch (strtolower($command->getName())) {

            case 'npc':
                if ($player instanceof Player) {
                    if (count($args) < 2) {
                        if (isset($args[0])) {
                            /* NPC Editor */
                            if (strtolower($args[0]) === "edit") {
                                if (!isset(self::$editor[array_search($player->getName(), self::$editor)])) {
                                    self::$editor[] = $player->getName();
                                    $player->sendMessage(self::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_EDIT_ENABLED));
                                } else {
                                    unset(self::$editor[array_search($player->getName(), self::$editor)]);
                                    $player->sendMessage(self::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_EDITMODE_DISABLE));
                                }
                            } else {
                                $player->sendMessage(self::PREFIX . COLOR::RED . 'Usage: ' . COLOR::YELLOW . '/npc edit');
                            }
                        } else {
                            $this->spawn($player);
                            $player->sendMessage(self::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_CREATION_MESSAGE));
                        }
                    } else {
                        $player->sendMessage(self::PREFIX . COLOR::RED . "To spawn NPC: " . COLOR::YELLOW . "/npc" . COLOR::RED . "\nTo turn ON/OFF NPC edit mode: " . COLOR::YELLOW . "/npc edit"); /* Bruh don't need translate */
                    }
                }
                break;

            case 'rca':
                if (is_null($this->getServer()->getPluginManager()->getPlugin('Slapper'))) {
                    if (count($args) >= 2) {
                        $target = $this->getServer()->getPlayer(array_shift($args));
                        if ($target instanceof Player) {
                            $this->getServer()->dispatchCommand($target, trim(implode(" ", $args)));
                        } else {
                            $player->sendMessage(self::PREFIX . Language::translated(Language::RCA_PLAYER_NOTFOUND));
                        }
                    } else {
                        $player->sendMessage(self::PREFIX . "Usage: /rca <player-name> <command-for-execute>");
                    }
                }
                break;
        }
        return true;
    }

    /**
     * @param Player $player
     */
    public function spawn(Player $player)
    {
        $NBT = Entity::createBaseNBT($player, $player->getMotion(), $player->getYaw(), $player->getPitch());
        $NBT->setTag($player->namedtag->getTag("Skin"));
        $NPC = new CustomNPC ($player->getLevel(), $NBT);
        $NPC->setNameTag('Custom NPC');
        $NPC->yaw = $player->getYaw();
        $NPC->pitch = $player->getPitch();
        $NPC->spawnToAll();
    }

    /**
     * Check if name is in editor mode or not.
     *
     * @param Player $player
     * @return boolean
     */
    public function isEditor(Player $player): bool
    {
        return in_array($player->getName(), self::$editor);
    }

    /**
     * Check if NPC spawned for first time and doesn't have any interactions/commands yet!
     *
     * @param CustomNPC $npc
     * @return boolean
     */
    public function spawnedForFistTime(CustomNPC $npc): bool
    {
        return (is_null($npc->namedtag->getCompoundTag("Commands")) and is_null($npc->namedtag->getCompoundTag("Interactions")));
    }

    public function isInCoolDown(Player $player, CustomNPC $npc): bool
    {
        return isset(self::$timer[$npc->getId()][$player->getName()]);
    }

    public function addCooldown(Player $player, CustomNPC $npc)
    {
        self::$timer[$npc->getId()][$player->getName()] = microtime(true);
    }

    public function getNPCCooldown(CustomNPC $npc): float
    {
        return (float)min(preg_grep('/\d/i', NPC::get($npc, 'Settings')));
    }
}