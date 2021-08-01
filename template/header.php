<?php
/*
This file is part of McWebPanel.
Copyright (C) 2020 Cristina Ibañez, Konata400

    McWebPanel is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    McWebPanel is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with McWebPanel.  If not, see <https://www.gnu.org/licenses/>.
*/
header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self'; script-src 'self'; form-action 'self'; base-uri 'none'; connect-src 'self'; frame-ancestors 'none'");
header('X-Content-Type-Options: nosniff'); 
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header('Permissions-Policy: geolocation=(), microphone=()');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex, nofollow">
  <meta name="description" content="McWebPanel">
  <meta name="author" content="Konata400">
  <title>McWebPanel</title>

  <!-- Menu CSS -->
  <link rel="stylesheet" href="css/menu.css">

  <!-- Script AJAX -->
  <script src="js/jquery.min.js"></script>

  <!-- Favicons -->
  <link rel="apple-touch-icon" href="img/icons/apple-icon-180x180.png" sizes="180x180">
  <link rel="icon" href="img/icons/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="img/icons/favicon-16x16.png" sizes="16x16" type="image/png">
  <link rel="icon" href="img/icons/favicon.ico">