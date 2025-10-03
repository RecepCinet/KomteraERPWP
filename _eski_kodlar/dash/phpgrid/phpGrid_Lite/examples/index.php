<?php    
use phpCtrl\C_DataGrid;

if (!empty($_GET["file"]))
{
    $f = $_GET["file"];
    
    $f = str_replace(".php","",$f);
    
    // remote file inclusion attempt fix
    if (strtolower($f) == 'index')
        die("File doesn't exist or potential remote file inclusion attempt!");

    if (strpos($f,".")!==false)
        die("File doesn't exist or potential remote file inclusion attempt");
        
    $f = "../examples/$f.php";
    if (!file_exists($f))
        die("File doesn't exist or potential remote file inclusion attempt");

    $code = file_get_contents($f);
    
    // replace db settings with placeholder
    $code = preg_replace('/("hostname"=>")[^"].*"/i','$1HOSTNAME"',$code);
    $code = preg_replace('/("username"=>")[^"].*"/i','$1USERNAME"',$code);
    $code = preg_replace('/("password"=>")[^"]?.*"/i','$1PASSWORD"',$code);
    
    highlight_string($code);
    echo "<br>&nbsp;";
    die;
}    


function filesToArray($dir) 
{
    $result = array();
    $cdir = scandir($dir);
    $ignore_files = ['index.php', 'l', 'save_selected_rows.php', 'save_virtual_column.php', 'save_local_array.php'];
    foreach ($cdir as $key => $value){
       if(in_array(strtolower($value), $ignore_files) || 
          substr($value, 0, 1) === '.' || 
          strpos(strtolower($value), '.csv') > 0 || 
          is_dir($dir . DIRECTORY_SEPARATOR . $value)){
          // do nothing
       } else {
            $result[] = $value;
       }
    }

    return $result;
}
$examples = filesToArray("../examples");
// print_r($examples);

function folderToArray($dir){
  $folders = array();
  $cdir = scandir($dir);
  foreach ($cdir as $key => $value){
      // echo $value . ' : '. is_dir('../css' . DIRECTORY_SEPARATOR . $value) ."\n";
     if(substr($value, 0, 1) !== '.' && is_dir($dir . DIRECTORY_SEPARATOR . $value)){
          $folders[] = $value;
     }
  }

  return $folders;
}

$themes = folderToArray('../css');
// print_r($themes);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Demos</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/font-awesome.min.css">
  <script src="../js/jquery-2.1.4.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>

  <style>
    body{
        padding-top: 50px;
    }
    /* Remove the navbar's default margin-bottom and rounded borders */ 
    .navbar {
        margin-bottom: 0;
        border-radius: 0;
        background: #fff;
        -webkit-box-shadow: 0 0 65px -15px rgba(0,0,0,.75);
        -moz-box-shadow: 0 0 65px -15px rgba(0,0,0,.75);
        box-shadow: 0 0 65px -15px rgba(0,0,0,.75);
        border: 0;
    }
    
    /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
    .row.content {height: 1000px}
    
    /* Set gray background color and 100% height */
    .sidenav {
      padding-top: 0;
      padding-right: 0;
      background-color: #f1f1f1;
      height: 100%;
      overflow-y: scroll;
      overflow-x: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      position: fixed;
      padding-bottom: 100px;
    }

    .sidenav a{
      color: #337BC8;
      font-weight: 500;
    }

    .sidenav a.list-group-item:focus, 
    .sidenav a.list-group-item:hover,
    .sidenav a.list-group-item:active {
      background-color: #377BC8;
      color: white;
    }

    #code{
        background: #f5f5f5;
        border: 1px solid #e3e3e3;
        position: relative;
        margin-top: 20px;
        padding:15px;
        overflow: auto;
    }
    
    /* Set black background color, white text and some padding */
    footer {
      padding: 15px;
    }

    .navbar-inverse{
        background-color: #377BC8;
    }
    .navbar-inverse .navbar-nav>li>a{
        color: white;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height:auto;} 
    }


  .modal.left .modal-dialog,
  .modal.right .modal-dialog {
    position: fixed;
    margin: auto;
    width: 320px;
    height: 100%;
    -webkit-transform: translate3d(0%, 0, 0);
        -ms-transform: translate3d(0%, 0, 0);
         -o-transform: translate3d(0%, 0, 0);
            transform: translate3d(0%, 0, 0);
  }

  .modal.left .modal-content,
  .modal.right .modal-content {
    height: 100%;
    overflow-y: auto;
  }
  
  .modal.left .modal-body,
  .modal.right .modal-body {
    padding: 15px 15px 80px;
  }

/*Left*/
  .modal.left.fade .modal-dialog{
    left: -320px;
    -webkit-transition: opacity 0.3s linear, left 0.3s ease-out;
       -moz-transition: opacity 0.3s linear, left 0.3s ease-out;
         -o-transition: opacity 0.3s linear, left 0.3s ease-out;
            transition: opacity 0.3s linear, left 0.3s ease-out;
  }
  
  .modal.left.fade.in .modal-dialog{
    left: 0;
  }
        
