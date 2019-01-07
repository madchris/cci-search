<?php

namespace Drupal\cci_cpnt_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class CciSearchForm extends FormBase {

  public function getFormId() {
    return "cci_search_form";
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $search_icon = drupal_get_path('module', 'cci_cpnt_search') . '/images/search.svg';
    $form['#action'] = Url::fromRoute('search.view_node_search')->toString();
    $form['#method'] = 'get';

    $form['keys'] = [
      '#type' => 'search',
      '#title' => $this->t('Search'),
      '#title_display' => 'invisible',
      '#size' => 15,
      '#default_value' => '',
      '#attributes' => ['title' => $this->t('Enter the terms you wish to search for.')],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      // Prevent op from showing up in the query string.
      '#name' => '',
      '#suffix' => '<img class="search-icon" src="/'.$search_icon.'" />'
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}