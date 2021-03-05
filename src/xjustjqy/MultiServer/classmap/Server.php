<?

namespace xjustjqy\MultiServer\classmap;

use xjustjqy\MultiServer\Loader as API;
use xjustjqy\MultiServer\worlds\WorldManager;
use pocketmine\Server as DF;

class Server extends DF
{

  /** @var Level[] */
  protected $levels = [];
  /** @var Level */
  protected $levelDefault;
  /** @var DF */
  private $server;
  /** @var string */
  private $name;
  /** @var int */
  private $id;

  /** @var self */
  private static $self;

  public function __construct(DF $server, string $name, int $id, bool $fake = false)
  {
    self::$self = $this;
    $this->server = $server;
    $this->name = $name;
    $this->id = $id;
    if ($fake === false) {
      $this->loadLevels();
    }
  }

  public static function fetch()
  {
    return self::$self;
  }

  public function loadLevels()
  {
    foreach (API::getInstance()->getSettings()->getServers() as $name => $server) {
      if ($name !== $this->name) continue;
      foreach ($server["levels"] as $level) {
        $S = DF::getInstance();
        if (!$S->isLevelGenerated($level["name"])) {
          $S->generateLevel($level["name"], $level["seed"], $level["generator_type"], $level["options"]);
        }
        if ($this->server->loadLevel($level["name"])) {
          $world = $this->server->getLevelByName($level["name"]);
          $this->levels[] = $world;
          $opts = $level["options"];
          if (isset($opts["default"]) && $opts["default"] === true) {
            $this->levelDefault = $world;
          }
        }
      }
      if(isset($server["default"]) && $server["default"] === true) {
        DF::getInstance()->setDefaultLevel($this->getDefaultWorld());

      }
    }
  }

  public function getDefaultWorld(): ?Level
  {
    return $this->levelDefault;
  }

  public function getLevels(): array
  {
    return $this->levels;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }
}
