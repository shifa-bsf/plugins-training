<?php 

class GetPets {
  function __construct() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'pets';

    $this->args = $this->getArgs();
    $this->placeholders = $this->createPlaceholders();

    $query = "SELECT * FROM $tablename ";
    $countQuery = "SELECT COUNT(*) FROM $tablename ";
    $query .= $this->createWhereText();
    $countQuery .= $this->createWhereText();
    $query .= " LIMIT 100";

    $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->placeholders));
    $this->pets = $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
  }

  function getArgs() {
    $temp = array(
        'favcolor' => isset($_GET['favcolor']) ? sanitize_text_field($_GET['favcolor']) : null,
        'species' => isset($_GET['species']) ? sanitize_text_field($_GET['species']) : null,
        'minyear' => isset($_GET['minyear']) ? absint($_GET['minyear']) : null,
        'maxyear' => isset($_GET['maxyear']) ? absint($_GET['maxyear']) : null,
        'minweight' => isset($_GET['minweight']) ? absint($_GET['minweight']) : null,
        'maxweight' => isset($_GET['maxweight']) ? absint($_GET['maxweight']) : null,
        'favhobby' => isset($_GET['favhobby']) ? sanitize_text_field($_GET['favhobby']) : null,
        'favfood' => isset($_GET['favfood']) ? sanitize_text_field($_GET['favfood']) : null,
    );

    return array_filter($temp, function($x) {
      return $x;
    });

  }

  function createPlaceholders() {
    return array_map(function($x) {
      return $x;
    }, $this->args);
  }

  function createWhereText() {
    $whereQuery = "";

    if (count($this->args)) {
      $whereQuery = "WHERE ";
    }

    $currentPosition = 0;
    foreach($this->args as $index => $item) {
      $whereQuery .= $this->specificQuery($index);
      if ($currentPosition != count($this->args) - 1) {
        $whereQuery .= " AND ";
      }
      $currentPosition++;
    }

    return $whereQuery;
  }

  function specificQuery($index) {
    switch ($index) {
      case "minweight":
        return "petweight >= %d";
      case "maxweight":
        return "petweight <= %d";
      case "minyear":
        return "birthyear >= %d";
      case "maxyear":
        return "birthyear <= %d";
      default:
        return $index . " = %s";
    }
  }

}