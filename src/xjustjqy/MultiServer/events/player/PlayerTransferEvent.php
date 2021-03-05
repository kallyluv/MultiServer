<?

namespace xjustjqy\MultiServer\events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use xjustjqy\MultiServer\classmap\Server as SudoServer;
use xjustjqy\MultiServer\classmap\Player as SudoPlayer;
use xjustjqy\MultiServer\Loader as API;

class PlayerTransferEvent extends PluginEvent {
  
  /** @var Player */
  private $who;
  /** @var SudoServer */
  private $from;
  /** @var SudoServer */
  private $to;
  
 public function __construct(Player $who, SudoServer $from, SudoServer $to) {
   $this->who = $this->getAPI()->toSudoPlayer($who);
   $this->to = $to;
   $this->from = $from;
 }
  
  public function getPlayer() : Player {
   return $this->player;
  }
  
  public function getFrom() : SudoServer {
    return $this->from; 
  }
  
  public function getTo() : SudoServer {
   return $this->to; 
  }
  
  public function getAPI() : API {
   return API::getInstance(); 
  }
  
}
