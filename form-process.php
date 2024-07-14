<?php
/**
 * php file part for contact form validate
 *
 * @package Nekaton - Responsive School Template
 */
    $name=$_POST['name'];
    $email=$_POST['email'];
    $number=$_POST['number'];
    $subject=$_POST['subject'];
    $message=$_POST['message'];		
	$from="From: $name $number $subject<$email>\r\nReturn-path: $email";
	$subject="Website SDN 11 Taluak";
	mail("syarhabilabdussalam241104@gmail.com", $subject, $message, $from)

?>
