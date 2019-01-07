<?php

namespace Drupal\cci_cpnt_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Provides a 'SearchBlock' block.
 *
 * @Block(
 *  id = "cci_search_block",
 *  admin_label = @Translation("CCI recherche"),
 * )
 */
class CciSearchBlock extends BlockBase {

  protected $nbr_links = 8;

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $config = $this->getConfiguration();
    $links = unserialize($config['links']);

    $form['background_image'] = array(
      '#type' => 'managed_file',
      '#title' => $this->t('Image de fond'),
      '#default_value' => isset($config['background_image']) ? [$config['background_image']] : NULL,
      '#upload_location' => 'public://backgrounds',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg', 'png'],
      ],
    );

    for ($i = 1; $i <= $this->nbr_links; $i++) {
      $form['link_' . $i] = [
        '#type' => 'details',
        '#title' => 'lien ' . $i,
        '#open' => FALSE
      ];

      $form['link_' . $i]['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Titre du lien ' . $i),
        '#default_value' => isset($links[$i]['title']) ? $links[$i]['title'] : '',
      );
      $form['link_' . $i]['url'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Url du lien ' . $i),
        '#default_value' => isset($links[$i]['url']) ? $links[$i]['url'] : '',
      );
    }

    $form['link_1']['#open'] = TRUE;

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $links = [];
    $form_file = $form_state->getValue('background_image', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      $file->setPermanent();
      $file->save();

      $this->setConfigurationValue('background_image', $file->id());
    }

    for ($i = 1; $i <= $this->nbr_links; $i++) {
      $links[$i] = $form_state->getValue('link_' . $i, '');
    }
    $this->setConfigurationValue('links', serialize($links));
  }

  public function build() {
    $tagcloud = [];
    $config = $this->getConfiguration();
    $links = unserialize($config['links']);
    $background_url = "";
    $background_image = File::load($config['background_image']);
    if (!is_null($background_image)) {
      $background_url = $background_image->getFileUri();
    }
    $form = \Drupal::formBuilder()->getForm('Drupal\cci_cpnt_search\Form\CciSearchForm');

    foreach ($links AS $link) {
      if ($link['title'] !== ""
      && $link['url'] !== "") {
        $tagcloud[] = [
          'title' => $link['title'],
          'url' => \Drupal::service('path.validator')->getUrlIfValid($link['url'])
        ];
      }
    }

    return [
      '#theme' => 'searchblock',
      '#content' => [
        'form' => $form,
        'background' => $background_url,
        'tagcloud' => $tagcloud
      ],
      '#attached' => [
        'library' => [
          'cci_cpnt_search/search'
        ]
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'background_image' => '',
      'links' => [],
    ];
  }

}