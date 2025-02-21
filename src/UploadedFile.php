<?php

namespace Equit;

/**
 * Represents a file uploaded to the application.
 *
 * This class is used by the LibEquit\Request class to manage files uploaded to the application. Each file uploaded is
 * represented by a single object of this class.
 *
 * The class's constructor is private. To create new instances, use the static functions createFromData() and
 * createFromFile(). This helps ensure valid objects are created.
 *
 * Each UploadedFile created has a name, MIME type and either content or a temporary file in which the content is being
 * stored. Files actually uploaded by the user agent to the application all initially have a temporary file to contain
 * their content. To fetch the name use the name() method; for the mime type use the mimeType() method.
 *
 * The file's raw content is available from the data() method. This method will provide the data regardless of whether
 * the UploadedFile object was created from a temporary file or actual file data. In the case of temporary files, the
 * content of the file is read and cached in the object. This means that future calls to data() are faster because they
 * don't need to re-read the temporary file.
 *
 * The isNull() method can be used to determine whether the UploadedFile object references any data. It will check that
 * the object either has some data or an uploaded file. Note that objects using a temporary file will be considered non-
 * null even if the temporary file they reference is not valid.
 *
 * ### Events
 * This module does not emit any events.
 *
 * @noconnections
 * ### Connections
 * This module does not connect to any events.
 *
 * @nosettings
 * ### Settings
 * This module does not read any settings.
 *
 * @nosession
 * ### Session Data
 * This module does not create a session context.
 *
 * @class UploadedFile
 * @author Darren Edale
 * @package bead-framework
 */
class UploadedFile
{
	/** The name of the file. */
	private ?string $m_name = null;

	/** The path to the temporary file. */
	private ?string $m_tempFile = null;

	/** The file data, if loaded. */
	private ?string $m_fileData = null;

	/** The MIME type of the file. */
	private ?string $m_mimeType = null;

	/**
	 * Create a new UploadedFile object.
	 *
	 * The constructor is private - use on of the static create methods to
	 * create new objects.
	 *
	 * By default a `null` UploadedFile object willl be created.
	 *
	 * @param $name string _optional_ The name of the file.
	 */
	private function __construct(?string $name = null)
	{
		$this->setName($name);
	}

	/**
	 * Create a new UploadedFile object from some file data.
	 *
	 * The data is treated as a byte sequence. If no name is given, or it's `null` an object with no name is created.
	 *
	 * @param string $data The file data.
	 * @param string $name _optional_ The file name.
	 *
	 * @return UploadedFile The new `UploadedFile`.
	 */
	public static function createFromData(string $data, ?string $name = null): UploadedFile
	{
		$ret = new UploadedFile($name);
		$ret->setData($data);
		return $ret;
	}

	/**
	 * Create a new UploadedFile object from a temporary file.
	 *
	 * This method does not check to ensure that the provided file path exists.
	 *
	 * If no name is given, or it's `null` an object with no name is created.
	 *
	 * @param string $tempFile The temporary file path.
	 * @param string $name _optional_ The file name.
	 *
	 * @return UploadedFile The new UploadedFile.
	 */
	public static function createFromFile(string $tempFile, ?string $name = null)
	{
		$ret = new UploadedFile($name);
		$ret->setTempFile($tempFile);
		return $ret;
	}

	/**
	 * Set the name for the uploaded file.
	 *
	 * The name set here is the logical name of the file. It should not contain any path components, and has nothing to
	 * do with the name of the temporary file that contains the file's data.
	 *
	 * The name can be set to `null` to unset the existing name.
	 *
	 * @param string $name The file name.
	 */
	public function setName(?string $name): void
	{
		$this->m_name = $name;
	}

	/**
	 * Fetch the name of the uploaded file.
	 *
	 * The name is the logical name of the file. In cases where the file has been uploaded to the application by the
	 * user agent, it is the name of the file the user chose. It has nothing to do with the name of the temporary file
	 * that contains the file's data.
	 *
	 * @return string The file name, or `null` if no name is set.
	 */
	public function name(): ?string
	{
		return $this->m_name;
	}

