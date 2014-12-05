<?php

/**
 * The extension base class.
 */
class Minz_Extension {
	private $name;
	private $entrypoint;
	private $path;
	private $author;
	private $description;
	private $version;
	private $type;

	public static $authorized_types = array(
		'system',
		'user',
	);

	/**
	 * The constructor to assign specific information to the extension.
	 *
	 * Available fields are:
	 * - name: the name of the extension (required).
	 * - entrypoint: the extension class name (required).
	 * - path: the pathname to the extension files (required).
	 * - author: the name and / or email address of the extension author.
	 * - description: a short description to describe the extension role.
	 * - version: a version for the current extension.
	 * - type: "system" or "user" (default).
	 *
	 * It must not be redefined by child classes.
	 *
	 * @param $meta_info contains information about the extension.
	 */
	public function __construct($meta_info) {
		$this->name = $meta_info['name'];
		$this->entrypoint = $meta_info['entrypoint'];
		$this->path = $meta_info['path'];
		$this->author = isset($meta_info['author']) ? $meta_info['author'] : '';
		$this->description = isset($meta_info['description']) ? $meta_info['description'] : '';
		$this->version = isset($meta_info['version']) ? $meta_info['version'] : '0.1';
		$this->setType(isset($meta_info['type']) ? $meta_info['type'] : 'user');
	}

	/**
	 * Used when installing an extension (e.g. update the database scheme).
	 *
	 * It must be redefined by child classes.
	 */
	public function install() {}

	/**
	 * Used when uninstalling an extension (e.g. revert the database scheme to
	 * cancel changes from install).
	 *
	 * It must be redefined by child classes.
	 */
	public function uninstall() {}

	/**
	 * Call at the initialization of the extension (i.e. when the extension is
	 * enabled by the extension manager).
	 *
	 * It must be redefined by child classes.
	 */
	public function init() {}

	/**
	 * Getters and setters.
	 */
	public function getName() {
		return $this->name;
	}
	public function getEntrypoint() {
		return $this->entrypoint;
	}
	public function getPath() {
		return $this->path;
	}
	public function getAuthor() {
		return $this->author;
	}
	public function getDescription() {
		return $this->description;
	}
	public function getVersion() {
		return $this->version;
	}
	public function getType() {
		return $this->type;
	}
	private function setType($type) {
		if (!in_array($type, self::$authorized_types)) {
			throw new Minz_ExtensionException('invalid `type` info', $this->name);
		}
		$this->type = $type;
	}

	/**
	 * Return the url for a given file.
	 *
	 * @param $filename name of the file to serve.
	 * @param $type the type (js or css) of the file to serve.
	 * @return the url corresponding to the file.
	 */
	public function getFileUrl($filename, $type) {
		$dir = end(explode('/', $this->path));
		$file_name_url = urlencode($dir . '/static/' . $filename);

		$absolute_path = $this->path . '/static/' . $filename;
		$mtime = @filemtime($absolute_path);

		$url = '/ext.php?f=' . $file_name_url .
		       '&amp;t=' . $type .
		       '&amp;' . $mtime;
		return Minz_Url::display($url);
	}
}