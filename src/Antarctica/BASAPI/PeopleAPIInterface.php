<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18/01/2015
 * Time: 18:33
 */

namespace Antarctica\BASAPI;


interface PeopleAPIInterface {

    function getPerson($reference);

    function getEveryone();

}
