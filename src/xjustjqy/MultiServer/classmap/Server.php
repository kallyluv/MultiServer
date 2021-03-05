<?

namespace xjustjqy\MultiServer\classmap;

use xjustjqy\MultiServer\Loader as API;
use pocketmine\Server as Default;

class Server extends Default {
 
  /** @var Level[] */
  protected $levels = [];
  /** @var Default */
  private $server;
  /** @var string */
  private $name;
  /** @var int */
  private $id;
  
  public function __construct(Default $server, string $name, int $id) {
    $this->server = $server;
    $this->name = $name;
    $this->id = $id;
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
	
	public function getId() : int {
		return $this->id;	
	}
	
	public function getName() : string {
		return $this->name;	
	}
  
}
