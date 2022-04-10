<?php
           $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
           $randstring = '';
           for ($i = 0; $i < 30; $i++) {
               $randstring .= $characters[rand(0, strlen($characters)-1)];
           }
           echo $randstring;
        
?>