<?php
header('Content-Type: text/html; charset=utf-8');
include("pwverify.php");
$flc = file_get_contents(".htvtpdata.json");
if(isset($_REQUEST['raw'])){header("Content-type: text/html");var_dump($flc);exit;}
if($flc[1] == '\\')
{
  $flc = stripslashes($flc);
}
$flc_d = json_decode($flc,1); ?><!-- based on https://codepen.io/ashblue/pen/mCtuA -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="ubuntu.css" />
  <style>
    body{font-family:ubuntu;}
    @import "compass/css3";

    .table-editable {
      position: relative;

      .glyphicon {
        font-size: 20px;
      }
    }

    .table-remove {
      color: #700;
      cursor: pointer;

      &:hover {
        color: #f00;
      }
    }

    .table-up, .table-down {
      color: #007;
      cursor: pointer;

      &:hover {
        color: #00f;
      }
    }

    .table-add {
      color: #070;
      cursor: pointer;
      position: absolute;
      top: 8px;
      right: 0;

      &:hover {
        color: #0b0;
      }
    }
    #specificerror
    {
      font-family: "Ubuntu Mono";
    }
  </style>
  <script src="sha256.min.js"></script>
  <script src="jquery.min.js"></script>
  <script src="jquery-ui.min.js"></script>
  <script src="bootstrap.min.js"></script>
  <script src="underscore.js"></script>
</head>
<body>
  <div class="container">
  <h1>Fehlzeitenplanbackend</h1>
  <p>So geht's</p>

  <ol>
    <li>Einmal zur Probe auf "Speichern" klicken um zu testen dass kein technischer Fehler vorliegt (ist sonst ärgerlich falls das Speichern danach nicht klappt)</li>
    <li>Gültigkeitsdatum eingeben</li>
    <li>Alte Posten löschen (z.B. "Alles Löschen")</li>
    <li>Über das grüne Plus in der Titelzeile eine neue Zeile hinzufügen</li>
    <li>Daten eingeben</li>
    <li>Über die blauen Pfeile können die Reihen rauf-und runterbewegt werden</li>
    <li>Über die Roten kreuze gelöscht werden</li>
    <li>Um einen Wert zu verändern einfach drauf klicken</li>
  </ol>

<span class="desc">Datum für welches der Plan gilt: </span><input type="date" id="datepicker" value="<?php echo $flc_d['validdate']; ?>" placeholder="Klicken um Datum einzugeben" />
<br />
<button onclick='delall();'>Alles Löschen</button>
<br />

  <div id="table" class="table-editable">
    <span class="table-add glyphicon glyphicon-plus"></span>
    <table class="table">
      <tr>
        <th>Klasse</th>
        <th>Stunde</th>
        <th>Lehrer</th>
        <th>Fach</th>
        <th>Vertretungslehrer</th>
        <th>Info</th>
        <th></th>
        <th></th>
      </tr>
      <?php
      foreach($flc_d['data'] as $eintrag)
      {
        ?>  <tr>
    <?php
        foreach($eintrag as $spalte)
        {
          echo "       <td contenteditable=\"true\">{$spalte}</td>\n";
        }
        ?>
        <td>
          <span class="table-remove glyphicon glyphicon-remove"></span>
        </td>
        <td>
          <span class="table-up glyphicon glyphicon-arrow-up"></span>
          <span class="table-down glyphicon glyphicon-arrow-down"></span>
        </td>
      </tr>
        <?php
      }
      ?>
      <!-- This is our clonable table line -->
      <tr class="hide">
        <td contenteditable="true">00</td>
        <td contenteditable="true">0FS</td>
        <td contenteditable="true">_NAME_</td>
        <td contenteditable="true">_FACH_</td>
        <td contenteditable="true">_VERTRETUNGSLEHRER_</td>
        <td contenteditable="true">_INFO_</td>
        <td>
          <span class="table-remove glyphicon glyphicon-remove"></span>
        </td>
        <td>
          <span class="table-up glyphicon glyphicon-arrow-up"></span>
          <span class="table-down glyphicon glyphicon-arrow-down"></span>
        </td>
      </tr>
    </table>
  </div>

  <button id="export-btn" class="btn btn-primary">Speichern</button>
</div>
<div style="display:none;" id="gespeichert" title="Erfolgreich gespeichert!">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    Der Vertretungsplan wurde erfolgreich gespeichert.
  </p>
</div>
<div style="display:none;" id="notwriteable" title="Datei nicht beschreibbar">
  <p>
    <span class="ui-icon ui-icon-cancel" style="float:left; margin:0 7px 50px 0;"></span>
    Der Vertretungsplan konnte nicht gespeichert werden da die Datei nicht beschrieben werden konnte.
  </p>
