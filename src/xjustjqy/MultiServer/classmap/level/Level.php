<?

namespace xjustjqy\MultiServer\classmap\level;

use pocketmine\level\Level as Default;
use xjustjqy\MultiServer\classmap\Server;

class Level extends Default {

  /** @var string */
  private $server;
  /** @var Default */
  private $level;
  
  public function__construct(Default $level, Server $server) {
    $this->level = $level;
    $this->server = $server;
  }
  
  public function fetch() {
   return $this; 
  }
  
  public function getServer() {
   return $this->server; 
  }
  
}
