<?

namespace xjustjqy\MultiServer\worlds;

use pocketmine\level\Level as DefaultLevel;
use xjustjqy\MultiServer\classmap\Server;

class WorldManager {
 
  /** @var Level[] */
  private $levels = [];
  
  public function __construct() {
    $this->levels = Server::getInstance()->getLevels();
  }
  
}
