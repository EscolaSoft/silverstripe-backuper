<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 10.05.17
 * Time: 10:23
 */

use Aura\Sql\ExtendedPdo;

class BackupAdmin extends ModelAdmin {

  static $allowed_actions = array(
    'RestoreForm'
  );
  private static $managed_models = array(
    'Backup',
    'BackupUpload'
  );
  private static $url_segment = 'backper';
  private static $menu_title = 'Backups';
  public function getEditForm($id = null, $fields = null) {
    if ($this->modelClass == 'BackupUpload'){
      //return Documentation::getHTML();

      $fields = new FieldList(
        FileField::create('SQLBackup', 'Your SQL File Backup')
      );

      $actions = new FieldList(
        FormAction::create("RestoreForm")->setTitle("Restore your Backup")
      );

      $required = new RequiredFields('SQLBackup');

      $form = new Form($this, 'RestoreForm', $fields, $actions, $required);

      return $form;


    } else {
      /** @var CMSForm $form */
      $form = parent::getEditForm($id, $fields);

      $gridfield = $form->Fields()
        ->dataFieldByName($this->sanitiseClassName($this->modelClass));

      $gridfieldConfig = $gridfield->getConfig();

      // remove delete & edit buttons
      $gridfieldConfig
        ->removeComponentsByType('GridFieldEditButton');

      /** @var FieldList $fields */
      //$fields = $form->Fields();
      //$fields->add(new FileField('Upload'));
      //var_dump($form);
      return $form;
    }
  }


  public function RestoreForm($data) {
    /** TODO check all permissions */
    /** TODO what if ZIP ? */

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
    $pdo = new ExtendedPdo(
      "mysql:host=$server;dbname=$database",
      $username,
      $password,
      [], // driver attributes/options as key-value pairs
      []  // queries to execute after connection
    );



    if (isset($_FILES['SQLBackup'])) {
      $sql = file_get_contents($_FILES['SQLBackup']['tmp_name']);
      $pdo->exec($sql);
    }
    return $this->redirectBack();
  }

  private function recreateBackupList() {
    /** TODO remove old and add new */
  }

  public function doRestore($data, Form $form) {
    var_dump($form);
    die('');
    $form->sessionMessage('Hello '. $data['Name'], 'success');

    return $this->redirectBack();
  }
}