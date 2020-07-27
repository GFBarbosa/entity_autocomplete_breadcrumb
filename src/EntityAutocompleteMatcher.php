<?php

namespace Drupal\entity_autocomplete_breadcrumb;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;

class EntityAutocompleteMatcher extends \Drupal\Core\Entity\EntityAutocompleteMatcher {

  public function getMatches($target_id, $selection_handler, $selection_settings, $string = '') {

    $matches = [];
    if (!isset($string)) {
      return $matches;
    }

    $handler = $this->selectionManager->getInstance([
      'target_type' => $target_id,
      'handler' => $selection_handler,
      'handler_settings' => $selection_settings,
    ]);

    $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
    $entity_labels = $handler->getReferenceableEntities($string, $match_operator, 20);

    foreach ($entity_labels as $values) {
      foreach ($values as $entity_id => $label) {
        if ($target_id == 'taxonomy_term') {
          $custom_label = $this->getLabelForTerm($entity_id, $target_id, $label);
        }
        else {
          $custom_label = $label;
        }
        $key = "$label ($entity_id)";
        $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
        $key = Tags::encode($key);
        $matches[] = ['value' => $key, 'label' => $custom_label];
      }
    }
    return $matches;
  }

  protected function getLabelForTerm($entity_id, $entity_type_id, $label) {
    $term_storage = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');

    $parents = $term_storage->loadAllParents($entity_id);
    $first = array_pop($parents);
    $label = $first->getName();
    foreach (array_reverse($parents) as $term) {
      $label = $label . ' >> ' . $term->getName();
    }
    return $label;
  }

}