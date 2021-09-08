<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC;

use HighestDreams\CustomNPC\Entity\CustomNPC;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class Session
{

    public static $npc;

    public function __construct(NPC $npc)
    {
        self::$npc = $npc;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function isEditor(Player $player): bool
    {
        return isset(NPC::$editor[array_search($player->getName(), NPC::$editor)]);
    }

    /**
     * @param Player $player
     * @param bool $status
     */
    public static function setEditorMode(Player $player, bool $status)
    {
        if ($status === true) {
            NPC::$editor[] = $player->getName();
        } else {
            unset(NPC::$editor[array_search($player->getName(), NPC::$editor)]);
        }
    }

    /**
     * @param CustomNPC $npc
     * @return array
     */
    public static function getCommands(CustomNPC $npc): array
    {
        $result = [];
        if (!is_null($commands = $npc->namedtag->getCompoundTag("Commands"))) {
            foreach ($commands as $command) {
                $result[] = $command->getValue();
            }
        }
        return $result;
    }

    /**
     * @param CustomNPC $npc
     * @param string $command
     * @return bool
     */
    public static function isCommandExists(CustomNPC $npc, string $command): bool
    {
        if (!is_null($commands = $npc->namedtag->getCompoundTag("Commands"))) {
            return $commands->hasTag($command);
        }
        return false;
    }

    /**
     * @param CustomNPC $npc
     * @param string $command
     */
    public static function addCommand(CustomNPC $npc, string $command)
    {
        $commands = $npc->namedtag->getCompoundTag("Commands") ?? new CompoundTag("Commands");
        $commands->setString($command, $command);
        $npc->namedtag->setTag($commands);
    }

    /**
     * @param CustomNPC $npc
     * @param string $command
     */
    public static function removeCommand(CustomNPC $npc, string $command)
    {
        if (!is_null($commands = $npc->namedtag->getCompoundTag("Commands"))) {
            $commands->removeTag($command);
            $npc->namedtag->setTag($commands);
        }
    }

    /**
     * @param CustomNPC $npc
     * @return array
     */
    public static function getSettings(CustomNPC $npc): array
    {
        $result = [];
        if (!is_null($settings = $npc->namedtag->getCompoundTag("Settings"))) {
            foreach ($settings as $setting) {
                $result[] = $setting->getValue();
            }
        }
        return $result;
    }

    /**
     * @param CustomNPC $npc
     * @param string $setting
     * @return bool
     */
    public static function isSettingExists(CustomNPC $npc, string $setting): bool
    {
        if (!is_null($settings = $npc->namedtag->getCompoundTag("Settings"))) {
            return $settings->hasTag($setting);
        }
        return false;
    }

    /**
     * @param CustomNPC $npc
     * @param string $setting
     */
    public static function addSetting(CustomNPC $npc, string $setting)
    {
        $settings = $npc->namedtag->getCompoundTag("Settings") ?? new CompoundTag("Settings");
        $settings->setString($setting, $setting);
        $npc->namedtag->setTag($settings);
    }

    /**
     * @param CustomNPC $npc
     * @param string $setting
     */
    public static function removeSetting(CustomNPC $npc, string $setting)
    {
        if (!is_null($settings = $npc->namedtag->getCompoundTag("Settings"))) {
            $settings->removeTag($setting);
            $npc->namedtag->setTag($settings);
        }
    }

    /**
     * @param Player $player
     * @param bool $value
     * @param $npcID
     */
    public static function setTeleporting(Player $player, bool $value, $npcID)
    {
        if ($value === true) {
            NPC::$teleport[$player->getName()] = $npcID;
        } else {
            unset(NPC::$teleport[$player->getName()]);
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function isTeleporting(Player $player): bool
    {
        return isset(NPC::$teleport[$player->getName()]);
    }
}