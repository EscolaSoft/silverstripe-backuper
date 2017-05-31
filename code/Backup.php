<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 10.05.17
 * Time: 10:25
 */

use Ifsnop\Mysqldump as IMysqldump;

class Backup extends DataObject {

  static $db = array(
    'Database'=>'Boolean',
    'Assets'=>'Boolean'
  );

  static $defaults = array(
    'Database'=>true,
    'Assets'=>true
  );

  private $create = false;
  private $dir = '';
  static $summary_fields = array(
    'ID',
    'Created',
    'DBFile',
    'AssetsFile',
  );
  private function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
  }

  private function getFileLink($file) {
    $output = HTMLText::create();
    $dir = BASE_PATH.DIRECTORY_SEPARATOR.BACKAPERS_BASE.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$this->Created;
    $path = BACKAPERS_BASE.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$this->Created;
    $filepath = $dir.DIRECTORY_SEPARATOR.$file;
    if (is_file($filepath)) {
      $fs = filesize($filepath);
      $hfs = $this->human_filesize($fs);
      $output->setValue("<a target='_blank' href='$path/$file'>Download $file ($hfs)</a>");
      return $output;
    } else {
      return 'No File';
    }
  }

  public function DBFile() {
    return $this->getFileLink('dump.sql');
  }

  public function AssetsFile() {
    return $this->getFileLink('assets.zip');
  }
  public function onBeforeWrite() {
    if ($this->ID) {
    } else {
      $this->create = true;
    }
    parent::onBeforeWrite();
  }
  public function getCMSFields() {
    $fields = parent::getCMSFields();
    $html = new LiteralField('doc', '<p>Press save below to create a new backup.</p> <p><strong>This is going to take some time</strong></p><p>Go back to list to download backup</p>');
    $fields->add($html);
    return $fields;
  }

  public function onBeforeDelete(){
    $backup = DataObject::get_one('Backup', $this->ID);
    $dir = BASE_PATH.DIRECTORY_SEPARATOR.BACKAPERS_BASE.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$backup->Created;
    @unlink($dir.DIRECTORY_SEPARATOR.'dump.sql');
    @unlink($dir.DIRECTORY_SEPARATOR.'assets.zip');
    @rmdir($dir.DIRECTORY_SEPARATOR);
    parent::onBeforeDelete();

  }

  public function onAfterWrite() {
    parent::onAfterWrite();
    if ($this->create) {
      $backup = DataObject::get_one('Backup', $this->ID);
      $dir = BASE_PATH.DIRECTORY_SEPARATOR.BACKAPERS_BASE.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$backup->Created;
      if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
      }
      $this->dir = $dir;
      $this->createSqlDump();
      $this->createZipArchive();
    }
  }

  protected function createZipArchive() {
    $filepath = $this->dir.DIRECTORY_SEPARATOR.'assets.zip';
    $zippy = \Alchemy\Zippy\Zippy::load();
    $archive = $zippy->create($filepath, array(
      'folder' => ASSETS_PATH
    ), true);
  }

  protected function createSqlDump() {
    global $databaseConfig;
    $server = $databaseConfig["server"];
    $username = $databaseConfig["username"];
    $password = $databaseConfig["password"];
    $database = $databaseConfig["database"];
    if (stristr(PHP_OS, 'DAR')) {
      if ($server == 'localhost') {
        $server = '127.0.0.1';//MAC os issue
      }
    }
    try {
      $dump = new IMysqldump\Mysqldump("mysql:host=$server;dbname=$database", $username, $password, array(
        'add-drop-table' => true
      ));
      $dump->start( $this->dir.DIRECTORY_SEPARATOR.'dump.sql');
    } catch (\Exception $e) {
      /** TODO Dataobject validation */
      die ('mysqldump-php error: ' . $e->getMessage());
    }

  }

  public function canEdit($member=null) {
    $can = parent::canEdit($member);
    if ($this->ID) {
      return false;
    } else {
      return $can;
    }
  }




} 