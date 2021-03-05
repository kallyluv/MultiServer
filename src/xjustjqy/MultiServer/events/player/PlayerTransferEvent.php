<?

namespace xjustjqy\MultiServer\events\player;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;
use pocketmine\Player;
use pocketmine\level\Level;
use xjustjqy\MultiServer\classmap\Server as SudoServer;
use xjustjqy\MultiServer\Loader as API;

class PlayerTransferEvent extends PluginEvent implements Cancellable
{

  /** @var Player */
  private $who;
  /** @var SudoServer */
  private $from;
  /** @var SudoServer */
  private $to;
  /** @var Level */
  private $level;

  public function __construct(Player $who, SudoServer $from, SudoServer $to, Level $level)
  {
    parent::__construct(API::getInstance());
    $this->who = $who;
    $this->to = $to;
    $this->from = $from;
    $this->level = $level;
    $this->getAPI()->getPlayerManager()->reregister($who, $to);
  }

  public function onRun()
  {
    if($this->isCancelled() || is_null($this->level)) return;
    $this->who->teleport($this->level->getSafeSpawn());
    $this->who->sendMessage("Sent to server: " . $this->to->getName());
  }

  public function getPlayer(): Player
  {
    return $this->player;
  }

  public function getFrom(): SudoServer
  {
    return $this->from;
  }

  public function getTo(): SudoServer
  {
    return $this->to;
  }

  public function getAPI(): API
  {
    return API::getInstance();
  }
}
