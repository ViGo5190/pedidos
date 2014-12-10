<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */


function securePreventFrame(){
    header('X-Frame-Options: DENY');
}