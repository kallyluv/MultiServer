<?

namespace xjustjqy\MultiServer\classmap;

use pocketmine\Player as DFPlayer;
use pocketmine\Server as DFServer;
use xjustjqy\MultiServer\Loader as API;

class Player extends DFPlayer {
  
  /** @var DFPlayer */
  private $player;
  /** @var Server */
  private $server;
 
  public function __construct(DFPlayer $player, Server $current) {
    $this->player = $player;
    $this->setServer($current);
  }
  
  public function getServer() : Server {
    return $this->server;
  }
  
  public function setServer(Server $server) {
    $this->server = $server;
  }
  
  public function getPlayer() : DFPlayer {
    return $this->player; 
  }
}
