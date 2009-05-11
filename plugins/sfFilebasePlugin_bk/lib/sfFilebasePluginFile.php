<?php
/**
 * This file is part of the sfFilebase symfony plugin.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFilebasePluginFile represents each file that is not
 * handled in a special way.
 *
 * @see        splFileInfo
 * @package    de.optimusprime.sfFilebasePlugin
 * @author     Johannes Heinen <johannes.heinen@gmail.com>
 * @copyright  Johannes Heinen <johannes.heinen@gmail.com>
 */
class sfFilebasePluginFile extends SplFileInfo
{
  /**
   * Reference to files' Filebase-instance
   *
   * @var sfFilebasePlugin $filebase
   */
  protected $filebase;
  
  /**
   * sfFilebasePluginFileObject reference,
   * only not null if file was
   * opened for reading/writing.
   * 
   * @var sfFilebasePluginFileObject
   */
  protected $filebaseFileObject = null;

  /**
   * Constructor
   *
   * @param string $file_name
   */
  public function __construct($path_name, sfFilebasePlugin $filebase)
  {
    $this->filebase = $filebase;
    
    // get rid of trainling slash
    parent::__construct(sfFilebasePluginUtil::unifySlashes($path_name));
    $this->setInfoClass('sfFilebasePluginFile');
    $this->setFileClass('sfFilebasePluginFileObject');
  }

  /**
   * Returns extension of file
   *
   * @return string $extension or null if file has no extension
   */
  public function getExtension()
  {
    $extension = pathinfo(parent::getFilename(),PATHINFO_EXTENSION);
    if(!empty($extension))
    {
      return $extension;
    }
    return null;
  }
  
  /**
   * Returns true if file exist in filesystem.
   *
   * @return boolean
   */
  public function fileExists()
  {
    return file_exists($this->getPathname());
  }
  
  /**
   * Returns true if sfFilebasePluginFile is a
   * web image file. Used to factory
   * a sfFilebasePluginImage instance by sfFilebasePlugin::
   * getFilebaseFile()
   *
   * @todo improved mime-type detection
   * @return boolean true if file is an image
   */
  public function isImage()
  {
    return $this->filebase->getIsImage($this);
  }

  /**
   * ToString method
   *
   * @return string $pathname
   */
  public function __toString()
  {
    return $this->getPathname();
  }
  
  /**
   * If Format is string, Size will be formatted (kb, mb ...) 
   *
   * @throws sfFilebasePluginException
   * @param string $format = null | string
   */
  public function getSize($format=null)
  {
    if(!$this->fileExists())  throw new sfFilebasePluginException(sprintf('File %s does not exist.', $this->getPathname()));
    if(!$this->isReadable())  throw new sfFilebasePluginException(sprintf('File %s is not readable.', $this->getPathname()));
    $size = parent::getSize();
    if($format)
    {
      return sfFilebasePlugin::getStringFilesize((double)$size);
    }
    return $size;
  }
  
  public function getCTime($format = false)
  {
    if($format)
      return date($format, parent::getCTime());
    return parent::getCTime();
  }
  
  public function getMTime($format = false)
  {
    if($format)
      return date($format, parent::getMTime());
    return parent::getMTime();
  }
  
  public function getATime($format = false)
  {
    if($format)
      return date($format, parent::getATime());
    return parent::getATime();
  }
  
  /**
   * Retrieves Content-Type (mime-type)
   * of File.
   * 
   * @return string
   */
  public function getContentType()
  {
    return $this->getMimeType();
  }

  /**
   * Returns Mime-Type (Content-Type)
   * of file.
   *
   * @return string $mime
   */
  public function getMimeType()
  {
    return sfFilebasePluginUtil::getMimeByExtension($this->getExtension());
  }

  /**
   * Returns a sfFilebasePlugin instance
   *
   * @return sfFilebasePlugin $filebase
   */
  public function getFilebase()
  {
    return $this->filebase;
  }

  /**
   * Returns true if file is read- and write-able.
   * 
   * @return boolean
   */
  public function isRWritable()
  {
    return parent::isReadable() && parent::isWritable();
  }

  /**
   * Returns true if file exists and is writable.
   *
   * @return boolean
   */
  public function existsAndIsWritable()
  {
    return self::fileExists() && parent::isWritable();
  }

  /**
   * Generates md5 hash from pathname.
   * @return string $hash
   */
  public function getHash()
  {
    return $this->getFilebase()->getHashForFile($this);
  }

