<?php
namespace Drupal\theme_reference_wbu;

use Drupal\user\Entity\User;
use Stephane888\Debug\debugLog;

class JobseekersResouces {

  public static function wbupreprocess_page(&$variables)
  {
    $parameters = \Drupal::routeMatch()->getParameters()->all();
    // debugLog::kintDebugDrupal(self::getListLayout(), 'getListLayout');
    //$variables['page']['demo_layout'] = self::LoadLayout('top_header');
    // dump($layoutPluginManager);
    self::loadUserInfo($variables, $parameters);
    // dump($variables);
    // dump($profiles);
  }

  public static function DisplayUser(User $user, $view_mode = 'carousel')
  {
    $fields = $user->getFields();
    foreach ($fields as $key => $field) {
      $fields[$key] = $user->{$key}->view($view_mode);
    }
    $fields['created_time'] = \Drupal::service('date.formatter')->format($user->getCreatedTime(), 'long');
    $fields['login_time'] = \Drupal::service('date.formatter')->format($user->getLastLoginTime(), 'long');
    $fields['acces_time'] = \Drupal::service('date.formatter')->format($user->getLastAccessedTime(), 'long');
    $fields['uid'] = $user->id();
    $fields['roles'] = $user->getRoles(true);
    return $fields;
  }

  protected static function getListLayout()
  {
    $layoutPluginManager = \Drupal::service('plugin.manager.core.layout');
    $layoutDefinitions = $layoutPluginManager->getDefinitions();
    $definedLayouts = [];
    foreach ($layoutDefinitions as $layoutDefinition) {
      $definedLayouts[] = $layoutDefinition->getLabel();
    }
    return [
      '#theme' => 'item_list',
      '#items' => $definedLayouts
    ];
  }
 
 /**
   * Ce service 'plugin.manager.core.layout' est est disponible si et seulement si le module "Layout Builder" est installé.
   * @param string $id_layout
   * @return array
   */
  protected static function LoadLayout($id_layout)
  {
    $layoutPluginManager = \Drupal::service('plugin.manager.core.layout');
    // Provide any configuration to the layout plugin if necessary.
    $layoutInstanceConfiguration = [];
    $layoutInstance = $layoutPluginManager->createInstance($id_layout, $layoutInstanceConfiguration);
    $regions = [];
    return $layoutInstance->build($regions);
  }

  /**
   * Si l'utilisateur est connecté et est sur son profile.
   * on affiche le resume et les informations editables.
   */
  protected static function loadUserInfo(&$variables, $parameters)
  {
    if ($variables["logged_in"] && ! empty($variables['user'])) {
      $uid = $variables['user']->id();
      $user = self::DisplayUser(User::load($uid));
      if (\Drupal::currentUser()->id() == $user['uid']) {
        $variables["current_user"] = $user;
      }
    }
  }

  // public static function
}
