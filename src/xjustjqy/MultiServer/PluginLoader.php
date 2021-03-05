<?

namespace xjustjqy\MultiServer;

use xjustjqy\MultiServer\Server as SudoServer;
use pocketmine\plugin\PluginLoader as Default;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\command\PluginCommand;
use pocketmine\command\SimpleCommandMap;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\permission\Permissible;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\Server;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Utils;
use function array_intersect;
use function array_map;
use function array_merge;
use function array_pad;
use function class_exists;
use function count;
use function dirname;
use function explode;
use function file_exists;
use function get_class;
use function gettype;
use function implode;
use function in_array;
use function is_a;
use function is_array;
use function is_bool;
use function is_dir;
use function is_string;
use function is_subclass_of;
use function iterator_to_array;
use function mb_strtoupper;
use function mkdir;
use function shuffle;
use function stripos;
use function strpos;
use function strtolower;
use function is_file;
use function strlen;
use function substr;
use const DIRECTORY_SEPARATOR;

class PluginLoader extends Default {
  
  /** @var string */
  
  private $dir;
  /** @var SudoServer */
	private $server;

	/** @var SimpleCommandMap */
	private $commandMap;

	/** @var Plugin[] */
	protected $plugins = [];

	/** @var Plugin[] */
	protected $enabledPlugins = [];

	/**
	 * @var PluginLoader[]
	 * @phpstan-var array<class-string<PluginLoader>, PluginLoader>
	 */
	protected $fileAssociations = [];

	/** @var string|null */
	private $pluginDataDirectory;
  
  public function __construct(string $dir, SudoServer $server, SimpleCommandMap $commandMap, ?string $pluginDataDirectory) {
    $this->dir = $dir; 
    $this->server = $server;
		$this->commandMap = $commandMap;
		$this->pluginDataDirectory = $pluginDataDirectory;
		if($this->pluginDataDirectory !== null){
			if(!file_exists($this->pluginDataDirectory)){
				@mkdir($this->pluginDataDirectory, 0777, true);
			}elseif(!is_dir($this->pluginDataDirectory)){
				throw new \RuntimeException("Plugin data path $this->pluginDataDirectory exists and is not a directory");
			}
		}
    $this->registerInterface($this);
  }
  
  public function registerInterface(Default $loader) : void{
		$this->fileAssociations[get_class($loader)] = $loader;
	}
  
  public function canLoadPlugin(string $path) : bool{
		$ext = ".phar";
		return is_file($path) and substr($path, -strlen($ext)) === $ext;
	}
  
  public function getPluginDescription(string $file) : ?PluginDescription{
		$phar = new \Phar($file);
		if(isset($phar["plugin.yml"])){
			return new PluginDescription($phar["plugin.yml"]->getContent());
		}

		return null;
	}

	public function getAccessProtocol() : string{
		return "phar://";
	}
  
  /**
	 * @return Plugin[]
	 */
	public function getPlugins() : array{
		return $this->plugins;
	}

	private function getDataDirectory(string $pluginPath, string $pluginName) : string{
		if($this->pluginDataDirectory !== null){
			return $this->pluginDataDirectory . $pluginName;
		}
		return dirname($pluginPath) . DIRECTORY_SEPARATOR . $pluginName;
	}
  
