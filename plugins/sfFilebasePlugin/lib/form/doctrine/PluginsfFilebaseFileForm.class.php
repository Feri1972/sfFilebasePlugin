<?php

/**
 * PluginsfFilebaseFile form.
 *
 * @package    form
 * @subpackage sfFilebaseFile
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginsfFilebaseFileForm extends BasesfFilebaseFileForm
{
  public function setup()
  {
    parent::setup();
    unset($this->widgetSchema['hash']);
    unset($this->widgetSchema['type']);
    unset($this->validatorSchema['hash']);
    unset($this->validatorSchema['type']);

    $this->validatorSchema['sf_filebase_directory_id']->setOption('required', false);
    $this->widgetSchema['tags'] = new sfWidgetFormInput();

    $this->validatorSchema['tags'] = new sfValidatorAnd(array(
      new sfValidatorString(),
      new sfValidatorRegex(array('pattern'=>'#^[^, ;]([^, ;]+[,; ] ?)*?[^, ;]+$#'))
    ), array('required'=>false));

    if($this->isNew())
    {
      $this->widgetSchema['pathname']     = new sfWidgetFormInputFile();
      $this->validatorSchema['pathname']  = new sfFilebasePluginValidatorFile(array('allow_overwrite'=> true, 'required'=>true));
    }
    else
    {
      $this->widgetSchema['pathname']->setAttribute('readonly', 'readonly');
      $tag_string = $this->getObject()->getTagsAsString();
      $this->widgetSchema['tags']->setDefault($tag_string);
      $this->validatorSchema['pathname']->setOption('required',false);
    }
  }

  /**
   * Betray him in a very nasty way ...
   * This is not a real column, but who cares...
   *
   * @param array $values
   */
  public function updateTagsColumn($tags)
  {
    $this->getObject()->setTags(sfFilebaseTagTable::splitTags($tags));
  }

  /**
   * Saves the current file for the field.
   *
   * @param  string          $field    The field name
   * @param  string          $filename The file name of the file to save
   * @param  sfValidatedFile $file     The validated file to save
   *
   * @return sfPluginValidatorUploadedFile The filename used to save the file
   */
  public function saveFile($field, $filename = null, sfValidatedFile $file = null)
  {
    $file = parent::saveFile($field, $filename, $file);
    $this->getObject()->setHash($file->getHash());
    return $file->getPathname();
  }
}