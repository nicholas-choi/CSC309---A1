<?php

class Division
{
  const DIVISIONSIZE = 5;
  public $newDivision = array();

  public function constructDivision($nameArray){
    $divisionCursorStart = 0;

    $nameArraySize = count($nameArray);
    $finishSubDivisionConstruction = false;
    $tempArray = array();
    if ($nameArraySize < self::DIVISIONSIZE){
      $divisionSize = $nameArraySize;
      $divisionCursorEnd = $nameArraySize - 1;
      $finishSubDivisionConstruction = true;
    } else {
      $divisionSize = self::DIVISIONSIZE;
      $divisionCursorEnd = self::DIVISIONSIZE;
    }


    $tempSubDivision = $this->constructSubDivision(array_slice($nameArray, $divisionCursorStart, $divisionSize), $divisionSize);
    $tempArray = array_merge($tempArray, array($tempSubDivision));

    while ($finishSubDivisionConstruction == false){
      if ($divisionCursorStart == 0) {
        $divisionCursorStart = $divisionCursorEnd - 1;
        $divisionCursorEnd = $divisionCursorStart + $divisionSize - 1;
      } else {
        $divisionCursorStart += $divisionSize - 1;
        $divisionCursorEnd += $divisionSize - 1;
      }
      if ($divisionCursorEnd >= ($nameArraySize - 1)){
        $divisionCursorEnd = $nameArraySize - 1;
        $finishSubDivisionConstruction = true;
        $divisionSize = $divisionCursorEnd - $divisionCursorStart + 1;
      }

      $tempSubDivision = $this->constructSubDivision(array_slice($nameArray, $divisionCursorStart, $divisionSize), $divisionSize);
      $tempArray = array_merge($tempArray, array($tempSubDivision));
    }
    $this->newDivision = $tempArray;
  }

  public function constructSubDivision($subDivisionArray, $arraySize) {

        if ($arraySize == 1) {
            return array();
        }

        if (empty($subDivisionArray)) {
            return array();
        }

        $prefix = array(array_shift($subDivisionArray));

        $result = array();
        foreach ($subDivisionArray as $value) {
            array_push($result, array($prefix[0], $value));
        }

        $result = array_merge($result, $this->constructSubDivision($subDivisionArray, $arraySize--));

        return $result;
    }

  public function getDivisions(){
    return $this->newDivision;
  }
}
?>
