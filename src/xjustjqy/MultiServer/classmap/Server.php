<?

namespace xjustjqy\MultiServer\classmap;

use xjustjqy\MultiServer\Loader as API;
use pocketmine\Server as DF;

class Server extends DF {
 
  /** @var Level[] */
  protected $levels = [];
  /** @var Level */
  protected $default;
  /** @var DF */
  private $server;
  /** @var string */
  private $name;
  /** @var int */
  private $id;

  /** @var self */
  private static $self;
  
  public function __construct(DF $server, string $name, int $id) {
		self::$self = $this;
    $this->server = $server;
    $this->name = $name;
    $this->id = $id;
    $this->loadLevels();
  }
	
	public static function fetch() {
		return self::$self;	
	}
  
  public function loadLevels() {
    foreach(API::getInstance()->getSettings()->getServers() as $server) {
      foreach($server["levels"] as $level) {
	$S = DF::getInstance();
	if(!$S->isLevelGenerated($level["name"])) {
           $S->generateLevel($level["name"], $level["seed"], $level["generator_type"], $level["options"]);
        }
        if($this->server->loadLevel($level["name"])) {
          $world = $this->server->getLevelByName($level["name"]);
          $this->levels[] = $world;
          $opts = $level["options"];
          if(isset($opts["default"]) && $opts["default"] === true) {
            $this->default = $world;
          }
        }
      }
    }
  }

  public function getDefaultWorld() : ?Level {
    return $this->default;
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