</div>
<div style="display:none;" id="passworderror" title="Passwort ist falsch">
  <p>
    <span class="ui-icon ui-icon-cancel" style="float:left; margin:0 7px 50px 0;"></span>
    Der Vertretungsplan konnte nicht gespeichert werden da das Passwort falsch ist. Bitte versuchen sie es erneut.
  </p>
</div>
<div style="display:none;" id="error" title="Fehler">
  <p>
    <span class="ui-icon ui-icon-cancel" style="float:left; margin:0 7px 50px 0;"></span>
    Der Vertretungsplan wurde aus unbekannten gr&uuml;nden nicht gespeichert.
  </p>
  <p id="specificerror"></p>
</div>
  <script>
    var $TABLE = $('#table');
    var $BTN = $('#export-btn');
    var password = "";

    $('.table-add').click(function () {
      var $clone = $TABLE.find('tr.hide').clone(true).removeClass('hide table-line');
      $TABLE.find('table').append($clone);
    });

    $('.table-remove').click(function () {
      $(this).parents('tr').detach();
    });

    $('.table-up').click(function () {
      var $row = $(this).parents('tr');
      if ($row.index() === 1) return; // Don't go above the header
      $row.prev().before($row.get(0));
    });

    $('.table-down').click(function () {
      var $row = $(this).parents('tr');
      $row.next().after($row.get(0));
    });

    // A few jQuery helpers for exporting only
    jQuery.fn.pop = [].pop;
    jQuery.fn.shift = [].shift;

    $BTN.click(function () {
      if(password.length != 64)
      {
        password = sha256(prompt("Passwort?",""));
      }
      if(password == null || password == undefined || password.length != 64)
      {
        return;
      }
      var $rows = $TABLE.find('tr:not(:hidden)');
      var headers = [];
      var data = [];
      // Get the headers (add special header logic here)
      $($rows.shift()).find('th:not(:empty)').each(function () {
        headers.push($(this).text().toLowerCase());
      });

      // Turn all existing rows into a loopable array
      $rows.each(function () {
        var $td = $(this).find('td');
        var h = {};

        // Use the headers from earlier to name our hash keys
        $.each(headers,function(i,header){
          h[header] = $td.eq(i).text();
        });

        data.push(h);
      });
      // Output the result
      var dts = {date: Math.round((new Date()).getTime() / 1000), validdate: $("#datepicker").val(), data: data};
      $.post("save.php", {json: JSON.stringify(dts), password: password}, function(result){
          var ret = JSON.parse(result),
          success = (ret.fail1 == false && ret.fail2 == false && ret.writeable == true);
          if(success)
          {
            $("#gespeichert").css("display","");
            $( "#gespeichert" ).dialog({
            modal: true,
            buttons: {
              Ok: function() {
                $( this ).dialog( "close" );
                $("#gespeichert").css("display","none");
              }
            }
          });
          }
          else if(ret.fail3 == true)
          {
            password = "";
            $("#passworderror").css("display","");
            $( "#passworderror" ).dialog({
            modal: true,
            buttons: {
              Ok: function() {
                $( this ).dialog( "close" );
                $("#passworderror").css("display","none");
                $BTN.click();
              }
            }
          });
          }
          else
          {
            if(ret.writeable == false)
            {
              $("#notwriteable").css("display","");
              $( "#notwriteable" ).dialog({
              modal: true,
              buttons: {
                Ok: function() {
                  $( this ).dialog( "close" );
                  $("#notwriteable").css("display","none");
                }
              }
            });
            }
            else
            {
              $("#error").css("display","");
              $("#specificerror").text("Passwortüberprüfungsfehler: "+ret.fail3+"\nSchreibfunktionsfehler: "+ret.fail1+"\nPrüfsummenüberprüfungsfehler: "+ret.fail2+"\nBeschreibbar: "+ret.writeable);
              $( "#error" ).dialog({
              modal: true,
              buttons: {
                Ok: function() {
                  $( this ).dialog( "close" );
                  $("#error").css("display","none");
                }
              }
            });
            }
          }
      });
    });
    function checkDateInput() { // https://stackoverflow.com/a/10199306
    var input = document.createElement('input');
    input.setAttribute('type','date');

    var notADateValue = 'not-a-date';
    input.setAttribute('value', notADateValue);

    return (input.value !== notADateValue);
}
    if (!checkDateInput()) {
    $('input[type=date]').datepicker({
        // Consistent format with the HTML5 picker
        dateFormat: 'dd/mm/yy'
    });
}
function delall()
{
  $(".table-remove.glyphicon.glyphicon-remove").each(function(){
    if(!$(this).parent().parent().hasClass("hide"))
    {
      $(this).click();
    }
  });
}
  </script>
</body>
</html>