/*Right*/
  .modal.right.fade .modal-dialog {
    right: -320px;
    -webkit-transition: opacity 0.3s linear, right 0.3s ease-out;
       -moz-transition: opacity 0.3s linear, right 0.3s ease-out;
         -o-transition: opacity 0.3s linear, right 0.3s ease-out;
            transition: opacity 0.3s linear, right 0.3s ease-out;
  }
  
  .modal.right.fade.in .modal-dialog {
    right: 0;
  }

/* ----- MODAL STYLE ----- */
  .modal-content {
    border-radius: 0;
    border: none;
  }

  .modal-header {
    border-bottom-color: #EEEEEE;
    background-color: #FAFAFA;
  }

  #themeName.list-group a{
    line-height: .8;
    font-size:13px;
  }

</style>

</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="https://phpgrid.com"><img src="https://phpgrid.com/wp-content/uploads/2015/03/phpgrid-logo-w-slogan.png" style="width:120px"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Demo</a></li>
      </ul>
    </div>
  </div>
</nav>
  
<div class="container-fluid text-left">    
  <div class="row content">
    <div class="col-md-2 sidenav list-group">
      <?php 
        $isIndex = strtolower(basename(__FILE__, '.php')) == 'index';

        foreach($examples as $k=>$v) {

            // url_hash_name is used to add hash to URL

            $fname = $url_hash_name = str_replace(".php","",$v);
            $fname = str_replace("_"," ",$fname);
            $fname = str_replace("-"," ",$fname);
            $fname = str_replace('Phpgrid', 'phpGrid', ucwords($fname));

            if (trim($fname) == 'Wysiwyg') $fname = 'WYSIWYG Editor';

            $active = (($isIndex && ($v == 'basic_phpgrid.php')) ? "active" : "");
            echo "<a class='list-group-item ". $active ."' href='$v' onclick=\"
              jQuery('#code').load('index.php?file=$v'); 
              $('#grid-demo-tabs a:first').tab('show');
              window.location.hash = '$url_hash_name';
              $('.sidenav.list-group a').removeClass('active');
              $(this).addClass('active');\" 
              target='demo_frame'> $fname </a>" ."\n";
        }
        ?>
        <br /><br /><br /><br /><br />
    </div>
    
    <div class="col-md-10 col-md-offset-2 text-left" id="main-content"> 
      <br />
      <div id="demo" class="tab-pane fade in active">
        <iframe style="overflow:auto;min-height:600px" onload="iframeLoaded(this)" id="demo_frame" name="demo_frame" frameborder="0" width="100%" height="500" src="basic_phpgrid.php"></iframe>
        </div>
        
        <h2>Source code</h2>

        <div id="code">
        </div>
    </div>

  </div>
</div>

<footer></footer>


<!-- Modal -->
  <div class="modal right fade" id="themeListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel2">Color Themes</h4>
        </div>

        <div class="modal-body">
          <div class="list-group" id="themeName">
            <?php foreach($themes as $t): ?>
              <a class="list-group-item" tn="<?php echo $t; ?>" href="#"><?php echo ucwords($t) ?></a>
            <?php endforeach; ?>
          </div>
        </div>

      </div><!-- modal-content -->
    </div><!-- modal-dialog -->
  </div><!-- modal -->


<script type="text/javascript">
jQuery(document).ready(function($){
    if (window.location.hash) {
      // trigger link from URL hash e.g.  http://localhost/phpGridx/examples/#auto_scroll
      let demo = window.location.hash.substring(1);
      $("a.list-group-item[href='"+ demo +".php']")[0].click();

      // scroll to that link in left nav
      $('.sidenav').animate({ scrollTop: $("a.list-group-item[href='" + demo + ".php']").offset().top-300 }, 0)

      // need to add hash to URL whenever user click a demo link, probably not in this routine

    } else {
      // load default
      $('#code').load('index.php?file=basic_phpgrid.php');
    }

    $(document).on('click', '#themeName .list-group-item', function(e) {
        $(this).addClass("active").siblings().removeClass("active");
        updateTheme();
    });
});

function iframeLoaded(iframeId,stop) {
    if(iframeId) {
        if(iframeId.contentDocument){
            if (iframeId.contentDocument.body){
                if (iframeId.height != iframeId.contentDocument.body.scrollHeight){
                    iframeId.height = iframeId.contentDocument.body.scrollHeight + 10;
                }
            }
        } else {
            iframeId.height = iframeId.contentWindow.document.body.scrollHeight + 20 + "px";
        }
    }
    
    setTimeout(function(){
        iframeLoaded(iframeId,1);
    },1000);
    
    if (!stop) {
      updateTheme();
    }
}

function updateTheme(){
    var themeName = jQuery('#themeName .list-group-item.active').attr('tn');
    if(!themeName) themeName = 'cobalt-flat'; // default theme

    // replace theme file (note: can't use attri('href') since [0] retruns an object);
    $("iframe#demo_frame").contents().find('#theme-custom-style')[0].href = "../css/"+themeName+"/jquery-ui.css";

    if(themeName == 'bootstrap'){
//      $("iframe[name=demo_frame]").contents().find('body').text().replace('guiStyle:"jQueryUI"', 'guiStyle:"bootstrap"');
    } else {
//      $("iframe[name=demo_frame]").contents().find('body').text().replace('guiStyle:"bootstrap"', 'guiStyle:"jQueryUI"');
    }
}

(function(){
  updateTheme();
})

</script>


</body>
</html>
