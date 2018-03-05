<?php header('Content-Type: text/html; charset=utf-8'); include("pwverify.php"); ?><!DOCTYPE html>
<html>
  <head>
    <!--
    Copyright 2017-2018 Daniel Troger

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    -->
    <title>Vertretungsplan</title>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="Vertretungsplan" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="ubuntu.css" />
    <link rel="stylesheet" type="text/css" href="add2home.css" />
    <script src="add2home.js"></script>
    <style>
      @font-face
      {
        font-family: 'Waldorf';
        font-style: normal;
        src: url(Waldorf.ttf) format('truetype');
      }
      #container
      {
        border-radius: 20px;
        position: absolute;
        left: 1.8%;
        top: 12%;
        bottom: 12%;
        right: 1.8%;
        overflow-y: auto;
      }
      #container::-webkit-scrollbar-track
      {
        border-radius: 30px;
      	background-color: white;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.4);
      }

      #container::-webkit-scrollbar
      {
      	width: 15px;
        height: 15px;
      }

      #container::-webkit-scrollbar-thumb
      {
      	background-color: #111505;
        border-radius: 30px;
      }
      body
      {
        font-family: Waldorf, ubuntu;
        /*background: #9fc253;*/
        background: #FFD35A;
      }
      th, td
      {
        border-left: 1px dashed #6DA6D1;
        padding: 1.2%;
        word-break:break-all;
      }
      th:first-child, td:first-child
      {
        border-left: none;
      }
      table
      {
        border-radius: 20px;
        background-color: #AF0802;
        width: 100%;
        color: white;
        text-align: left;
        font-family: Ubuntu;
      }
      th:first-child{border-radius: 20px 0px 0px 0px;}
      th:last-child{border-radius: 0px 20px 0px 0px;}
      th
      {
        background-color: #9F2716;
        font-weight: bold;
        text-transform: uppercase;
      }
      tr:nth-child(even){background-color: rgba(255,255,255,0.1);}
      @media only screen and (max-device-width: 700px)
      {
        table
        {
          font-size: 0.4em;
        }
      }
      @media only screen and (min-device-width: 701px)
      {
        tr:hover {background-color: #9F2716;}
      }
      #targetdate
      {
        top: 2%;
        left: 2%;
        position: absolute;
      }
      #impressum
      {
        position: absolute;
        right: 2%;
      }
      @font-face
      {
        font-family: 'Gender';
        src: url('gender/gender.eot');
        src: url("gender/gender.woff") format("woff"),
          url("gender/gender.ttf") format("truetype"),
          url("gender/gender.svg#gender") format("svg");
      }
      #back:hover, #impressum:hover
      {
        text-decoration: underline;
      }
      #back, #impressum{color:black;}
      #footer
      {
        position: absolute;
        bottom: 2%;
        font-size: 2vh;
        left: 2%; right: 0;
      }
    </style>
    <script>
      window.onbeforeprint = function(){
        document.getElementById("back").style.display = "none";
        document.getElementById("saveddate").style.display = "none";
        document.getElementById("impressum").style.display = "none";
        document.body.innerHTML += "<style class='printborders'>td{border-top: 1px dashed #6DA6D1;}</style>";
      }
      window.onafterprint = function(){
        document.getElementById("back").style.display = "";
        document.getElementById("saveddate").style.display = "";
        document.getElementById("impressum").style.display = "";
        for(var pb = document.getElementsByClassName("printborders"),i = 0; i<pb.length; i++)
        {
          pb[i].remove();
        }
      }
      var positiontable = window.onload = window.onorientationchange = function(e){
        var targetdate = document.getElementById("targetdate"),
        footer = document.getElementById("footer"),
        cs = document.getElementById("container").style,
        vh = Math.max(document.documentElement.clientHeight, window.innerHeight || 0); // https://stackoverflow.com/a/8876069
        cs.top = (targetdate.getBoundingClientRect().top+targetdate.offsetHeight+(vh*0.02))+"px";
        cs.bottom = (((vh-footer.getBoundingClientRect().bottom)*1.8)+footer.offsetHeight)+"px";
      }
      window.addEventListener("orientationchange",sctop); // scroll to top when rotating device, useful for mobile safari's fullscreen mode
      function sctop()
      {
        var x = setInterval(function(){scrollTo(0,0);positiontable()},5);
        setTimeout(function(){clearInterval(x)},1000);
      }
      window.addEventListener("load",function(){ // on idevices, scrolling and therefore entering/leaving full screen mode, on mobile safari, triggers resize events
        if(!(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream)) // https://stackoverflow.com/a/9039885
        {
          window.onresize = positiontable;
        }
      });
    </script>
  </head>
  <body>
    <?php
    $tage = array("Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag","Sonntag");
    date_default_timezone_set("Europe/Stockholm");
    $flc = file_get_contents(".htvtpdata.json");
    if($flc[1] == '\\')
    {
      $flc = stripslashes($flc);
    }
    $flc_d = json_decode($flc,1);
    ?>
    <?php
    echo "<h1 id='targetdate'>Vertretungsplan " . $flc_d['validdate'] . "</h1>";
    ?>
    <div id="container">
      <table>
        <tr>
          <th>Klasse</th>
          <th>Stunde</th>
          <th>Lehrer<span style="font-family: gender;">G</span></th>
          <th>Fach</th>
          <th>Vertretungslehrer<span style="font-family: gender;">G</span></th>
          <th>Info</th>
        </tr>
      <?php
      foreach($flc_d['data'] as $eintrag)
      {
        ?>  <tr>
    <?php
        foreach($eintrag as $spalte)
        {
          echo "       <td>{$spalte}</td>\n";
        }
        ?>      </tr>
        <?php
      }
      ?>
      </table>
    </div>
    <div id="footer">
      <?php
      echo "<span id='saveddate'>Zuletzt geändert am " . $tage[date("N",$flc_d['date'])-1] . " den " . date("d.m.Y \u\m H:i",$flc_d['date']). "</span>";
      ?>

      <br />
      <a href="/" id="back">Zur&uuml;ck zur Homepage</a>
      <a href="/kontakt/impressum/" id="impressum">Impressum</a>
    </div>
  </body>
</html>
