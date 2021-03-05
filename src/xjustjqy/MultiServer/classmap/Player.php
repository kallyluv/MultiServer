<?

namespace xjustjqy\MultiServer\classmap;

use pocketmine\Player as DefaultPlayer;
use pocketmine\Server as DefaultServer;
use xjustjqy\MultiServer\Loader as API;

class Player extends DefaultPlayer {
  
  /** @var DefaultPlayer */
  private $player;
  /** @var Server */
  private $server;
 
  public function __construct(DefaultPlayer $player, Server $current) {
    $this->player = $player;
    $this->setServer($current);
  }
  
  public function getServer() : Server {
    return $this->server;
  }
  
  public function setServer(Server $server) {
    $this->server = $server;
  }
  
  public function getPlayer() : DefaultPlayer {
    return $this->player; 
  }
}
