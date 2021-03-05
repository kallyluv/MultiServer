<?

namespace xjustjqy\MultiServer\classmap;

use xjustjqy\MultiServer\Loader as API;
use pocketmine\Server as Default;

class Server extends Default {
 
  /** @var Level[] */
  protected $levels = [];
  /** @var Default */
  private $server;
  
  public function __construct(Default $server) {
    $this->server = $server;
    $this->loadLevels();
  }
  
  public function loadLevels() {
    foreach(API::getInstance()->getSettings()->get("servers") as $server) {
      foreach($server["levels"] as $level) {
        if($this->server->loadLevel($level)) {
          $world = $this->server->getLevelByName($level);
          $this->levels[] = $world;
        }
      }
    }
  }
  
  public function getLevels() : array {
   return $this->levels; 
  }
  
}
