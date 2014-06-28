<?php 
$base_url = $this->config->item('base_url');
$projectName = $this->config->item('project_name');
?>
    <div role="navigation" class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="<?php echo $base_url;?>" class="navbar-brand"><?=$projectName?></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="<?=(($selected=="downloads")?"active":"")?>"><a href="<?=$base_url."index.php/pages/view/downloads"?>">DOWNLOADS</a></li>
            <li class="<?=(($selected=="aboutus")?"active":"")?>"><a href="<?=$base_url."index.php/pages/view/aboutus"?>">ABOUT US</a></li>
	    <li class="<?=(($selected=="readme")?"active":"")?>"><a href="<?=$base_url."index.php/pages/view/readme"?>">README</a></li>
            <!--<li><a href="#contact">Contact</a></li>-->
            <!--<li class="dropdown">
              <a data-toggle="dropdown" class="dropdown-toggle" href="#">Dropdown <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>-->
          </ul>
          <!--<ul class="nav navbar-nav navbar-right">
            <li><a href="../navbar/">Default</a></li>
            <li class="active"><a href="./">Static top</a></li>
            <li><a href="../navbar-fixed-top/">Fixed top</a></li>
          </ul>-->
        </div><!--/.nav-collapse -->
      </div>
    </div>
