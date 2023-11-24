<?php 

class get_pets {
  function __construct() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'pets';

    $this->args = $this->get_args();
    // $this->placeholders = $this->create_placeholders();

    $query = "SELECT * FROM $tablename ";
    $countQuery = "SELECT COUNT(*) FROM $tablename ";
    
    $query .= $this->create_where_text();
    $countQuery .= $this->create_where_text();
    $query .= " order by 'id' LIMIT 100 ";

    $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->args));
    $this->pets = $wpdb->get_results($wpdb->prepare($query, $this->args));
  }

  function get_args() {
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

    return array_filter($temp, function($value) {
      return $value;
    });

  }

  // function create_placeholders() {
  //   return array_map(function($value) {
  //     return $value;
  //   }, $this->args);
  // }

  function create_where_text() {
    $whereQuery = "";

    if (count($this->args)) {
      $whereQuery = "WHERE ";
    }

    $currentPosition = 0;
    foreach($this->args as $index => $item) {
      $whereQuery .= $this->specific_query($index);
      if ($currentPosition != count($this->args) - 1) {
        $whereQuery .= " AND ";
      }
      $currentPosition++;
    }

    return $whereQuery;
  }

  function specific_query($index) {
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