	/**
	 * Set the path for the uploaded file's temporary file.
	 *
	 * This method does not check to ensure that the provided file path exists.
	 * It can be set to `null` to unset the current temporary file path.
	 *
	 * If a temporary file path is set, any file data that the object held in
	 * its cache is discarded. This is true even if the temporary file path is
	 * unset, which will result in a null object.
	 *
	 * @param string $tempFile The path to the temporary file.
	 */
	public function setTempFile(?string $tempFile): void
	{
		$this->m_tempFile = $tempFile;
		$this->m_fileData = null;
	}

	/**
	 * Fetch the path for the uploaded file's temporary file.
	 *
	 * @return string The path to the temporary file, or `null` if no path is set.
	 */
	public function tempFile(): ?string
	{
		return $this->m_tempFile;
	}

	/**
	 * Set the MIME type for the uploaded file data.
	 *
	 * @param string|null $type string The MIME type.
	 *
	 * The MIME type will be checked for syntactic validity but will not be validated against the actual content or
	 * against any putative list of known MIME types.
	 *
	 * The MIME type can be set to `null` to unset the existing MIME type.
	 */
	public function setMimeType(?string $type): void
	{
		static $s_validRx = "/[a-z][a-z0-9\\-]*\\/[a-z][a-z0-9\\-]*/";

		if (is_string($type) && !preg_match($s_validRx, $type)) {
			AppLog::warning("\"{$type}\" may not be a valid MIME type", __FILE__, __LINE__, __FUNCTION__);
		}

		$this->m_mimeType = $type;
	}

	/**
	 * Fetch the MIME type for the uploaded file data.
	 *
	 * For files uploaded to the application by the user agent, the MIME type is
	 * the type reported by the user agent; it is not derived by assessing the
	 * content of the uploaded file.
	 *
	 * @return string The MIME type, or `null` if no MIME type is set.
	 */
	public function mimeType(): ?string
	{
		return $this->m_mimeType;
	}

	/**
	 * Set the data for the uploaded file.
	 *
	 * If file data is set any temporary file path is unset. This is true even if the data provided is `null`, which
	 * will result in a null object. The temporary file path remains unmodified if invalid data is provided.
	 *
	 * @return bool `true` if the file data was set, `false` otherwise.
	 */
	public function setData(?string $data)
	{
		$this->m_fileData = $data;
		$this->m_tempFile = null;
		return true;
	}

	/**
	 * Fetch the file data.
	 *
	 * This method returns the uploaded file content. If the file data is contained in a temporary file, the content of
	 * the file is read, cached and returned; otherwise, the file data is returned. For `null` objects, `null` is
	 * returned. If the temporary file cannot be read for any reason `null` is returned.
	 *
	 * Subsequent calls to this method will re-use the cached data unless the temporary file path is altered in the
	 * meantime. This means that subsequent calls are much faster as they don't involve any disk IO.
	 *
	 * The string returned should be treated as a raw byte sequence.
	 *
	 * @return string The file data, or `null` if none is set or the temporary file cannot be read.
	 */
	public function data(): ?string
	{
		if (is_null($this->m_fileData) && !is_null($this->m_tempFile)) {
			if (!is_file($this->m_tempFile)) {
				AppLog::error("file \"{$this->m_tempFile}\" is not a file");
			} else if (!is_readable($this->m_tempFile)) {
				AppLog::error("file \"{$this->m_tempFile}\" is not readable");
			} else {
				$this->m_fileData = file_get_contents($this->m_tempFile);
			}
		}

		return $this->m_fileData;
	}

	/**
	 * Check whether the uploaded file is null.
	 *
	 * The file is considered null if it has neither a temporary file path nor cached data.
	 *
	 * @return bool `true` if the uploaded file is null, `false` if it is not.
	 */
	public function isNull()
	{
		return empty($this->m_tempFile) && is_null($this->m_fileData);
	}
}