  /**
	 * @return null|Plugin
	 */
	public function getPlugin(string $name){
		if(isset($this->plugins[$name])){
			return $this->plugins[$name];
		}

		return null;
	}

  
  public function loadPlugins() {
    $directory = $this->dir;
    if(!is_dir($directory)){
			return [];
		}

		$plugins = [];
		$loadedPlugins = [];
		$dependencies = [];
		$softDependencies = [];
		if(is_array($newLoaders)){
			$loaders = [];
			foreach($newLoaders as $key){
				if(isset($this->fileAssociations[$key])){
					$loaders[$key] = $this->fileAssociations[$key];
				}
			}
		}else{
			$loaders = $this->fileAssociations;
		}

		$files = iterator_to_array(new \FilesystemIterator($directory, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS));
		shuffle($files); //this prevents plugins implicitly relying on the filesystem name order when they should be using dependency properties
		foreach($loaders as $loader){
			foreach($files as $file){
				if(!is_string($file)) throw new AssumptionFailedError("FilesystemIterator current should be string when using CURRENT_AS_PATHNAME");
				if(!$loader->canLoadPlugin($file)){
					continue;
				}
				try{
					$description = $loader->getPluginDescription($file);
					if($description === null){
						continue;
					}

					$name = $description->getName();
					if(stripos($name, "pocketmine") !== false or stripos($name, "minecraft") !== false or stripos($name, "mojang") !== false){
						$this->server->getLogger()->error($this->server->getLanguage()->translateString("pocketmine.plugin.loadError", [$name, "%pocketmine.plugin.restrictedName"]));
						continue;
					}elseif(strpos($name, " ") !== false){
						$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.plugin.spacesDiscouraged", [$name]));
					}

					if(isset($plugins[$name]) or $this->getPlugin($name) instanceof Plugin){
						$this->server->getLogger()->error($this->server->getLanguage()->translateString("pocketmine.plugin.duplicateError", [$name]));
						continue;
					}

					if(!$this->isCompatibleApi(...$description->getCompatibleApis())){
						$this->server->getLogger()->error($this->server->getLanguage()->translateString("pocketmine.plugin.loadError", [
							$name,
							$this->server->getLanguage()->translateString("%pocketmine.plugin.incompatibleAPI", [implode(", ", $description->getCompatibleApis())])
						]));
						continue;
					}

					if(count($description->getCompatibleOperatingSystems()) > 0 and !in_array(Utils::getOS(), $description->getCompatibleOperatingSystems(), true)) {
						$this->server->getLogger()->error($this->server->getLanguage()->translateString("pocketmine.plugin.loadError", [
							$name,
							$this->server->getLanguage()->translateString("%pocketmine.plugin.incompatibleOS", [implode(", ", $description->getCompatibleOperatingSystems())])
						]));
						continue;
					}

					if(count($pluginMcpeProtocols = $description->getCompatibleMcpeProtocols()) > 0){
						$serverMcpeProtocols = [ProtocolInfo::CURRENT_PROTOCOL];
						if(count(array_intersect($pluginMcpeProtocols, $serverMcpeProtocols)) === 0){
							$this->server->getLogger()->error($this->server->getLanguage()->translateString("pocketmine.plugin.loadError", [
								$name,
								$this->server->getLanguage()->translateString("%pocketmine.plugin.incompatibleProtocol", [implode(", ", $pluginMcpeProtocols)])
							]));
							continue;
						}
					}

					$plugins[$name] = $file;

					$softDependencies[$name] = array_merge($softDependencies[$name] ?? [], $description->getSoftDepend());
					$dependencies[$name] = $description->getDepend();

					foreach($description->getLoadBefore() as $before){
						if(isset($softDependencies[$before])){
							$softDependencies[$before][] = $name;
						}else{
							$softDependencies[$before] = [$name];
						}
					}
				}catch(\Throwable $e){
					$this->server->getLogger()->error($this->server->getLanguage()->translateString("pocketmine.plugin.fileError", [$file, $directory, $e->getMessage()]));
					$this->server->getLogger()->logException($e);
				}
			}
		}

		while(count($plugins) > 0){
			$loadedThisLoop = 0;
			foreach($plugins as $name => $file){
				if(isset($dependencies[$name])){
					foreach($dependencies[$name] as $key => $dependency){
						if(isset($loadedPlugins[$dependency]) or $this->getPlugin($dependency) instanceof Plugin){
							unset($dependencies[$name][$key]);
						}elseif(!isset($plugins[$dependency])){
							$this->server->getLogger()->critical($this->server->getLanguage()->translateString("pocketmine.plugin.loadError", [
								$name,
								$this->server->getLanguage()->translateString("%pocketmine.plugin.unknownDependency", [$dependency])
							]));
							unset($plugins[$name]);
							continue 2;
						}
					}

					if(count($dependencies[$name]) === 0){
						unset($dependencies[$name]);
					}
				}

				if(isset($softDependencies[$name])){
					foreach($softDependencies[$name] as $key => $dependency){
						if(isset($loadedPlugins[$dependency]) or $this->getPlugin($dependency) instanceof Plugin){
							$this->server->getLogger()->debug("Successfully resolved soft dependency \"$dependency\" for plugin \"$name\"");
							unset($softDependencies[$name][$key]);
						}elseif(!isset($plugins[$dependency])){
							//this dependency is never going to be resolved, so don't bother trying
							$this->server->getLogger()->debug("Skipping resolution of missing soft dependency \"$dependency\" for plugin \"$name\"");
							unset($softDependencies[$name][$key]);
						}else{
							$this->server->getLogger()->debug("Deferring resolution of soft dependency \"$dependency\" for plugin \"$name\" (found but not loaded yet)");
						}
					}

					if(count($softDependencies[$name]) === 0){
						unset($softDependencies[$name]);
					}
				}

				if(!isset($dependencies[$name]) and !isset($softDependencies[$name])){
					unset($plugins[$name]);
					$loadedThisLoop++;
					if(($plugin = $this->loadPlugin($file, $loaders)) instanceof Plugin){
						$loadedPlugins[$name] = $plugin;
					}else{
						$this->server->getLogger()->critical($this->server->getLanguage()->translateString("pocketmine.plugin.genericLoadError", [$name]));
					}
				}
			}

			if($loadedThisLoop === 0){
				//No plugins loaded :(
				foreach($plugins as $name => $file){
					$this->server->getLogger()->critical($this->server->getLanguage()->translateString("pocketmine.plugin.loadError", [$name, "%pocketmine.plugin.circularDependency"]));
				}
				$plugins = [];
			}
		}

		return $loadedPlugins;
  }
  
