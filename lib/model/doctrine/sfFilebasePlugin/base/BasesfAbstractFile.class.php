<?php

/**
 * BasesfAbstractFile
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $filename
 * @property string $hash
 * @property string $comment
 * @property string $type
 * @property Doctrine_Collection $tags
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class BasesfAbstractFile extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('sf_abstract_files');
        $this->hasColumn('filename', 'string', 255, array('type' => 'string', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('hash', 'string', 255, array('type' => 'string', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('comment', 'string', null, array('type' => 'string'));
        $this->hasColumn('type', 'string', 255, array('type' => 'string', 'length' => 255));


        $this->index('u1', array('type' => 'unique', 'fields' => array(0 => 'filename', 1 => 'lft', 2 => 'rgt')));
        $this->setSubClasses(array('sfFilebaseFile' => array('type' => '1'), 'sfFilebaseDirectory' => array('type' => '2')));
    }

    public function setUp()
    {
        $this->hasMany('sfFilebaseTag as tags', array('local' => 'id',
                                                      'foreign' => 'sf_abstract_files_id'));

        $nestedset0 = new Doctrine_Template_NestedSet(array('hasManyRoots' => false));
        $this->actAs($nestedset0);
    }
}