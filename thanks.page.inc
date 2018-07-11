<?php

/**
 * @file
 * Contains thanks.page.inc.
 *
 * Page callback for Thanks entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Thanks templates.
 *
 * Default template: thanks.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_thanks(array &$variables) {
  // Fetch Thanks Entity Object.
  $thanks = $variables['elements']['#thanks'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
