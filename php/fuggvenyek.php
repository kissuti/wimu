<?php

function szampontos($szamot) {
  if (strlen($szamot)<4) {
    print $szamot;
  }
  elseif (strlen($szamot)==4) {
    $elso = substr($szamot,0,1);
    $masodik = substr($szamot,1,3);
    print $elso . "." . $masodik; 
  }
  elseif (strlen($szamot)==5) {
    $elso = substr($szamot,0,2);
    $masodik = substr($szamot,2,3);
    print $elso . "." . $masodik; 
  }
  elseif (strlen($szamot)==6) {
    $elso = substr($szamot,0,3);
    $masodik = substr($szamot,3,3);
    print $elso . "." . $masodik; 
  }
  elseif (strlen($szamot)==7) {
    $elso = substr($szamot,0,1);
    $masodik = substr($szamot,1,3);
    $harmadik = substr($szamot,4,3);
    print $elso . "." . $masodik . "." . $harmadik; 
  }
}

?>