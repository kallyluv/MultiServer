<?

namespace xjustjqy\MultiServer\classmap\level;

use pocketmine\level\Level as DF;
use xjustjqy\MultiServer\classmap\Server;

class Level extends DF {

  /** @var string */
  private $server;
  /** @var DF */
  private $level;
  
  public function __construct(DF $level, Server $server) {
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
