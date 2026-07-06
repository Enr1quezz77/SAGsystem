<?php
$raw_hex = '00000a0000004bf066320001ff000000';
$bin = hex2bin($raw_hex);
$u = unpack('vpad1/vuid/vpad2/Vtime/Cstate/Cverif/Vpad3', $bin);
print_r($u);
?>
