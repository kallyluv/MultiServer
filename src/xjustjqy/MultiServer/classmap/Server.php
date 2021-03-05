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
		define("ServerInstance", $this);
    $this->server = $server;
    $this->name = $name;
    $this->id = $id;
    $this->loadLevels();
  }
	
	public static function getInstance() {
		return ServerInstance;	
	}
  
  public function loadLevels() {
    foreach(API::getInstance()->getSettings()->getServers() as $server) {
      foreach($server["levels"] as $level) {
	$S = Default::getInstance();
	if(!$S->isLevelGenerated($level["name"])) {
           $S->generateLevel($level["name"], $level["seed"], $level["generator_type"], $level["options"]);
        }
        if($this->server->loadLevel($level["name"])) {
          $world = $this->server->getLevelByName($level["name"]);
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