  /**
	 * @param PluginLoader[] $loaders
	 */
	public function loadPlugin(string $path, array $loaders = null) : ?Plugin{
		foreach($loaders ?? $this->fileAssociations as $loader){
			if($loader->canLoadPlugin($path)){
				$description = $loader->getPluginDescription($path);
				if($description instanceof PluginDescription){
					$this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.plugin.load", [$description->getFullName()]));
					try{
						$description->checkRequiredExtensions();
					}catch(PluginException $ex){
						$this->server->getLogger()->error($ex->getMessage());
						return null;
					}

					$dataFolder = $this->getDataDirectory($path, $description->getName());
					if(file_exists($dataFolder) and !is_dir($dataFolder)){
						$this->server->getLogger()->error("Projected dataFolder '" . $dataFolder . "' for " . $description->getName() . " exists and is not a directory");
						return null;
					}
					if(!file_exists($dataFolder)){
						mkdir($dataFolder, 0777, true);
					}

					$prefixed = $loader->getAccessProtocol() . $path;
					$loader->loadPlugin($prefixed);

					$mainClass = $description->getMain();
					if(!class_exists($mainClass, true)){
						$this->server->getLogger()->error("Main class for plugin " . $description->getName() . " not found");
						return null;
					}
					if(!is_a($mainClass, Plugin::class, true)){
						$this->server->getLogger()->error("Main class for plugin " . $description->getName() . " is not an instance of " . Plugin::class);
						return null;
					}

					try{
						/**
						 * @var Plugin $plugin
						 * @see Plugin::__construct()
						 */
						$plugin = new $mainClass($loader, $this->server, $description, $dataFolder, $prefixed);
						$plugin->onLoad();
						$this->plugins[$plugin->getDescription()->getName()] = $plugin;

						$pluginCommands = $this->parseYamlCommands($plugin);

						if(count($pluginCommands) > 0){
							$this->commandMap->registerAll($plugin->getDescription()->getName(), $pluginCommands);
						}

						return $plugin;
					}catch(\Throwable $e){
						$this->server->getLogger()->logException($e);
						return null;
					}
				}
			}
		}

		return null;
	}
  
}
