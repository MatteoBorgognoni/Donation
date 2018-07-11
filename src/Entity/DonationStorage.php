<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Database\Database;

/**
 * Defines the storage handler class for donations. Needed in order to create programmatically a label
 * I would have used a simpler solution but this has been the safest option.
 * I needed the id to create the label and it seems that after the doSaveFieldItems method would be too late and before the id is not yet available
 * So.. for now is ok.. (but don't like it)
 *
 *
 * This extends the base storage class, adding required special handling for
 * node entities.
 */
class DonationStorage extends SqlContentEntityStorage  implements ContentEntityStorageInterface {
  
  /**
   * {@inheritdoc}
   */
  protected function doSaveFieldItems(ContentEntityInterface $entity, array $names = []) {
    $full_save = empty($names);
    $update = !$full_save || !$entity->isNew();
    
    if ($full_save) {
      $shared_table_fields = TRUE;
      $dedicated_table_fields = TRUE;
    }
    else {
      $table_mapping = $this->getTableMapping();
      $storage_definitions = $this->entityManager->getFieldStorageDefinitions($this->entityTypeId);
      $shared_table_fields = FALSE;
      $dedicated_table_fields = [];
      
      // Collect the name of fields to be written in dedicated tables and check
      // whether shared table records need to be updated.
      foreach ($names as $name) {
        $storage_definition = $storage_definitions[$name];
        if ($table_mapping->allowsSharedTableStorage($storage_definition)) {
          $shared_table_fields = TRUE;
        }
        elseif ($table_mapping->requiresDedicatedTableStorage($storage_definition)) {
          $dedicated_table_fields[] = $name;
        }
      }
    }
    // Update shared table records if necessary.
    if ($shared_table_fields) {
      $record = $this->mapToStorageRecord($entity->getUntranslated(), $this->baseTable);
      // Create the storage record to be saved.
      if ($update) {
        $default_revision = $entity->isDefaultRevision();
        if ($default_revision) {
          $this->database
            ->update($this->baseTable)
            ->fields((array) $record)
            ->condition($this->idKey, $record->{$this->idKey})
            ->execute();
        }
        if ($this->revisionTable) {
          if ($full_save) {
            $entity->{$this->revisionKey} = $this->saveRevision($entity);
          }
          else {
            $record = $this->mapToStorageRecord($entity->getUntranslated(), $this->revisionTable);
            $entity->preSaveRevision($this, $record);
            $this->database
              ->update($this->revisionTable)
              ->fields((array) $record)
              ->condition($this->revisionKey, $record->{$this->revisionKey})
              ->execute();
          }
        }
        if ($default_revision && $this->dataTable) {
          $this->saveToSharedTables($entity);
        }
        if ($this->revisionDataTable) {
          $new_revision = $full_save && $entity->isNewRevision();
          $this->saveToSharedTables($entity, $this->revisionDataTable, $new_revision);
        }
      }
      else {
        $insert_id = $this->database
          ->insert($this->baseTable, ['return' => Database::RETURN_INSERT_ID])
          ->fields((array) $record)
          ->execute();
        // Even if this is a new entity the ID key might have been set, in which
        // case we should not override the provided ID. An ID key that is not set
        // to any value is interpreted as NULL (or DEFAULT) and thus overridden.
        
        if (!isset($record->{$this->idKey})) {
          $record->{$this->idKey} = $insert_id;
        }
        $entity->{$this->idKey} = (string) $record->{$this->idKey};
        if ($this->revisionTable) {
          $record->{$this->revisionKey} = $this->saveRevision($entity);
        }
  
        // Set label ----------------------------------------------------
        $reference = $entity->prepareReference();
        $entity->setReference($reference);
        
        if ($this->dataTable) {
          $this->saveToSharedTables($entity);
        }
        if ($this->revisionDataTable) {
          $this->saveToSharedTables($entity, $this->revisionDataTable);
        }
      }
    }
    
    // Update dedicated table records if necessary.
    if ($dedicated_table_fields) {
      $names = is_array($dedicated_table_fields) ? $dedicated_table_fields : [];
      $this->saveToDedicatedTables($entity, $update, $names);
    }

  }
  

}