  /**
   * Trys to delete a file from fs.
   *
   * @throws sfFilebasePluginException
   */
  public function delete()
  {
    return $this->filebase->deleteFile($this);
  }

  /**
   * Changes the access permissions of a sfFilebasePluginFile
   *
   * @param integer
   * @throws sfFilebasePluginException
   * @return sfFilebasePluginFile $file
   */
  public function chmod($perms = 0755)
  {
    return $this->filebase->chmodFile($this, $perms);
  }

  /**
   * Renames a file.
   *
   * @param string $new_name: The new filename
   * @param boolean $overwrite: If true, existing files with same name will be
   *                            overwritten.
   * @throws sfFilebasePluginException
   * @return sfFilebasePluginFile $file
   */
  public function rename($new_name, $overwrite = true)
  {
    return $this->filebase->renameFile($this, $new_name, $overwrite);
  }

  /**
   * Copies a file to the given destination.
   *
   * @param mixed sfFilebasePluginFile | string $destination
   * @param boolean $overwrite: If true dest file will be overwritten
   * @return sfFilebasePluginFile $copied_file
   */
  public function copy($destination, $overwrite=false)
  {
    return $this->filebase->copyFile($this, $destination, $overwrite);
  }

  /**
   * Converts an absolute Pathname to a relative one,
   * as seen from sfFilebasePlugin-Directory.
   *
   * @throws sfFilebasePluginException
   * @return string $relative_pathname
   */
  public function getRelativePathFromFilebaseDirectory()
  {
    if(!$this->getFilebase()->isInFilebase($this)) throw new sfFilebasePluginException(sprintf('Path %s does not lie within FilebaseDirectory', $this->getPathname()));
    return ltrim(preg_replace('#^'.sfFilebasePluginUtil::unifySlashes($this->getFilebase()->getPathname()).'#','', sfFilebasePluginUtil::unifySlashes($this->getPathname()),1),'/\\');
  }

   /**
   * Returns filename, as seen absolutely from webroot.
   *
   * @throws sfFilebasePluginException
   * @return string $path
   */
  public function getAbsolutePathFromWebroot()
  {
    if(!$this->getFilebase()->isInFilebase($this)) throw new sfFilebasePluginException(sprintf('Path %s does not lie within FilebaseDirectory', $this->getPathname()));
    $path = '/' . ltrim(preg_replace('#^'.sfFilebasePluginUtil::unifySlashes($_SERVER['DOCUMENT_ROOT']).'#i','', sfFilebasePluginUtil::unifySlashes($this->getPathname())), '/\\');
    return $path;
  }

  /**
   * Returns a new sfFilebasePluginFile
   * 
   * @param string | sfFilebasePluginFile  $filename
   * @return sfFilebasePluginFile
   */
  public function getFilebaseFile($filename)
  {
    if(is_string($filename))
    {
      if(strlen($filename)>0)
      {
        if(sfFilebasePluginUtil::isAbsolutePathname($filename))
        {
          $filename = new sfFilebasePluginFile($filename, $this->filebase);
        }
        else
        {
          $filename = new sfFilebasePluginFile($this->getPathname().DIRECTORY_SEPARATOR.$filename, $this->filebase);
        }
      }
      else
      {
        $filename = $this;
      }
    }
    if($filename instanceof sfFilebasePluginFile)
    {
      // returns only true if file exists, so beware of that
      if($filename->isDir())
      {
        $filename = new sfFilebasePluginDirectory($filename->getPathname(), $this->filebase);
      }
      elseif($filename->isImage())
      {
        $filename = new sfFilebasePluginImage($filename->getPathname(), $this->filebase);
      }
      return $filename;
    }
    throw new sfFilebasePluginException(sprintf('File %s must be of type [string] or instanceof FilebaseFile', $filename));
  }

  /**
   * Opens the file for read/writing
   * 
   * @param string $open_mode
   * @param ressource $context stream context
   * @return sfFilebasePluginFileObject $file
   */
  public function openFile ($open_mode = 'r', $use_include_path = false, $context = null)
  {
    if(!$this->fileExists()) throw new sfFilebasePluginException (sprintf('File %s does not exist.', $this->getPathname()));
    $this->filebaseFileObject = new sfFilebasePluginFileObject($this, $this->getFilebase(), $open_mode, false, $context);
    return $this->filebaseFileObject;
  }

  /**
   * Returns a human readable string file type.
   * This could be for example "jpeg image" or "wma audio"
   * 
   * @return string
   */
  public function getHumanReadableFileType()
  {
    return sfFilebasePluginUtil::getStringTypeByExtension($this->getExtension());
  }
}