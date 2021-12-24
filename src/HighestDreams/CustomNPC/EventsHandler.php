<?php
declare(strict_types=1);
#=========================================#
# Plugin Custom NPC Made By HighestDreams #
#=========================================#
namespace HighestDreams\CustomNPC;

use DateTime;
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

    public $main;

    public function __construct(NPC $main)
    {
        $this->main = $main;
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function EntityDamageByEntityEvent (EntityDamageByEntityEvent $event)
    {
        $player = $event->getDamager();
        $NPC = $event->getEntity();

        if ($NPC instanceof CustomNPC and $player instanceof Player) {
            $event->setCancelled(true);

            if ($this->main->isEditor($player)) {
                (new CustomizeMain())->send($player, $NPC);
                return;
            }

            if ($this->main->spawnedForFistTime($NPC)) {
                if ($player->hasPermission('customNPC.permission')) { # Send tip to enable editor mode.
                    $player->sendMessage(NPC::PREFIX . COLOR::GREEN . Language::translated(Language::NPC_NEVER_ADDED_COMMAND));
                }
                return;
            }
            $this->execute($player, $NPC, 'Commands');
        }
    }

    /**
     * @param Player $player
     * @param CustomNPC $NPC
     * @param $event
     * @param string $tag
     */
    public function execute (Player $player, CustomNPC $NPC, string $tag) {
        # Cooldown stuff.
        if (($coolDown = $this->main->getNPCCooldown($NPC)) > 0) { # If npc has cooldown.
            if (isset(NPC::$timer[$NPC->getId()][$player->getName()]) and (NPC::$timer[$NPC->getId()][$player->getName()] + $this->main->getNPCCooldown($NPC) > (microtime(true)))) {
                $timeOne = new DateTime(DateTime::createFromFormat('U.u', (string)NPC::$timer[$NPC->getId()][$player->getName()])->format("H:i:s"));
                $timeTwo = new DateTime('now');
                $diff = $timeTwo->diff($timeOne);
                $player->sendPopup(str_replace('{seconds}', (string)($coolDown - $diff->s), NPC::$settings->get('cooldown-message')));
                return;
            }
            NPC::$timer[$NPC->getId()][$player->getName()] = microtime(true);
        }

        # Tags (Commands/Interactions) Execution.
        foreach (NPC::get($NPC, $tag) as $tags) {
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
        $maxDistance = is_bool($distance = NPC::$settings->get('rotation-distance')) ? 5 : $distance;

        if ($from->distance($to) < 0.1) {
            return;
        }

        # Shitty code :(
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
                    $pk->position = $NPC->asVector3()->add(0, 1.6, 0);
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
                        $this->execute($player, $NPC, 'Commands');
                        $event->setCancelled(true);
                    } elseif (count(NPC::get($NPC, 'Interactions')) >= 1) {
                        $this->execute($player, $NPC, 'Interactions');
                        $event->setCancelled(true);
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
        if (preg_replace('/§./i', '', $event->getMessage()) === "here") {
            if (isset(NPC::$teleport[$player->getName()])) {
                if (!is_null($NPC = $player->getLevel()->getEntity(NPC::$teleport[$player->getName()]))) {
                    $NPC->teleport($player->asPosition());
                    $NPC->yaw = $player->getYaw();
                    $NPC->pitch = $player->getPitch();
                    $player->sendMessage(NPC::PREFIX . COLOR::GREEN . 'NPC ' . COLOR::AQUA . NPC::$teleport[$player->getName()] . COLOR::GREEN . Language::translated(Language::NPC_TELEPORT));
                    unset(NPC::$teleport[$player->getName()]);
                } else {
                    $player->sendMessage(NPC::PREFIX . COLOR::RED . "You can't teleport NPCs to another levels.");
                }
                $event->setCancelled(true);
            }
        }
    }
}