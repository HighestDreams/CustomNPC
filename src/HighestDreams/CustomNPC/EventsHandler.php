<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC;

use HighestDreams\CustomNPC\Entity\CustomNPC;
use HighestDreams\CustomNPC\Form\CustomizeMain;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as COLOR;

class EventsHandler implements Listener
{

    public static $session;
    public $npc;

    public function __construct(NPC $npc)
    {
        $this->npc = $npc;
        self::$session = new Session(NPC::getInstance());
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onHitNPC(EntityDamageByEntityEvent $event)
    {
        $player = $event->getDamager();
        $npc = $event->getEntity();
        if ($npc instanceof CustomNPC) {
            if ($player instanceof Player) {
                if (is_null($npc->namedtag->getCompoundTag("Commands"))) {
                    if ($player->hasPermission('customnpc.permission')) {
                        if (!self::$session::isEditor($player)) {
                            $player->sendMessage(NPC::PREFIX . COLOR::GREEN . "For customize npc first enable editor mode with command: /npc edit");
                        } else {
                            (new CustomizeMain(NPC::getInstance()))->send($player, $npc);
                        }
                    }
                } else {
                    if (self::$session::isEditor($player)) {
                        (new CustomizeMain(NPC::getInstance()))->send($player, $npc);
                    } else {
                        foreach (self::$session::getSettings($npc) as $setting) {
                            if (ctype_alnum($setting)) {
                                if ((int)$setting > 0) {
                                    if (!isset(NPC::$timer[$npc->getId()][$player->getName()])) {
                                        NPC::$timer[$npc->getId()][$player->getName()] = microtime(true);
                                        foreach (self::$session::getCommands($npc) as $command) {
                                            $command = str_replace("{player}", '"' . $player->getName() . '"', $command);
                                            $command = str_replace("{rca}", 'rca', $command);
                                            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $command);
                                        }
                                        $event->setCancelled(true);
                                        return;
                                    }
                                    if ((NPC::$timer[$npc->getId()][$player->getName()] + (float)$setting > (microtime(true)))) {
                                        $player->sendTip(NPC::$settings->get('cooldown-message'));
                                        $event->setCancelled(true);
                                        return;
                                    } else {
                                        NPC::$timer[$npc->getId()][$player->getName()] = microtime(true);
                                    }
                                }
                            }
                        }
                        foreach (self::$session::getCommands($npc) as $command) {
                            $command = str_replace("{player}", '"' . $player->getName() . '"', $command);
                            $command = str_replace("{rca}", 'rca', $command);
                            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $command);
                        }
                    }
                }
            }
            $event->setCancelled(true);
        }
    }

    /**
     * @param PlayerMoveEvent $ev
     */
    public function NPCRotation(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();
        $maxDistance = NPC::$settings->get('rotation-distance');

        if ($from->distance($to) < 0.1) return;

        foreach ($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $npc) {

            $angle = atan2($player->z - $npc->z, $player->x - $npc->x);
            $yaw = (($angle * 180) / M_PI) - 90;
            $v = new Vector2($npc->x, $npc->z);
            $dist = $v->distance($player->x, $player->z);
            $angle = atan2($dist, $player->y - $npc->y);
            $pitch = (($angle * 180) / M_PI) - 90;

            if ($npc instanceof CustomNPC) {
                if (self::$session::isSettingExists($npc, 'rotation')) {
                    $pk = new MovePlayerPacket();
                    $pk->entityRuntimeId = $npc->getId();
                    $pk->position = $npc->asVector3()->add(0, $npc->getEyeHeight(), 0);
                    $pk->yaw = $yaw;
                    $pk->pitch = $pitch;
                    $pk->headYaw = $yaw;
                    $pk->onGround = $npc->onGround;
                    $player->dataPacket($pk);
                }
            } else {
                $pk = new MoveActorAbsolutePacket();
                $pk->entityRuntimeId = $npc->getId();
                $pk->position = $npc->asVector3();
                $pk->xRot = $pitch;
                $pk->yRot = $yaw;
                $pk->zRot = $yaw;
                $player->dataPacket($pk);
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
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $level = $player->getLevel();

        if (preg_match('/here/i', $message) and strlen($message) <= 4) { /* Maybe the player uses different formats for chatting?! */
            if (self::$session::isTeleporting($player)) {
                if (!is_null($npc = $level->getEntity(NPC::$teleport[$player->getName()]))) {
                    $npc->teleport($player->asVector3());
                    $npc->yaw = $player->getYaw();
                    $npc->pitch = $player->getPitch();
                    $player->sendMessage(NPC::PREFIX . COLOR::GREEN . 'NPC ' . COLOR::AQUA . NPC::$teleport[$player->getName()] . COLOR::GREEN . ' has been teleported to you successfully.');
                    self::$session::setTeleporting($player, false, 0);
                }
                $event->setCancelled(true);
            }
        }
    }
}