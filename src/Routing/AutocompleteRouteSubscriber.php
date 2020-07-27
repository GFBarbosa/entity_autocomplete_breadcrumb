<?php

namespace Drupal\entity_autocomplete_breadcrumb\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class AutocompleteRouteSubscriber extends RouteSubscriberBase {

  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.entity_autocomplete')) {
      $route->setDefault('_controller', '\Drupal\entity_autocomplete_breadcrumb\Controller\EntityAutocompleteController::handleAutocomplete');
    }
  }

}