<?

namespace xjustjqy\MultiServer\worlds;

use pocketmine\level\Level as DFLevel;
use xjustjqy\MultiServer\classmap\Server;

class WorldManager {
 
  /** @var Level[] */
  private $levels = [];
  
  public function __construct() {
    $this->levels = Server::fetch()->getLevels();
  }
  
}
