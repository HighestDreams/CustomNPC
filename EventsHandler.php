<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC;

use HighestDreams\CustomNPC\Entity\CustomNPC;
use HighestDreams\CustomNPC\Form\CustomizeMain;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as COLOR;

class EventsHandler implements Listener
{

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onHitNPC (EntityDamageByEntityEvent $event)
    {
        $player = $event->getDamager();
        $NPC = $event->getEntity();

        if ($NPC instanceof CustomNPC) {
            if ($player instanceof Player) {
                /* If never added command to NPC (Like spawned NPC for the first time) */
                if (is_null($NPC->namedtag->getCompoundTag("Commands"))) {
                    if ($player->hasPermission('customNPC.permission')) {
                        /* If clicked person (Is op or they have customNPCs permission) */
                        if (!isset(NPC::$editor[array_search($player->getName(), NPC::$editor)])) {
                            $player->sendMessage(NPC::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_NEVER_ADDED_COMMAND));
                        } else {
                            (new CustomizeMain())->send($player, $NPC);
                        }
                    }
                } else {
                    /* If commands added before to the clicked NPC */
                    if (isset(NPC::$editor[array_search($player->getName(), NPC::$editor)])) {
                        /* If clicked person (Is Op or they have customNPCs permission) */
                        (new CustomizeMain())->send($player, $NPC);
                    } else { /* Now it's time for execute for the player */
                        $this->execute($player, $NPC, $event, 'Commands');
                    }
                }
            }
            $event->setCancelled(true);
        }
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     * @param $event
     * @param string $tag
     */
    public function execute (Player $player, CustomNPC $NPC, $event, string $tag) {
        /* Cool-down */
        foreach (NPC::get($NPC, 'Settings') as $setting) {
            if (is_numeric($setting) and (int)$setting > 0) {
                if (!isset(NPC::$timer[$NPC->getId()][$player->getName()])) {
                    NPC::$timer[$NPC->getId()][$player->getName()] = microtime(true);
                    /* Execute Tags for the player */
                    foreach (NPC::get($NPC, $tag) as $tags) {
                        /* Replace tags in command before execution */
                        foreach (["{player}" => '"' . $player->getName() . '"', "{rca}" => 'rca'] as $search => $replace) {
                            $tags = str_replace($search, $replace, $tags);
                        }
                        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $tags);
                    }
                    $event->setCancelled(true);
                    return;
                }
                if ((NPC::$timer[$NPC->getId()][$player->getName()] + (float)$setting > (microtime(true)))) {
                    $player->sendPopup(NPC::$settings->get('cooldown-message'));
                    $event->setCancelled(true);
                    return;
                } else {
                    NPC::$timer[$NPC->getId()][$player->getName()] = microtime(true);
                }
            }
        }
        /* So here is for those NPCs haven't cool-sown set */
        foreach (NPC::get($NPC, $tag) as $tags) {
            /* Replace tags in command before execution */
            foreach (["{player}" => '"' . $player->getName() . '"', "{rca}" => 'rca'] as $search => $replace) {
                $tags = str_replace($search, $replace, $tags);
            }
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $tags);
        }
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function NPCRotation (PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();
        $maxDistance = NPC::$settings->get('rotation-distance');

        if (is_bool($maxDistance)) {
            $maxDistance = 7;
        }

        if ($from->distance($to) < 0.1) {
            /* Its joke time: This is too close, get away from me.  ( LMAO pls do not swear me, Ik it was cringe ) */
            return;
        }

        foreach ($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $NPC) {
            if ($NPC instanceof CustomNPC) {
                if (NPC::isset($NPC, 'rotation', 'Settings')) {
                    $angle = atan2($player->z - $NPC->z, $player->x - $NPC->x);
                    $yaw = (($angle * 180) / M_PI) - 90;
                    $dist = (new Vector2($NPC->x, $NPC->z))->distance($player->x, $player->z);
                    $angle = atan2($dist, $player->y - $NPC->y);
                    $pitch = (($angle * 180) / M_PI) - 90;

                    $pk = new MovePlayerPacket();
                    $pk->entityRuntimeId = $NPC->getId();
                    $pk->position = $NPC->asVector3()->add(0, 1.6, /* When NPC size increase and if rotation be on for NPC, NPC move in air, but now its fixed */ 0);
                    $pk->yaw = $yaw;
                    $pk->pitch = $pitch;
                    $pk->headYaw = $yaw;
                    $pk->onGround = $NPC->onGround;
                    $player->dataPacket($pk);
                }
            }
        }
    }

    /**
     * @param EntitySpawnEvent $event
     */
    public function onNPCSpawn(EntitySpawnEvent $event)
    {
        if ($event->getEntity() instanceof CustomNPC) {
            /* Clear lag plugin won't kill NPC (Code from Slapper plugin!) */
            if (!is_null($clearLag = Server::getInstance()->getPluginManager()->getPlugin("ClearLagg"))) {
                $clearLag->exemptEntity($event->getEntity());
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onNPCDamage(EntityDamageEvent $event)
    {
        if ($event->getEntity() instanceof CustomNPC) {
            if (!$event instanceof EntityDamageByEntityEvent) {
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onDataPkReceive (DataPacketReceiveEvent $event) {
        $player = $event->getPlayer();
        $pk = $event->getPacket();
        if ($pk instanceof InventoryTransactionPacket and $pk->trData instanceof UseItemOnEntityTransactionData) {
            if ($pk->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT) {
                $NPC = Server::getInstance()->findEntity($pk->trData->getEntityRuntimeId());
                if ($NPC instanceof CustomNPC) {
                    if (NPC::isset($NPC, 'pair_interactions_with_commands', 'Settings')) {
                        $this->execute($player, $NPC, $event, 'Commands');
                    } else {
                        $this->execute($player, $NPC, $event, 'Interactions');
                    }
                }
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $level = $player->getLevel();

        if (preg_match('/here/i', $message) and strlen($message) <= 10) { /* I don't know, Mybe who teleports NPC, used color format (Like: §l§a) */
            if (isset(NPC::$teleport[$player->getName()])) {
                if (!is_null($NPC = $level->getEntity(NPC::$teleport[$player->getName()]))) {
                    $NPC->teleport($player->asVector3());
                    $NPC->yaw = $player->getYaw();
                    $NPC->pitch = $player->getPitch();
                    $player->sendMessage(NPC::PREFIX . COLOR::GREEN . 'NPC ' . COLOR::AQUA . NPC::$teleport[$player->getName()] . COLOR::GREEN . Language::translated(Language::NPC_TELEPORT));
                    unset(NPC::$teleport[$player->getName()]);
                }
                $event->setCancelled(true);
            }
        }
    }
}